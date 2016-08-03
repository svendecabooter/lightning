<?php

namespace Acquia\LightningExtension;

use Drupal\Driver\DrupalDriver;

/**
 * Contains helper methods for contexts which use the Drupal API natively.
 */
trait DrupalApiTrait {

  /**
   * Returns the current route name, as determined by the current URL path.
   *
   * @return string
   *    The current route name.
   *
   * @throws \UnexpectedValueException
   *   If the current path does not match any known route.
   */
  protected function getRouteName() {
    $this->assertDrupalApi();

    $path = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);

    $url = \Drupal::service('path.validator')->getUrlIfValidWithoutAccessCheck($path);
    if ($url) {
      return $url->getRouteName();
    }
    else {
      throw new \UnexpectedValueException($path . ' does not match any known route.');
    }
  }

  /**
   * Asserts the presence of the Drupal API.
   *
   * @throws \Exception
   *   If the current driver is not the Drupal API driver, or if the driver is
   *   not bootstrapped.
   */
  protected function assertDrupalApi() {
    $driver = $this->getDriver();

    if ($driver instanceof DrupalDriver) {
      if ($driver->isBootstrapped() == FALSE) {
        throw new \Exception('Drupal API driver is not bootstrapped.');
      }
    }
    else {
      throw new \Exception('Not using native Drupal API driver.');
    }
  }

}
