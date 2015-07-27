//gist collection view
var GistsView = function(options) {
  this.collection = options.collection;
  this.element = document.getElementById(options.elementID);
  this.listID = options.listID;
  this.emptyText = options.emptyText;
  this.errorText = options.errorText;
  this.app = options.app;
};

GistsView.prototype = {
  //add gist list or "no results" to DOM
  render: function() {
    var newResults;
    if (this.collection.getCount()) {
      newResults = document.createElement('ul');
      this.collection.models.forEach(function(model) {
        var view = new GistView({
          model: model,
          collectionView: this
        });
        newResults.appendChild(view.render());
      }, this);
    } else {
      newResults = document.createElement('p');
      if (this.collection.error) {
        newResults.innerHTML = this.errorText;
      } else {
        newResults.innerHTML = this.emptyText;
      }
    }
    newResults.id = this.listID;
    this.element.replaceChild(newResults,
                              document.getElementById(this.listID));
  },
  toggleFavorite: function(model) {
    this.collection.remove(model);
    app.toggleFavorite(model);
  }
};
