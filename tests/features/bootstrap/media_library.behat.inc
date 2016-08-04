<?php

/**
 * @file
 * Contains \MediaLibrarySubContext.
 */

use Acquia\LightningExtension\Context\AwaitContext;
use Acquia\LightningExtension\Context\EntityBrowserFrameContext;
use Acquia\LightningExtension\ElementManipulationTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains step definitions for testing the CKEditor media widget.
 */
class MediaLibrarySubContext extends DrupalSubContextBase {

  use ElementManipulationTrait;

  /**
   * The Await context.
   *
   * @var AwaitContext
   */
  protected $await;

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
    $this->await = $this->getContext(AwaitContext::class);
    $this->minkContext = $this->getContext(MinkContext::class);
  }

  /**
   * Opens the media browser, obviously.
   *
   * @param string $button
   *   (optional) The embed button ID.
   *
   * @When I open the media browser
   */
  public function openMediaBrowser($button = 'media_browser') {
    $this->getContext('\CkEditorSubContext')
      ->execute('editdrupalentity', NULL, ['id' => $button]);

    $this->minkContext->iWaitForAjaxToFinish();

    $this->getContext(EntityBrowserFrameContext::class)->enter($button);
  }

  /**
   * Selects an item from the media library.
   *
   * @param int $n
   *   The one-based index of the item to select.
   *
   * @When I select item :n in the media browser
   */
  public function selectNthItem($n) {
    $this->clickViewItem('media', --$n);
  }

  /**
   * Completes the media browser selection.
   *
   * @When I complete the media browser selection
   */
  public function completeSelection() {
    /** @var \Behat\Mink\Session $session */
    $session = $this->getSession();

    $this->getContext(EntityBrowserFrameContext::class)->complete();

    $this->minkContext->iWaitForAjaxToFinish();
    $this->await->awaitElement('form.entity-embed-dialog');
    $session->executeScript('document.querySelector("form.entity-embed-dialog").elements.op[1].click()');
    $this->minkContext->iWaitForAjaxToFinish();
  }

}
