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

    $this->getContext(EntityBrowserFrameContext::class)
      ->enterEntityBrowser($button);
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
    sleep(5);
    $row = floor($n / 4) + 1;
    $column = ($n % $row) + 1;

    $selector = '.view-media .row-' . $row . ' .col-' . $column;
    $this->clickSelector($selector);
  }

  /**
   * Completes the media browser selection.
   *
   * @When I complete the media browser selection
   */
  public function completeSelection() {
    /** @var \Behat\Mink\Session $session */
    $session = $this->getSession();
    $session->switchToWindow();
    $session->executeScript('window.frames.entity_browser_iframe_media_browser.document.forms[0].elements.op.click()');
    $this->await->awaitExpression('!window.frames.entity_browser_iframe_media_browser');
    $this->minkContext->iWaitForAjaxToFinish();
    $this->await->awaitElement('form.entity-embed-dialog');
    $session->executeScript('document.querySelector("form.entity-embed-dialog").elements.op[1].click()');
    $this->minkContext->iWaitForAjaxToFinish();
  }

  /**
   * Waits for the inline entity form (containing required fields) to exist.
   */
  protected function awaitEntityForm() {
    $this->minkContext->iWaitForAjaxToFinish();
    $this->await->awaitExpression('document.getElementById("ief-target").textContent.length');
  }

  /**
   * Uploads a file in the media browser.
   *
   * @param string $file
   *   The path to the file, relative to the test files directory.
   *
   * @When I upload :file
   */
  public function upload($file) {
    $this->minkContext->clickLink('Upload');
    $this->minkContext->attachFileToField('File', $file);
    $this->awaitEntityForm();
  }

  /**
   * Enters an embed code in the media browser.
   *
   * @param string $code
   *   The embed code.
   *
   * @When I enter embed code :code
   */
  public function embed($code) {
    $this->minkContext->clickLink('Create Embed');
    $this->minkContext->fillField('embed_code', $code);
    // The change event, which triggers AJAX, is fired automatically after 600
    // milliseconds.
    sleep(1);
    $this->awaitEntityForm();
  }

}
