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

}
