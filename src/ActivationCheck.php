<?php

/**
 * @file
 * Contains Drupal\colorbox\ActivationCheck.
 */

namespace Drupal\colorbox;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Implementation of ActivationCheckInterface.
 */
class ActivationCheck implements ActivationCheckInterface {

  /**
   * The colorbox settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $settings;

  /**
   * Create an instace of ActivationCheck.
   */
  public function __construct(ConfigFactoryInterface $config) {
    $this->settings = $config->get('colorbox.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
  }

}
