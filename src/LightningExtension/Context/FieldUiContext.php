<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\DrupalApiTrait;
use Drupal\Core\Url;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains steps for interacting with the Field UI.
 */
class FieldUiContext extends DrupalSubContextBase {

  use DrupalApiTrait;

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
    $this->minkContext->checkOption('display_modes_custom[' . $view_mode . ']');
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
    $this->minkContext->uncheckOption('display_modes_custom[' . $view_mode . ']');
    $this->minkContext->pressButton('Save');
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
