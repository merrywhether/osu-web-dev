var GistCollection = function(options) {
  //construct with default settings
  this.model = Gist;
  this.models = [];
  this.error = false;
  //reference to app for callbacks
  this.app = options.app;
};

GistCollection.prototype = {
  getCount: function() {
    return this.models.length;
  },
  remove: function(model) {
    var index = this.models.indexOf(model);
    this.models.splice(index, 1);
    this.save();
  },
  extend: function(options) {
    for (var method in options) {
      this[method] = options[method];
    }
  },
  //noop, to be implemented when needed
  save: function() {
    return;
  }
};

//current gist collection
var CurrentGists = function(options) {
  //call to "super"
  GistCollection.call(this, options);
  //collection-specific settings
  this.python = false;
  this.json = false;
  this.javascript = false;
  this.sql = false;
  this.pages = 1;
};

CurrentGists.prototype = Object.create(GistCollection.prototype);
CurrentGists.prototype.constructor = CurrentGists;
CurrentGists.prototype.extend({
  setPages: function(pages) {
    this.pages = pages;
  },
  setFilter: function(filter, value) {
    this[filter] = value;
  },
  fetch: function() {
    //create xhr requests for each page
    this.models = [];
    var currentPage = 1;
    while (currentPage <= this.pages) {
      var xhr = new XMLHttpRequest();
      xhr.onload = this.processData.bind(this);
      xhr.onerror = this.processError.bind(this);
      var url = 'https://api.github.com/gists?per_page=30&page=' + currentPage;
      xhr.open('GET', url, true);
      xhr.send();
      currentPage++;
    }
  },
  processData: function(e) {
    //non-200 is an error
    if (e.target.status != 200) {
      this.processError();
      return;
    }
    this.error = false;
    var gistResults = JSON.parse(e.target.responseText);
    var favoriteIDs = this.app.getFavoriteIDs();
    //iterate through returned list
    gistResults.forEach(function(gist) {
      //check if already favorited
      //if matches a favorite, return
      if (favoriteIDs.indexOf(gist.id) != -1) {
        return;
      }
      //language filter check, using object like a set
      var languages = {};
      for (var file in gist.files) {
        if (gist.files[file].language) {
          languages[gist.files[file].language] = true;
        }
      }
      //union of selected languages
      //if one+ languages selected and no matches, return
      if ((this.python || this.json || this.javascript || this.sql) &&
          !((this.python && languages.Python) ||
            (this.json && languages.JSON) ||
            (this.javascript && languages.JavaScript) ||
            (this.SQL && languages.SQL))) {
        return;
      }
      //add to current gist list
      this.models.push(new this.model({
        id: gist.id,
        title: gist.description,
        url: gist.html_url,
        author: gist.owner ? gist.owner.login : null,
        languages: languages,
        isFavorite: false
      }));
    }, this);
    this.app.renderCurrentGists();
  },
  processError: function(e) {
    this.error = true;
    this.models = [];
    this.app.renderCurrentGists();
  }
});



var FavoriteGists = function(options) {
  //call to "super"
  GistCollection.call(this, options);
};

FavoriteGists.prototype = Object.create(GistCollection.prototype);
FavoriteGists.prototype.constructor = FavoriteGists;
FavoriteGists.prototype.extend({
  init: function() {
    var favorites = JSON.parse(localStorage.getItem('favorites'));
    if (favorites) {
      favorites.forEach(function(favorite) {
        this.models.push(new this.model(favorite));
      }, this);
    }
  },
  getFavoriteIDs: function() {
    return this.models.map(function(gist) {
      return gist.id;
    });
  },
  add: function(model) {
    this.models.push(model);
    this.save();
  },
  save: function() {
    localStorage.setItem('favorites', JSON.stringify(this.models));
  }
});
