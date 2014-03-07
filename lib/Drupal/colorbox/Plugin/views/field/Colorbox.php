<?php

/**
 * @file
 * Definition of Drupal\colorbox\Plugin\views\field\Colorbox.
 */

namespace Drupal\colorbox\Plugin\views\field;

use Drupal\Component\Annotation\PluginID;
use Drupal\Core\Annotation\Translation;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("colorbox")
 */
class Colorbox extends FieldPluginBase {
  function query() {
    // Do nothing, as this handler does not need to do anything to the query itself.
  }

  function defineOptions() {
    $options = parent::defineOptions();

    $options['trigger_field'] = array('default' => '');
    $options['popup'] = array('default' => '');
    $options['caption'] = array('default' => '');
    $options['gid'] = array('default' => TRUE);
    $options['custom_gid'] = array('default' => '');
    $options['width'] = array('default' => '600px');
    $options['height'] = array('default' => '400px');

    return $options;
  }

  function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);

    // Get a list of the available fields and arguments for trigger field and token replacement.
    $options = array();
    $fields = array('trigger_field' => t('- None -'));
    foreach ($this->view->display_handler->getHandlers('field') as $field => $handler) {
      $options[t('Fields')]["[$field]"] = $handler->adminLabel();
      // We only use fields up to (and including) this one.
      if ($field == $this->options['id']) {
        break;
      }

      $fields[$field] = $handler->definition['title'];
    }
    $count = 0; // This lets us prepare the key as we want it printed.
    foreach ($this->view->display_handler->getHandlers('argument') as $arg => $handler) {
      $options[t('Arguments')]['%' . ++$count] = t('@argument title', array('@argument' => $handler->adminLabel()));
      $options[t('Arguments')]['!' . $count] = t('@argument input', array('@argument' => $handler->adminLabel()));
    }

    $this->documentSelfTokens($options[t('Fields')]);

    // Default text.
    $patterns = t('<p>You must add some additional fields to this display before using this field. These fields may be marked as <em>Exclude from display</em> if you prefer. Note that due to rendering order, you cannot use fields that come after this field; if you need a field not listed here, rearrange your fields.</p>');
    // We have some options, so make a list.
    if (!empty($options)) {
      $patterns = t('<p>The following tokens are available for this field. Note that due to rendering order, you cannot use fields that come after this field; if you need a field not listed here, rearrange your fields.
If you would like to have the characters %5B and %5D please use the html entity codes \'%5B\' or  \'%5D\' or they will get replaced with empty space.</p>');
      foreach (array_keys($options) as $type) {
        if (!empty($options[$type])) {
          $items = array();
          foreach ($options[$type] as $key => $value) {
            $items[] = $key . ' == ' . $value;
          }
          $patterns .= theme('item_list',
            array(
              'items' => $items,
              'type' => $type
            ));
        }
      }
    }

    $form['trigger_field'] = array(
      '#type' => 'select',
      '#title' => t('Trigger field'),
      '#description' => t('Select the field that should be turned into the trigger for the Colorbox.  Only fields that appear before this one in the field list may be used.'),
      '#options' => $fields,
      '#default_value' => $this->options['trigger_field'],
      '#weight' => -12,
    );

    $form['popup'] = array(
      '#type' => 'textarea',
      '#title' => t('Popup'),
      '#description' => t('The Colorbox popup content. You may include HTML. You may enter data from this view as per the "Replacement patterns" below.'),
      '#default_value' => $this->options['popup'],
      '#weight' => -11,
    );

    $form['caption'] = array(
      '#type' => 'textfield',
      '#title' => t('Caption'),
      '#description' => t('The Colorbox Caption. You may include HTML. You may enter data from this view as per the "Replacement patterns" below.'),
      '#default_value' => $this->options['caption'],
      '#weight' => -10,
    );

    $form['gid'] = array(
      '#type' => 'checkbox',
      '#title' => t('Automatic generated Colorbox gallery'),
      '#description' => t('Enable Colorbox gallery using a generated gallery id for this view.'),
      '#default_value' => $this->options['gid'],
      '#weight' => -9,
    );

    $form['custom_gid'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom Colorbox gallery'),
      '#description' => t('Enable Colorbox gallery with a given string as gallery. Overrides the automatically generated gallery id above. You may enter data from this view as per the "Replacement patterns" below.'),
      '#default_value' => $this->options['custom_gid'],
      '#weight' => -8,
    );

    $form['width'] = array(
      '#type' => 'textfield',
      '#title' => t('Width'),
      '#description' => t('Specify the width of the Colorbox popup window. Because the content is dynamic, we cannot detect this value automatically. Example: "100%", 500, "500px".'),
      '#default_value' => $this->options['width'],
      '#weight' => -6,
    );

    $form['height'] = array(
      '#type' => 'textfield',
      '#title' => t('Height'),
      '#description' => t('Specify the height of the Colorbox popup window. Because the content is dynamic, we cannot detect this value automatically. Example: "100%", 500, "500px".'),
      '#default_value' => $this->options['height'],
      '#weight' => -7,
    );

    $form['patterns'] = array(
      '#type' => 'fieldset',
      '#title' => t('Replacement patterns'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#value' => $patterns,
    );
  }

  /**
   * Render the trigger field and its linked popup information.
   */
  function render(ResultRow $values) {
    $config = config('colorbox.settings');
    // Load the necessary js file for Colorbox activation.
    if (_colorbox_active() && !$config->get('extra.inline')) {
      $conf_js = array(
        '#attached' => array(
          'js' => array(
            drupal_get_path('module', 'colorbox') . '/js/colorbox_inline.js' => array(),
          ),
        ),
      );
      drupal_render($conf_js);
    }

    // We need to have multiple unique IDs, one for each record.
    static $i = 0;
    $i = mt_rand();

    // Return nothing if no trigger filed is selected.
    if (empty($this->options['trigger_field'])) {
      return;
    }

    // Get the token information and generate the value for the popup and the
    // caption.
    $tokens = $this->getRenderTokens($this->options['alter']);
    $popup = filter_xss_admin($this->options['popup']);
    $caption = filter_xss_admin($this->options['caption']);
    $gallery = filter_xss_admin($this->options['custom_gid']);
    $popup = strtr($popup, $tokens);
    $caption = strtr($caption, $tokens);
    $gallery = strtr($gallery, $tokens);

    $width = $this->options['width'] ? $this->options['width'] : '';
    $height = $this->options['height'] ? $this->options['height'] : '';
    $gallery_id = !empty($this->options['custom_gid']) ? $gallery : ($this->options['gid'] ? 'gallery-' . $this->view->storage->id : '');
    $link_text = $tokens["[{$this->options['trigger_field']}]"];
    $link_options = array(
      'html' => TRUE,
      'fragment' => 'colorbox-inline-' . $i,
      'query' => array(
        'width' => $width,
        'height' => $height,
        'title' => $caption,
        'inline' => 'true'
      ),
      'attributes' => array(
        'class' => array('colorbox-inline'),
        'rel' => $gallery_id
      )
    );
    // Remove any parameters that aren't set.
    $link_options['query'] = array_filter($link_options['query']);

    // If the nid is present make the link degrade to the node page if
    // JavaScript is off.
    $link_target = isset($values->nid) ? 'node/' . $values->nid : '';
    $link_tag = l($link_text, $link_target, $link_options);

    // The outside div is there to hide all of the divs because if the specific Colorbox
    // div is hidden it won't show up as a Colorbox.
    return $link_tag . '<div style="display: none;"><div id="colorbox-inline-' . $i . '">' . $popup . '</div></div>';
  }
}
