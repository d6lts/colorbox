// $Id$
(function ($) {

Drupal.behaviors.initColorbox = function (context) {
  $('a, area, input', context)
    .filter('.colorbox:not(.initColorbox-processed)')
    .addClass('initColorbox-processed')
    .colorbox(Drupal.settings.colorbox);
};

{
  $(document).bind('cbox_complete', function () {
    Drupal.attachBehaviors('#cboxLoadedContent');
  });
}

})(jQuery);
