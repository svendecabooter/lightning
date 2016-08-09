<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\AwaitTrait;
use Acquia\LightningExtension\EntityBrowserContextInterface;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for working with entity browsers that display iFrames.
 */
class EntityBrowserFrameContext extends DrupalSubContextBase implements EntityBrowserContextInterface {

  use AwaitTrait;

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
    // The iFrame will only exist in the top-level window context.
    $this->getSession()->switchToWindow();

    // Wait for the iFrame element to exist, so that any initialization scripts
    // have a chance to run.
    $selector = $this->getSelector($id);
    $this->awaitElement($selector);

    return $this->assertSession()
      ->elementExists('css', $selector)
      ->getAttribute('name');
  }

  /**
   * {@inheritdoc}
   *
   * @When I enter the entity browser
   * @When I enter the :id entity browser
   */
  public function enter($id = NULL) {
    $frame = $this->getFrame($id);
    $this->getSession()->switchToIFrame($frame);
    $this->awaitElement('form.entity-browser-form');
  }

  /**
   * {@inheritdoc}
   *
   * @When I submit the entity browser
   * @When I submit the :id entity browser
   */
  public function submit($id = NULL) {
    $this->getSession()
      ->executeScript('window.frames["' . $this->getFrame($id) . '"].document.querySelector("form.entity-browser-form").op.click()');
  }

  /**
   * {@inheritdoc}
   *
   * @When I complete the entity browser
   * @When I complete the :id entity browser
   */
  public function complete($id = NULL) {
    // Submitting the entity browser might close the frame, so get the frame
    // first so that we can assert its disappearance.
    $frame = $this->getFrame($id);
    $this->submit($id);
    $this->awaitExpression('typeof window.frames["' . $frame . '"] === "undefined"');
  }

}
