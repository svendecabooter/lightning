<?php

namespace Acquia\LightningExtension\Context;

use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for awaiting various conditions.
 */
class AwaitContext extends DrupalSubContextBase {

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
   *
   * @When I wait for :expression to be true
   * @When I wait :timeout seconds for :expression to be true
   */
  public function awaitExpression($expression, $timeout = 10) {
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
   *
   * @When I wait for the :selector element to exist
   * @When I wait :timeout seconds for the :selector element to exist
   */
  public function awaitElement($selector, $timeout = 10) {
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
  public function awaitFrame($frame, $timeout = 10) {
    $this->awaitExpression('window.frames["' . $frame . '"]', $timeout);
  }

  /**
   * Makes PHP twiddle its thumbs for a certain amount of time.
   *
   * @param int $timeout
   *   How long to stall, in seconds.
   *
   * @When I wait :timeout second(s)
   */
  public function sleep($timeout) {
    sleep($timeout);
  }

}
