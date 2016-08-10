<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\FieldUiTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains steps for interacting with the Field UI.
 */
class FieldUiContext extends DrupalSubContextBase {

  use FieldUiTrait;

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
   * Customizes a view mode.
   *
   * @param string $entity_type
   *   The entity type ID.
   * @param string $view_mode
   *   The view mode ID.
   * @param string $bundle
   *   (optional) The bundle to customize.
   *
   * @Given I have customized the :view_mode view mode of the :bundle :entity_type type
   * @Given I have customized the :view_mode view mode of the :entity_type entity type
   *
   * @When I customize the :view_mode view mode of the :bundle :entity_type type
   * @When I customize the :view_mode view mode of the :entity_type entity type
   */
  public function customize($entity_type, $view_mode, $bundle = NULL) {
    $this->manageDisplay($entity_type, $bundle);
    $this->minkContext->checkOption($view_mode);
    $this->minkContext->pressButton('Save');
  }

  /**
   * Uncustomizes a view mode.
   *
   * @param string $entity_type
   *   The entity type ID.
   * @param string $view_mode
   *   The view mode ID.
   * @param string $bundle
   *   (optional) The bundle to uncustomize.
   *
   * @Given I have uncustomized the :view_mode view mode of the :bundle :entity_type type
   * @Given I have uncustomized the :view_mode view mode of the :entity_type entity type
   *
   * @When I uncustomize the :view_mode view mode of the :bundle :entity_type type
   * @When I uncustomize the :view_mode view of the :entity_type entity type
   */
  public function uncustomize($entity_type, $view_mode, $bundle = NULL) {
    $this->manageDisplay($entity_type, $bundle);
    $this->minkContext->uncheckOption($view_mode);
    $this->minkContext->pressButton('Save');
  }

}
