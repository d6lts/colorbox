(function ($) {

Drupal.behaviors.initColorboxLoad = function (context) {
  if (!$.isFunction($.colorbox)) {
    return;
  }
  var settings = Drupal.settings.colorbox;
  $.urlParam = function(name, url){
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
    if (!results) { return ''; }
    return results[1] || '';
  };
  $('a, area, input', context).filter('.colorbox-load:not(.initColorboxLoad-processed)').addClass('initColorboxLoad-processed')
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
    transition:settings.transition,
    speed:settings.speed,
    opacity:settings.opacity,
    slideshowAuto:settings.slideshowAuto,
    slideshowSpeed:settings.slideshowSpeed,
    slideshowStart:settings.slideshowStart,
    slideshowStop:settings.slideshowStop,
    current:settings.current,
    previous:settings.previous,
    next:settings.next,
    close:settings.close,
    overlayClose:settings.overlayClose,
    maxWidth:settings.maxWidth,
    maxHeight:settings.maxHeight,
    innerWidth:function(){
      return $.urlParam('width', $(this).attr('href'));
    },
    innerHeight:function(){
      return $.urlParam('height', $(this).attr('href'));
    },
    iframe:function(){
      return $.urlParam('iframe', $(this).attr('href'));
    },
    slideshow:function(){
      return $.urlParam('slideshow', $(this).attr('href'));
    }
  });
};

})(jQuery);
