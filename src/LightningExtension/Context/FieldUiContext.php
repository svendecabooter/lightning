<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\DrupalApiTrait;
use Acquia\LightningExtension\UndoTrait;
use Drupal\Core\Url;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains steps for interacting with the Field UI.
 */
class FieldUiContext extends DrupalSubContextBase {

  use DrupalApiTrait;
  use UndoTrait;

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
   * Post-scenario hook.
   *
   * @AfterScenario
   */
  public function clean() {
    $this->undoAll();
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
   * @param bool $undo
   *   (optional) Whether to automatically undo this operation post-scenario.
   *
   * @Given I have customized the :view_mode view mode of the :bundle :entity_type type
   *
   * @When I customize the :view_mode view mode of the :bundle :entity_type type
   */
  public function customize($entity_type, $view_mode, $bundle = NULL, $undo = TRUE) {
    $this->manageDisplay($entity_type, $bundle);
    $this->minkContext->checkOption('display_modes_custom[' . $view_mode . ']');
    $this->minkContext->pressButton('Save');

    if ($undo) {
      $arguments = func_get_args();
      $arguments[3] = FALSE;
      $this->undo([$this, 'uncustomize'], $arguments);
    }
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
   * @param bool $undo
   *   (optional) Whether to automatically undo this operation post-scenario.
   *
   * @Given I have uncustomized the :view_mode view mode of the :bundle :entity_type type
   *
   * @When I uncustomize the :view_mode view mode of the :bundle :entity_type type
   */
  public function uncustomize($entity_type, $view_mode, $bundle = NULL, $undo = TRUE) {
    $this->manageDisplay($entity_type, $bundle);
    $this->minkContext->uncheckOption('display_modes_custom[' . $view_mode . ']');
    $this->minkContext->pressButton('Save');

    if ($undo) {
      $arguments = func_get_args();
      $arguments[3] = FALSE;
      $this->undo([$this, 'customize'], $arguments);
    }
  }

  /**
   * Visits the Manage Display page for an entity type and bundle.
   *
   * @param string $entity_type
   *   The entity type ID.
   * @param string $bundle
   *   (optional) The bundle ID.
   *
   * @throws \LogicException
   *   If the entity type is not exposed to Field UI.
   */
  protected function manageDisplay($entity_type, $bundle = NULL) {
    $path = $this->getFieldUiPath($entity_type, $bundle);

    if ($path) {
      $this->visitPath($path);
      $this->minkContext->clickLink('Manage display');
    }
    else {
      $value = $entity_type;
      if ($bundle) {
        $value .= '.' . $bundle;
      }
      throw new \LogicException($value . ' is not exposed to Field UI.');
    }
  }

  /**
   * Returns the internal path of an entity type's Field UI base route.
   *
   * @param string $entity_type
   *   The entity type ID.
   * @param string $bundle
   *   (optional) The bundle ID.
   *
   * @return string|null
   *   An internal Drupal path, or NULL if the entity type is not exposed to
   *   Field UI.
   */
  protected function getFieldUiPath($entity_type, $bundle = NULL) {
    $this->assertDrupalApi();

    $definition = \Drupal::entityTypeManager()->getDefinition($entity_type);

    $route = $definition->get('field_ui_base_route');
    if ($route) {
      $parameters = [];
      if ($bundle) {
        $parameters[$definition->getBundleEntityType()] = $bundle;
      }
      return Url::fromRoute($route, $parameters)->getInternalPath();
    }
  }

}
