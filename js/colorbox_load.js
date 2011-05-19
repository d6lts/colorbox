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
  $('a, area, input', context).filter('.colorbox-load:not(.initColorboxLoad-processed)').addClass('initColorboxLoad-processed').colorbox({
    transition:settings.transition,
    speed:settings.speed,
    opacity:settings.opacity,
    slideshow:settings.colorbox.slideshow,
    slideshowAuto:settings.colorbox.slideshowAuto,
    slideshowSpeed:settings.colorbox.slideshowSpeed,
    slideshowStart:settings.colorbox.slideshowStart,
    slideshowStop:settings.colorbox.slideshowStop,
    current:settings.colorbox.current,
    previous:settings.colorbox.previous,
    next:settings.colorbox.next,
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
