<?php
/**
 * @file
 * Administrative class form for the colorbox module.
 * Contains \Drupal\colorbox\Form\ColorboxSettingsForm.
 */

namespace Drupal\colorbox\Form;

use Drupal\system\SystemConfigFormBase;
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * General configuration form for controlling the colorbox behaviour..
 */
class ColorboxSettingsForm extends SystemConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'colorbox_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    
  	// Get all settings
  	$config = $this->configFactory->get('colorbox.settings');

    drupal_add_js(drupal_get_path('module', 'colorbox') . '/js/colorbox_admin_settings.js', array('preprocess' => FALSE));

    // $library = libraries_detect('colorbox');

    if (module_exists('insert')) {
      $form['colorbox_insert_module'] = array(
        '#type' => 'fieldset',
        '#title' => t('Insert module settings'),
      );
      $image_styles = image_style_options(FALSE);
      $form['colorbox_insert_module']['colorbox_image_style'] = array(
        '#type' => 'select',
        '#title' => t('Image style'),
        '#empty_option' => t('None (original image)'),
        '#options' => $image_styles,
        '#default_value' => $config->get('insert.image_style'),
        '#description' => t('Select which image style to use for viewing images in the colorbox.'),
      );
      $form['colorbox_insert_module']['colorbox_insert_gallery'] = array(
        '#type' => 'radios',
        '#title' => t('Insert image gallery'),
        '#default_value' => $config->get('insert.insert_gallery'),
        '#options' => array(0 => t('Per page gallery'), 3 => t('No gallery')),
        '#description' => t('Should the gallery be all images on the page (default) or disabled.'),
      );
    }

    $form['colorbox_extra_features'] = array(
      '#type' => 'fieldset',
      '#title' => t('Extra features'),
    );
    $form['colorbox_extra_features']['colorbox_load'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable Colorbox load'),
      '#default_value' => $config->get('extra.load'),
      '#description' => t('This enables custom links that can open forms and paths in a Colorbox. Add the class "colorbox-load" to the link and build the url like this for paths "[path]?width=500&height=500&iframe=true" or "[path]?width=500&height=500" if you don\'t want an iframe. Other modules may activate this for easy Colorbox integration.'),
    );
    $form['colorbox_extra_features']['colorbox_inline'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable Colorbox inline'),
      '#default_value' => $config->get('extra.inline'),
      '#description' => t('This enables custom links that can open inline content in a Colorbox. Add the class "colorbox-inline" to the link and build the url like this "?width=500&height=500&inline=true#id-of-content". Other modules may activate this for easy Colorbox integration.'),
    );

    $form['colorbox_custom_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Styles and options'),
    );
    $colorbox_styles = array(
      'default' => t('Default'),
      'plain' => t('Plain (mainly for images)'),
      'stockholmsyndrome' => t('Stockholm Syndrome'),
      //$library['library path'] . '/example1' => t('Example 1'),
      //$library['library path'] . '/example2' => t('Example 2'),
      //$library['library path'] . '/example3' => t('Example 3'),
      //$library['library path'] . '/example4' => t('Example 4'),
      //$library['library path'] . '/example5' => t('Example 5'),
      'none' => t('None'),
    );
    $form['colorbox_custom_settings']['colorbox_style'] = array(
      '#type' => 'select',
      '#title' => t('Style'),
      '#options' => $colorbox_styles,
      '#default_value' => $config->get('custom.style'),
      '#description' => t('Select the style to use for the Colorbox. The example styles are the ones that come with the Colorbox plugin. Select "None" if you have added Colorbox styles to your theme.'),
    );
    $form['colorbox_custom_settings']['colorbox_custom_settings_activate'] = array(
      '#type' => 'radios',
      '#title' => t('Options'),
      '#options' => array(0 => t('Default'), 1 => t('Custom')),
      '#default_value' => $config->get('custom.activate'),
      '#description' => t('Use the default or custom options for Colorbox.'),
      '#prefix' => '<div class="colorbox-custom-settings-activate">',
      '#suffix' => '</div>',
    );

    $js_hide = variable_get('colorbox_custom_settings_activate', 0) ? '' : ' js-hide';
    $form['colorbox_custom_settings']['wrapper_start'] = array(
      '#markup' => '<div class="colorbox-custom-settings' . $js_hide . '">',
    );

    $form['colorbox_custom_settings']['colorbox_transition_type'] = array(
      '#type' => 'radios',
      '#title' => t('Transition type'),
      '#options' => array('elastic' => t('Elastic'), 'fade' => t('Fade'), 'none' => t('None')),
      '#default_value' => $config->get('custom.transition_type'),
      '#description' => t('The transition type.'),
    );
    $form['colorbox_custom_settings']['colorbox_transition_speed'] = array(
      '#type' => 'select',
      '#title' => t('Transition speed'),
      '#options' => drupal_map_assoc(array(100, 150, 200, 250, 300, 350, 400, 450, 500, 550, 600)),
      '#default_value' => $config->get('custom.transition_speed'),
      '#description' => t('Sets the speed of the fade and elastic transitions, in milliseconds.'),
    );
    $form['colorbox_custom_settings']['colorbox_opacity'] = array(
      '#type' => 'select',
      '#title' => t('Opacity'),
      '#options' => drupal_map_assoc(array('0', '0.10', '0.15', '0.20', '0.25', '0.30', '0.35', '0.40', '0.45', '0.50', '0.55', '0.60', '0.65', '0.70', '0.75', '0.80', '0.85', '0.90', '0.95', '1')),
      '#default_value' => $config->get('custom.opacity'),
      '#description' => t('The overlay opacity level. Range: 0 to 1.'),
    );
    $form['colorbox_custom_settings']['colorbox_text_current'] = array(
      '#type' => 'textfield',
      '#title' => t('Current'),
      '#default_value' => $config->get('custom.text_current'),
      '#size' => 30,
      '#description' => t('Text format for the content group / gallery count. {current} and {total} are detected and replaced with actual numbers while Colorbox runs.'),
    );
    $form['colorbox_custom_settings']['colorbox_text_previous'] = array(
      '#type' => 'textfield',
      '#title' => t('Previous'),
      '#default_value' => $config->get('custom.text_previous'),
      '#size' => 30,
      '#description' => t('Text for the previous button in a shared relation group.'),
    );
    $form['colorbox_custom_settings']['colorbox_text_next'] = array(
      '#type' => 'textfield',
      '#title' => t('Next'),
      '#default_value' => $config->get('custom.text_next'),
      '#size' => 30,
      '#description' => t('Text for the next button in a shared relation group.'),
    );
    $form['colorbox_custom_settings']['colorbox_text_close'] = array(
      '#type' => 'textfield',
      '#title' => t('Close'),
      '#default_value' => $config->get('custom.text_close'),
      '#size' => 30,
      '#description' => t('Text for the close button. The "Esc" key will also close Colorbox.'),
    );
    $form['colorbox_custom_settings']['colorbox_overlayclose'] = array(
      '#type' => 'checkbox',
      '#title' => t('Overlay close'),
      '#default_value' => $config->get('custom.overlayclose'),
      '#description' => t('Enable closing Colorbox by clicking on the background overlay.'),
    );
    $form['colorbox_custom_settings']['colorbox_maxwidth'] = array(
      '#type' => 'textfield',
      '#title' => t('Max width'),
      '#default_value' => $config->get('custom.maxwidth'),
      '#size' => 30,
      '#description' => t('Set a maximum width for loaded content. Example: "100%", 500, "500px".'),
    );
    $form['colorbox_custom_settings']['colorbox_maxheight'] = array(
      '#type' => 'textfield',
      '#title' => t('Max height'),
      '#default_value' => $config->get('custom.maxheight'),
      '#size' => 30,
      '#description' => t('Set a maximum height for loaded content. Example: "100%", 500, "500px".'),
    );
    $form['colorbox_custom_settings']['colorbox_initialwidth'] = array(
      '#type' => 'textfield',
      '#title' => t('Initial width'),
      '#default_value' => $config->get('custom.initialwidth'),
      '#size' => 30,
      '#description' => t('Set the initial width, prior to any content being loaded. Example: "100%", 500, "500px".'),
    );
    $form['colorbox_custom_settings']['colorbox_initialheight'] = array(
      '#type' => 'textfield',
      '#title' => t('Initial height'),
      '#default_value' => $config->get('custom.initialheight'),
      '#size' => 30,
      '#description' => t('Set the initial height, prior to any content being loaded. Example: "100%", 500, "500px".'),
    );
    $form['colorbox_custom_settings']['colorbox_fixed'] = array(
      '#type' => 'checkbox',
      '#title' => t('Fixed'),
      '#default_value' => $config->get('custom.fixed'),
      '#description' => t('If the Colorbox should be displayed in a fixed position within the visitor\'s viewport or relative to the document.'),
    );

    $form['colorbox_custom_settings']['colorbox_slideshow_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Slideshow settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['colorbox_custom_settings']['colorbox_slideshow_settings']['colorbox_slideshow'] = array(
      '#type' => 'radios',
      '#title' => t('Slideshow'),
      '#options' => array(0 => t('Off'), 1 => t('On')),
      '#default_value' => $config->get('custom.slideshow.slideshow'),
      '#description' => t('An automatic slideshow to a content group / gallery.'),
      '#prefix' => '<div class="colorbox-slideshow-settings-activate">',
      '#suffix' => '</div>',
    );
    $form['colorbox_custom_settings']['colorbox_scrolling'] = array(
      '#type' => 'radios',
      '#title' => t('Scrollbars'),
      '#options' => array(1 => t('On'), 0 => t('Off')),
      '#default_value' => $config->get('custom.scrolling'),
      '#description' => t('If false, Colorbox will hide scrollbars for overflowing content. This could be used on conjunction with the resize method for a smoother transition if you are appending content to an already open instance of Colorbox.'),
    );

    $js_hide = variable_get('colorbox_slideshow', 0) ? '' : ' js-hide';
    $form['colorbox_custom_settings']['colorbox_slideshow_settings']['wrapper_start'] = array(
      '#markup' => '<div class="colorbox-slideshow-settings' . $js_hide . '">',
    );

    $form['colorbox_custom_settings']['colorbox_slideshow_settings']['colorbox_slideshowauto'] = array(
      '#type' => 'checkbox',
      '#title' => t('Slideshow autostart'),
      '#default_value' => $config->get('custom.slideshow.auto'),
      '#description' => t('If the slideshow should automatically start to play.'),
    );
    $form['colorbox_custom_settings']['colorbox_slideshow_settings']['colorbox_slideshowspeed'] = array(
      '#type' => 'select',
      '#title' => t('Slideshow speed'),
      '#options' => drupal_map_assoc(array(1000, 1500, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000)),
      '#default_value' => $config->get('custom.slideshow.speed'),
      '#description' => t('Sets the speed of the slideshow, in milliseconds.'),
    );
    $form['colorbox_custom_settings']['colorbox_slideshow_settings']['colorbox_text_start'] = array(
      '#type' => 'textfield',
      '#title' => t('Start slideshow'),
      '#default_value' => $config->get('custom.slideshow.text_start'),
      '#size' => 30,
      '#description' => t('Text for the slideshow start button.'),
    );
    $form['colorbox_custom_settings']['colorbox_slideshow_settings']['colorbox_text_stop'] = array(
      '#type' => 'textfield',
      '#title' => t('Stop slideshow'),
      '#default_value' => $config->get('custom.slideshow.text_stop'),
      '#size' => 30,
      '#description' => t('Text for the slideshow stop button.'),
    );

    $form['colorbox_custom_settings']['colorbox_slideshow_settings']['wrapper_stop'] = array(
      '#markup' => '</div>',
    );

    $form['colorbox_custom_settings']['wrapper_stop'] = array(
      '#markup' => '</div>',
    );

    $form['colorbox_advanced_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Advanced settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['colorbox_advanced_settings']['colorbox_mobile_detect'] = array(
      '#type' => 'radios',
      '#title' => t('Mobile detection'),
      '#options' => array(1 => t('On'), 0 => t('Off')),
      '#default_value' => $config->get('advanced.mobile_detect'),
      '#description' => t('If on (default) Colorbox will not be active for devices with a the max width set below.'),
    );
    $form['colorbox_advanced_settings']['colorbox_mobile_device_width'] = array(
      '#type' => 'textfield',
      '#title' => t('Device with'),
      '#default_value' => $config->get('advanced.mobile_device_width'),
      '#size' => 30,
      '#description' => t('Set the mobile device max with. Default: 480px.'),
      '#states' => array(
        'visible' => array(
          ':input[name="colorbox_mobile_detect"]' => array('value' => '1'),
        ),
      ),
    );
    $form['colorbox_advanced_settings']['colorbox_caption_trim'] = array(
      '#type' => 'radios',
      '#title' => t('Caption shortening'),
      '#options' => array(0 => t('Default'), 1 => t('Yes')),
      '#default_value' => $config->get('advanced.caption_trim'),
      '#description' => t('If the caption should be made shorter in the Colorbox to avoid layout problems. The default is to shorten for the example styles, they need it, but not for other styles.'),
    );
    $form['colorbox_advanced_settings']['colorbox_caption_trim_length'] = array(
      '#type' => 'select',
      '#title' => t('Caption max length'),
      '#options' => drupal_map_assoc(array(40, 45, 50, 55, 60, 70, 75, 80, 85, 90, 95, 100, 105, 110, 115, 120)),
      '#default_value' => $config->get('advanced.caption_trim_length'),
      '#states' => array(
        'visible' => array(
          ':input[name="colorbox_caption_trim"]' => array('value' => '1'),
        ),
      ),
    );
    $form['colorbox_advanced_settings']['colorbox_visibility'] = array(
      '#type' => 'radios',
      '#title' => t('Show Colorbox on specific pages'),
      '#options' => array(0 => t('All pages except those listed'), 1 => t('Only the listed pages')),
      '#default_value' => $config->get('advanced.visibility'),
    );
    $form['colorbox_advanced_settings']['colorbox_pages'] = array(
      '#type' => 'textarea',
      '#title' => '<span class="element-invisible">' . t('Pages') . '</span>',
      '#default_value' => $config->get('advanced.pages'),
      '#description' => t("Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard. Example paths are %blog for the blog page and %blog-wildcard for every personal blog. %front is the front page.", array('%blog' => 'blog', '%blog-wildcard' => 'blog/*', '%front' => '<front>')),
    );
    $form['colorbox_advanced_settings']['colorbox_compression_type'] = array(
      '#type' => 'radios',
      '#title' => t('Choose Colorbox compression level'),
      '#options' => array(
        'minified' => t('Production (Minified)'),
        'source' => t('Development (Uncompressed Code)'),
      ),
      '#default_value' => $config->get('advanced.compression_type'),
    );

    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {

  	// Get config factory
  	$config = $this->configFactory->get('colorbox.settings');
  	$form_values = $form_state['values'];

    $config
      ->set('extra.load', $form_values['colorbox_load'])
      ->set('extra.inline', $form_values['colorbox_inline'])
      ->set('custom.style', $form_values['colorbox_style'])
      ->set('custom.activate', $form_values['colorbox_custom_settings_activate'])
      ->set('custom.transition_type', $form_values['colorbox_transition_type'])
      ->set('custom.transition_speed', $form_values['colorbox_transition_speed'])
      ->set('custom.opacity', $form_values['colorbox_opacity'])
      ->set('custom.text_current', $form_values['colorbox_text_current'])
      ->set('custom.text_previous', $form_values['colorbox_text_previous'])
      ->set('custom.text_next', $form_values['colorbox_text_next'])
      ->set('custom.text_close', $form_values['colorbox_text_close'])
      ->set('custom.overlayclose', $form_values['colorbox_overlayclose'])
      ->set('custom.maxwidth', $form_values['colorbox_maxwidth'])
      ->set('custom.maxheight', $form_values['colorbox_maxheight'])
      ->set('custom.initialwidth', $form_values['colorbox_initialwidth'])
      ->set('custom.initialheight', $form_values['colorbox_initialheight'])
      ->set('custom.fixed', $form_values['colorbox_fixed'])
      ->set('custom.scrolling', $form_values['colorbox_scrolling'])
      ->set('custom.slideshow.slideshow', $form_values['colorbox_slideshow'])
      ->set('custom.slideshow.auto', $form_values['colorbox_slideshowauto'])
      ->set('custom.slideshow.speed', $form_values['colorbox_slideshowspeed'])
      ->set('custom.slideshow.text_start', $form_values['colorbox_text_start'])
      ->set('custom.slideshow.text_stop', $form_values['colorbox_text_stop'])
      ->set('advanced.mobile_detect', $form_values['colorbox_mobile_detect'])
      ->set('advanced.mobile_detect_width', $form_values['colorbox_mobile_device_width'])
      ->set('advanced.caption_trim', $form_values['colorbox_caption_trim'])
      ->set('advanced.caption_trim_length', $form_values['colorbox_caption_trim_length'])
      ->set('advanced.visibility', $form_values['colorbox_visibility'])
      ->set('advanced.pages', $form_values['colorbox_pages'])
      ->set('advanced.compression_type', $form_values['colorbox_compression_type']);

    if (isset($form_values['colorbox_image_style'])) {
      $config->set('insert.image_style', $form_values['colorbox_image_style']);
    }

    if (isset($form_values['colorbox_insert_gallery'])) {
      $config->set('insert.insert_gallery', $form_values['colorbox_insert_gallery']);
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }

}
