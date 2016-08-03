<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\ElementManipulationTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for working with nodes.
 */
class NodeContext extends DrupalSubContextBase {

  use ElementManipulationTrait;

  /**
   * Visits the current revision of a node.
   *
   * @When I visit the current revision
   */
  public function visitCurrentNodeRevision() {
    $this->visitRevisionHistory();
    $this->clickSelector('main table tr td.revision-current:first-child a');
  }

  /**
   * Visits a specific revision of a node.
   *
   * @param int $n
   *   The one-based index of the revision.
   *
   * @When /^I visit the (\d+)(?:st|nd|rd|th) revision$/
   */
  public function visitNthRevision($n) {
    $this->visitRevisionHistory();
    $this->clickSelector('main table tr:nth-child(' . $n . ') td:first-child a');
  }

  /**
   * Visits a node's revision history.
   */
  protected function visitRevisionHistory() {
    $this->getSession()
      ->getPage()
      ->clickLink('Revisions');
  }

}
