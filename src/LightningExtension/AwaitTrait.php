<?php

namespace Acquia\LightningExtension;

/**
 * Contains helper methods for awaiting various conditions.
 */
trait AwaitTrait {

  /**
   * Waits for a JavaScript expression to be true.
   *
   * @param string $expression
   *   The JavaScript expression.
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   *
   * @throws \Exception
   *   If the expression times out.
   */
  protected function awaitExpression($expression, $timeout = 10) {
    $done = $this->getSession()->wait($timeout * 1000, $expression);

    if ($done == FALSE) {
      throw new \Exception('JavaScript expression timed out: ' . $expression);
    }
  }

  /**
   * Waits for an element to exist.
   *
   * @param string $selector
   *   The element's CSS selector.
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   */
  protected function awaitElement($selector, $timeout = 10) {
    $this->awaitExpression('document.querySelector("' . addslashes($selector) . '")', $timeout);
  }

  /**
   * Waits for a frame to exist.
   *
   * @param string $frame
   *   The name of the frame.
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   */
  protected function awaitFrame($frame, $timeout = 10) {
    $this->awaitExpression('window.frames["' . $frame . '"]', $timeout);
  }

}
