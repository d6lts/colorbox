<?php

namespace Drupal\colorbox;

/**
 * An interface for attaching things to the built page.
 */
interface PageAttachmentInterface {

  /**
   * Attach information to the page array.
   *
   * @param array $page
   *   The page array.
   */
  public function attach(array &$page);

  /**
   * Check if the attachment should be added to the current page.
   *
   * @return bool
   *   TRUE if the attachment should be added to the current page.
   */
  public function isApplicable();

}
