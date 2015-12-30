<?php

/**
 * @file
 * Contains Drupal\colorbox\ActivationCheck.
 */

namespace Drupal\colorbox;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Path\AliasStorageInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Path\CurrentPathStack;

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
   * A service to match paths.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * The current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * Alias storage.
   *
   * @var \Drupal\Core\Path\AliasStorageInterface
   */
  protected $aliasStorage;

  /**
   * Create an instace of ActivationCheck.
   */
  public function __construct(ConfigFactoryInterface $config, PathMatcherInterface $path_matcher, CurrentPathStack $current_path, AliasStorageInterface $alias_storage) {
    $this->settings = $config->get('colorbox.settings');
    $this->pathMatcher = $path_matcher;
    $this->currentPath = $current_path;
    $this->aliasStorage = $alias_storage;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    // Make it possible deactivate Colorbox with
    // parameter ?colorbox=no in the url.
    if (isset($_GET['colorbox']) && $_GET['colorbox'] == 'no') {
      return FALSE;
    }
    // Convert path to lowercase. This allows comparison of the same path
    // with different case. Ex: /Page, /page, /PAGE.
    $pages = Unicode::strtolower($this->settings->get('advanced.pages'));

    // Compare the lowercase path alias (if any) and internal path.
    $path = $this->currentPath->getPath();
    $path_alias = Unicode::strtolower($this->aliasStorage->lookupPathAlias($path, 'en'));
    $page_match = $this->pathMatcher->matchPath($path_alias, $pages);
    if ($path_alias != $path) {
      $page_match = $page_match || $this->pathMatcher->matchPath($path, $pages);
    }
    $page_match = $this->settings->get('advanced.visibility') == 0 ? !$page_match : $page_match;

    return $page_match;
  }

}
