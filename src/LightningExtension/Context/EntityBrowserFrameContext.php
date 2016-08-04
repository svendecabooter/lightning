<?php

namespace Acquia\LightningExtension\Context;

use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for working with entity browsers that display iFrames.
 */
class EntityBrowserFrameContext extends DrupalSubContextBase {

  /**
   * The Await context.
   *
   * @var AwaitContext
   */
  protected $await;

  /**
   * Pre-scenario hook.
   *
   * @BeforeScenario
   */
  public function gatherContexts() {
    $this->await = $this->getContext(AwaitContext::class);
  }

  /**
   * Returns the CSS selector for an entity browser iFrame.
   *
   * @param string $id
   *   (optional) The entity browser ID. If omitted, the selector will match any
   *   entity browser iFrame.
   *
   * @return string
   *   The CSS selector.
   */
  protected function getSelector($id = NULL) {
    if ($id) {
      return 'iframe[name="entity_browser_iframe_' . $id . '"]';
    }
    else {
      return 'iframe[name^="entity_browser_iframe_"]';
    }
  }

  /**
   * Returns the name of an entity browser iFrame.
   *
   * @param string $id
   *   (optional) The entity browser ID. If omitted, the first available entity
   *   browser iFrame name will be returned.
   *
   * @return string
   *   The iFrame name.
   */
  protected function getFrame($id = NULL) {
    $selector = $this->getSelector($id);

    $this->await->awaitElement($selector);

    return $this->assertSession()
      ->elementExists('css', $selector)
      ->getAttribute('name');
  }

  /**
   * Switches to an entity browser iFrame context.
   *
   * @param string $id
   *   (optional) The entity browser ID. If omitted, will switch the first
   *   available entity browser iFrame.
   *
   * @When I enter the entity browser
   * @When I enter the :id entity browser
   */
  public function enterEntityBrowser($id = NULL) {
    $frame = $this->getFrame($id);
    $this->getSession()->switchToIFrame($frame);
    $this->await->awaitElement('form.entity-browser-form');
  }

}
