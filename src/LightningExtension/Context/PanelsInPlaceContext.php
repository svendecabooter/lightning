<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\BlockManipulationInterface;
use Acquia\LightningExtension\ElementManipulationTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains step definitions for interacting with Panels IPE.
 */
class PanelsInPlaceContext extends DrupalSubContextBase implements BlockManipulationInterface {

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
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function removeBlock($label, $region) {
    throw new \Exception('Not supported...yet.');
  }

}
