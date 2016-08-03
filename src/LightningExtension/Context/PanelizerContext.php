<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\ElementManipulationTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains steps for interacting with Panelizer and panelized layouts.
 */
class PanelizerContext extends DrupalSubContextBase {

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
   * Saves the current Panels layout as a custom layout.
   *
   * @When I save the layout as a custom override
   */
  public function saveCustomLayout() {
    $this->saveLayout('custom');
  }

  /**
   * Saves the current Panels layout as a default layout.
   *
   * @When I save the layout as the default
   */
  public function saveDefaultLayout() {
    $this->saveLayout('default');
  }

  /**
   * Saves the current Panels layout.
   *
   * @param string $type
   *   How to save the layout. Can be 'default' or 'custom'.
   */
  protected function saveLayout($type) {
    $this->clickSelector('a[title = "Save"]');
    $this->minkContext->iWaitForAjaxToFinish();

    $this->clickSelector('a.panelizer-ipe-save-' . $type);
    $this->minkContext->iWaitForAjaxToFinish();
  }

}
