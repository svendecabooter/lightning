<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\DrupalApiTrait;
use Behat\Mink\Exception\ExpectationException;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for dealing with entities generically.
 */
class EntityContext extends DrupalSubContextBase {

  use DrupalApiTrait;

  /**
   * Asserts that the current route is the canonical route for an entity type.
   *
   * @param string $entity_type
   *   The entity type.
   *
   * @throws ExpectationException
   *   If we're not at the canonical route for the given entity type.
   *
   * @Then I should be visiting a(n) :entity_type entity
   */
  public function assertAtCanonicalRoute($entity_type) {
    $this->assertDrupalApi();

    $expected = 'entity.' . $entity_type . '.canonical';
    $actual = $this->getRouteName();

    if ($expected != $actual) {
      throw new ExpectationException(
        'Expected to be at canonical ' . $entity_type . ' route.',
        $this->getSession()->getDriver()
      );
    }
  }

}
