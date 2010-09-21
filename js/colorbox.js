// $Id$
(function ($) {

Drupal.behaviors.initColorbox = function (context) {
  var settings = Drupal.settings.colorbox;
  $('a, area, input', context)
    .filter('.colorbox:not(.initColorbox-processed)')
    .addClass('initColorbox-processed')
    .colorbox(Drupal.settings.colorbox);
};

})(jQuery);
