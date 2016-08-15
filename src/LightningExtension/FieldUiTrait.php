<?php

namespace Acquia\LightningExtension;

use Drupal\Core\Url;

/**
 * Contains helper methods for interacting with the Field UI.
 */
trait FieldUiTrait {

  use DrupalApiTrait;

  /**
   * Visits the main Field UI page for an entity type and bundle.
   *
   * @param string $entity_type
   *   The entity type ID.
   * @param string $bundle
   *   (optional) The bundle ID.
   * @param string $task
   *   (optional) The task to perform (i.e., the link to click).
   *
   * @throws \LogicException
   *   If the entity type is not exposed to Field UI.
   */
  protected function fieldUi($entity_type, $bundle = NULL, $task = NULL) {
    $path = $this->getFieldUiPath($entity_type, $bundle);

    if ($path) {
      $this->visitPath($path);

      if ($task) {
        $this->getSession()->getPage()->clickLink($task);
      }
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
