window.onload = function() {
  //create and initialize app
  app = new App();
  app.init();
};

//top-level app
var App = function() {
  //construct with default values
  //using app as global event manager because IE is stupid
  this.currentGists = new CurrentGists({app: this});
  this.currentGistsView = new GistsView({
    collection: this.currentGists,
    elementID: 'currentGistsContainer',
    listID: 'currentGists',
    emptyText: 'No results found matching filters.',
    errorText: 'There was an error with the GitHub request. Please try again.',
    app: this
  });
  this.favoriteGists = new FavoriteGists({app: this});
  this.favoriteGistsView = new GistsView({
    collection: this.favoriteGists,
    elementID: 'favoriteGistsContainer',
    listID: 'favoriteGists',
    emptyText: 'No gists have been selected as favorites.',
    errorText: 'There was an error using local storage.',
    app: this
  });
};

App.prototype = {
  init: function() {
    //register event listeners
    ['python', 'json', 'javascript', 'sql'].forEach(function(id) {
      document.getElementById(id).addEventListener('click',
        this.filterMonitor.bind(this));
    }, this);
    document.getElementById('pages').addEventListener('click',
        this.pagesMonitor.bind(this));
    document.getElementById('search').addEventListener('click',
        this.updateCurrentGists.bind(this));
    //render favorites
    this.favoriteGists.init();
    this.favoriteGistsView.render();
  },
  filterMonitor: function(e) {
    this.currentGists.setFilter(e.target.name, e.target.checked);
  },
  pagesMonitor: function(e) {
    this.currentGists.setPages(parseInt(e.target.value));
  },
  updateCurrentGists: function(e) {
    this.currentGists.fetch();
  },
  getFavoriteIDs: function() {
    return this.favoriteGists.getFavoriteIDs();
  },
  renderCurrentGists: function() {
    this.currentGistsView.render();
  },
  toggleFavorite: function(model) {
    if (model.isFavorite) {
      this.favoriteGists.add(model);
      this.currentGistsView.render();
    }
    this.favoriteGistsView.render();
  }
};
