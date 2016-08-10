<?php

/**
 * @file
 * Contains \PanelsSubContext.
 */

use Acquia\LightningExtension\ElementManipulationTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains step definitions for working with Panels and panelized pages.
 */
class PanelsSubContext extends DrupalSubContextBase {

  use ElementManipulationTrait;

  /**
   * The Mink context.
   *
   * @var MinkContext
   */
  protected $minkContext;

  /**
   * Pre-scenario hook.
   *
   * @BeforeScenario
   */
  public function gatherContexts() {
    $this->minkContext = $this->getContext(MinkContext::class);
  }

  /**
   * Places a block into a Panels layout.
   *
   * @param string $plugin_id
   *   The block plugin ID.
   * @param string $category
   *   The block's category.
   *
   * @When I place the :plugin_id block from the :category category
   */
  public function placeBlock($plugin_id, $category) {
    $this->clickSelector('a[title = "Manage Content"]');
    $this->minkContext->iWaitForAjaxToFinish();

    $this->clickSelector('a[data-category = "' . $category . '"]');
    $this->minkContext->iWaitForAjaxToFinish();

    $this->clickSelector('a[data-plugin-id = "' . $plugin_id . '"]');
    $this->minkContext->iWaitForAjaxToFinish();

    $this->clickSelector('[value = "Add"]');
    $this->minkContext->iWaitForAjaxToFinish();
  }

  /**
   * Asserts that a block with a specific plugin ID is present.
   *
   * @param string $plugin_id
   *   The block plugin ID.
   *
   * @Then I should see a :plugin_id block
   */
  public function assertBlock($plugin_id) {
    $this->assertSession()->elementExists('css', 'div[data-block-plugin-id = "' . $plugin_id . '"]');
  }

  /**
   * Asserts that a block with a specific plugin ID is not present.
   *
   * @param string $plugin_id
   *   The block plugin ID.
   *
   * @Then I should not see a :plugin_id block
   */
  public function assertNotBlock($plugin_id) {
    $this->assertSession()->elementNotExists('css', 'div[data-block-plugin-id = "' . $plugin_id . '"]');
  }

  /**
   * Asserts that a block has contextual links.
   *
   * @param string $plugin_id
   *   The block plugin ID.
   * @param string $link_class
   *   (optional) The class for a specific contextual link to assert.
   *
   * @Then the :plugin_id block should have contextual links
   * @Then the :plugin_id block should have a :link_class contextual link
   * @Then I should see a :plugin_id block with contextual links
   * @Then I should see a :plugin_id block with a :link_class contextual link
   */
  public function assertBlockContextualLinks($plugin_id, $link_class = NULL) {
    $selector = 'div[data-block-plugin-id = "' . $plugin_id . '"] ul.contextual-links';
    if ($link_class) {
      $selector .= ' li.' . $link_class;
    }
    $this->assertSession()->elementExists('css', $selector);
  }

}
