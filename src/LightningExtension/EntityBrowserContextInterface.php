<?php

namespace Acquia\LightningExtension;

/**
 * Interface for contexts that interact with entity browsers.
 */
interface EntityBrowserContextInterface {

  /**
   * Switches into an entity browser context.
   *
   * @param string $id
   *   (optional) The entity browser ID.
   */
  public function enter($id = NULL);

  /**
   * Submits an entity browser.
   *
   * @param string $id
   *   (optional) The entity browser ID.
   */
  public function submit($id = NULL);

  /**
   * Submits an entity browser and asserts that it is completed.
   *
   * @param string $id
   *   (optional) The entity browser ID.
   */
  public function complete($id = NULL);

}
