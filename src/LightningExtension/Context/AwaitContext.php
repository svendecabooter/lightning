<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\AwaitTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for awaiting various conditions.
 */
class AwaitContext extends DrupalSubContextBase {

  use AwaitTrait {
    awaitExpression as doAwaitExpression;
    awaitElement as doAwaitElement;
  }

  /**
   * Waits for a JavaScript expression to be truthy.
   *
   * @see AwaitTrait::awaitExpression()
   *
   * @When I wait for :expression to be true
   * @When I wait :timeout seconds for :expression to be true
   */
  public function awaitExpression($expression, $timeout = 10) {
    $this->doAwaitExpression($expression, $timeout);
  }

  /**
   * Waits for an element to exist.
   *
   * @see AwaitTrait::awaitElement()
   *
   * @When I wait for the :selector element to exist
   * @When I wait :timeout seconds for the :selector element to exist
   */
  public function awaitElement($selector, $timeout = 10) {
    $this->doAwaitElement($selector, $timeout);
  }

  /**
   * Waits a while.
   *
   * @param int $n
   *   How long to wait, in seconds.
   *
   * @When I wait :n second(s)
   */
  public function sleep($n) {
    sleep($n);
  }

}
