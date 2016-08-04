<?php

namespace Acquia\LightningExtension;

use Behat\Mink\Exception\UnsupportedDriverActionException;

/**
 * Contains helper methods for interacting with HTML elements.
 */
trait ElementManipulationTrait {

  /**
   * Scrolls an element into the viewport.
   *
   * @param string $selector
   *   The element's CSS selector.
   */
  protected function scrollTo($selector) {
    $js = sprintf('document.querySelector("%s").scrollIntoView()', addslashes($selector));
    $this->getSession()->executeScript($js);
  }

  /**
   * Clicks an element by CSS selector.
   *
   * @param string $selector
   *   The element's CSS selector.
   */
  protected function clickSelector($selector) {
    $element = $this->assertSession()->elementExists('css', $selector);

    try {
      // In certain cases (JavaScript tests), elements are not clickable unless
      // they're visible in the viewport.
      $this->scrollTo($selector);
    }
    catch (UnsupportedDriverActionException $e) {
      // Don't worry about it.
    }
    $element->click();
  }

  /**
   * Clicks an item in a view.
   *
   * This depends on two preprocess functions in lightning_core, which add
   * the data-view-id and data-row-index attributes to views and their rows,
   * respectively.
   *
   * @param string $view_id
   *   The view ID.
   * @param int $index
   *   The zero-based index of the row to click.
   */
  protected function clickViewItem($view_id, $index) {
    $selector = sprintf(
      '[data-view-id="%s"] [data-row-index="%d"]',
      $view_id,
      $index
    );
    $this->clickSelector($selector);
  }

}
