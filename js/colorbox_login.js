(function ($) {

Drupal.behaviors.initColorboxLogin = function (context) {
  if (!$.isFunction($.colorbox)) {
    return;
  }
  var settings = Drupal.settings.colorbox;
  $.urlParam = function(name, url){
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
    if (!results) { return ''; }
    return results[1] || '';
  };
  $("a[href*='/user/login'], a[href*='?q=user/login']", context)
    .filter(function () {
      var href = Drupal.absoluteUrl(this.href),
          q = $.urlParam('q', href);
      if (q != '') {
        q = '/' + q;
      }
      return Drupal.urlIsLocal(href) && href.indexOf(settings.file_directory_path) === -1 && href.indexOf('/system/files/') === -1 && q.indexOf('/system/files/') === -1;
    })
    .each(function () {
      if (this.hasAttribute('title')) {
        this.setAttribute('title', Drupal.checkPlain(this.getAttribute('title')));
      }
    }).colorbox({
    initialWidth:200,
    initialHeight:200,
    onComplete:function () {
      $('#edit-name').focus();
    }
  }).each(function () {
      var path = this.href;
      var new_path = path.replace(/user\/login/,'user/login/colorbox')
      var addquery = (path.indexOf('?') !=-1) ? '&' : '?';

      // If no destination, add one to the current page.
      if (path.indexOf('destination') !=-1) {
        this.href = new_path;
      }
      else {
        this.href = new_path + addquery + 'destination=' + window.location.pathname.substr(1);
      }
  });
};

})(jQuery);
