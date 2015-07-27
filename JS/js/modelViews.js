//gist model view
var GistView = function(options) {
  this.model = options.model;
  this.collectionView = options.collectionView;
  this.element = null;
};

GistView.prototype = {
  render: function(node) {
    //top-level rendering method for gists
    this.element = document.createElement('li');
    this.addTitle();
    this.addID();
    this.addByline();
    this.addLanguages();
    if (this.model.isFavorite) {
      this.element.className = 'favorite-gist';
      this.addUnfavoriteButton();
    } else {
      this.element.className = 'gist';
      this.addFavoriteButton();
    }
    return this.element;
  },
  addTitle: function() {
    //build title
    var title = document.createElement('h3');
    title.className = 'title';
    var titleAnchor = document.createElement('a');
    titleAnchor.setAttribute('href', this.model.url);
    if (this.model.title.length > 90) {
      titleAnchor.innerHTML = this.model.title.substr(0, 87) + '...';
    } else {
      titleAnchor.innerHTML = this.model.title;
    }
    // titleAnchor.innerHTML = this.model.title;
    title.appendChild(titleAnchor);
    this.element.appendChild(title);
  },
  addID: function() {
    //build id line
    var idLine = document.createElement('p');
    idLine.innerHTML = 'id: ' + this.model.id;
    idLine.className = 'id-line';
    this.element.appendChild(idLine);
  },
  addByline: function() {
    //build byline
    if (this.model.author) {
      var byline = document.createElement('p');
      byline.innerHTML = 'Posted by ' + this.model.author;
      byline.className = 'byline';
      this.element.appendChild(byline);
    }
  },
  addLanguages: function() {
    //build language list
    if (Object.keys(this.model.languages).length) {
      var languageContainer = document.createElement('div');
      var languageHeaderContainer = document.createElement('div');
      var languageHeader = document.createElement('h4');
      languageHeader.innerHTML = 'Languages:';
      languageHeader.className = 'language-header';
      languageHeaderContainer.appendChild(languageHeader);
      languageContainer.appendChild(languageHeaderContainer);
      var languageList = document.createElement('ul');
      languageList.className = 'language-list';

      for (var language in this.model.languages) {
        var languageItem = document.createElement('li');
        languageItem.innerHTML = language;
        languageList.appendChild(languageItem);
      }
      languageContainer.appendChild(languageList);
      this.element.appendChild(languageContainer);
    }
  },
  addFavoriteButton: function() {
    //add favorite button and attach event listener
    var button = this.createButton('Add to Favorites', 'add-favorite');
    button.addEventListener('click', this.toggleFavorite.bind(this));
    this.element.appendChild(button);
  },
  addUnfavoriteButton: function() {
    //add unfavorite button and attach event listener
    var button = this.createButton('Remove Favorite', 'remove-favorite');
    button.addEventListener('click', this.toggleFavorite.bind(this));
    this.element.appendChild(button);
  },
  toggleFavorite: function(e) {
    //remove element and delegate to collectionView
    this.model.toggleFavorite();
    this.collectionView.toggleFavorite(this.model);
  },
  createButton: function(value, className) {
    //utility method for creating buttons
    var button = document.createElement('button');
    button.className = className;
    button.innerHTML = value;
    return button;
  }
};
