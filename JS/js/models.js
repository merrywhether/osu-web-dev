//gist model
var Gist = function(options) {
  //comprehensive constructor
  this.id = options.id;
  if (options.title) {
    //remove malicious viagra links in titles
    this.title = options.title.replace(/<a.*<\/a>/, '');
  } else {
    this.title = 'Untitled Gist';
  }
  this.url = options.url;
  this.author = options.author;
  this.languages = options.languages;
  this.isFavorite = options.isFavorite || false;
};

Gist.prototype = {
  toggleFavorite: function() {
    this.isFavorite = !this.isFavorite;
  }
};
