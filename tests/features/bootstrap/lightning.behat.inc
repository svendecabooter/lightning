<?php

/**
 * @file
 * Contains \LightningSubContext.
 */

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\DrupalSubContextInterface;

/**
 * Sub context for Lightning step definitions.
 */
class LightningSubContext extends DrupalSubContextBase implements DrupalSubContextInterface {

  /**
   * The Mink context.
   *
   * @var \Drupal\DrupalExtension\Context\MinkContext
   */
  protected $minkContext;

  /**
   * Pre-scenario hook.
   *
   * @BeforeScenario
   */
  public function gatherContexts(BeforeScenarioScope $scope) {
    $environment = $scope->getEnvironment();

    $this->minkContext = $environment->getContext('Drupal\DrupalExtension\Context\MinkContext');
  }

  /**
   * Asserts that an element, specified by CSS selector, exists.
   *
   * @param string $selector
   *   The CSS selector to search for.
   *
   * @Then the element :selector should exist
   */
  public function theElementShouldExist($selector) {
    $this->assertSession()->elementExists('css', $selector);
  }

  /**
   * Clicks an arbitrary element, found by CSS selector.
   *
   * @param string $selector
   *   The CSS selector.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   *   If the specified element is not present on the page.
   *
   * @When I click the :selector element
   */
  public function clickElementBySelector($selector) {
    $session = $this->getSession();
    $element = $session->getPage()->find('css', $selector);
    if ($element) {
      try {
        $this->scrollToElement($selector);
      }
      catch (UnsupportedDriverActionException $e) {
        // Don't worry about it.
      }
      $element->click();
    }
    else {
      throw new ElementNotFoundException($session, 'element', 'css', $selector);
    }
  }

  /**
   * Asserts that a role as a set of permissions.
   *
   * @param string $rid
   *   The role ID.
   * @param \Behat\Gherkin\Node\PyStringNode $permissions
   *   The permissions to check for.
   *
   * @Then the :role role should have permissions:
   * @Then the :role role should have permission to:
   */
  public function assertPermissions($rid, PyStringNode $permissions) {
    foreach ($permissions->getStrings() as $permission) {
      $this->assertPermission($rid, $permission);
    }
  }

  /**
   * Asserts that a role does NOT have a set of permissions.
   *
   * @param string $rid
   *   The role ID.
   * @param \Behat\Gherkin\Node\PyStringNode $permissions
   *   The permissions to check for.
   *
   * @Then the :role role should not have permissions:
   * @Then the :role role should not have permission to:
   */
  public function assertNoPermissions($rid, PyStringNode $permissions) {
    foreach ($permissions->getStrings() as $permission) {
      $this->assertNoPermission($rid, $permission);
    }
  }

  /**
   * Asserts that a role has a specific permission.
   *
   * @param string $rid
   *   The role ID.
   * @param string $permission
   *   The permission to check for.
   *
   * @Given the :role role has the :permission permission
   * @Given the :role role has permission to :permission
   *
   * @Then the :role role should have the :permission permission
   * @Then the :role role should have permission to :permission
   */
  public function assertPermission($rid, $permission) {
    $this->minkContext->assertAtPath('/admin/people/permissions/' . $rid);
    $this->assertSession()->checkboxChecked($rid . '[' . $permission . ']');
  }

  /**
   * Asserts that a role does not have a specific permission.
   *
   * @param string $rid
   *   The role ID.
   * @param string $permission
   *   The permission to check for.
   *
   * @Given the :role role does not have the :permission permission
   * @Given the :role role does not have permission to :permission
   *
   * @Then the :role role should not have the :permission permission
   * @Then the :role role should not have permission to :permission
   */
  public function assertNoPermission($rid, $permission) {
    $this->minkContext->assertAtPath('/admin/people/permissions/' . $rid);

    $field = $rid . '[' . $permission . ']';
    try {
      $this->assertSession()->fieldNotExists($field);
    }
    catch (ExpectationException $e) {
      $this->assertSession()->checkboxNotChecked($field);
    }
  }

  /**
   * Creates a video entity from an embed code.
   *
   * @param \Behat\Gherkin\Node\PyStringNode $embed_code
   *   The embed code.
   *
   * @Given video from embed code:
   *
   * @When I create a video from embed code:
   */
  public function videoFromEmbedCode(PyStringNode $embed_code) {
    $this->createMediaFromEmbedCode('video', $embed_code, 'field_media_video_embed_field');
  }

  /**
   * Creates a media asset from an embed code.
   *
   * @param string $bundle
   *   The media bundle ID.
   * @param \Behat\Gherkin\Node\PyStringNode $embed_code
   *   The embed code.
   * @param string $field
   *   (optional) The embed code field. Defaults to embed_code.
   *
   * @Given :bundle media from embed code:
   *
   * @When I create :bundle media from embed code:
   */
  public function createMediaFromEmbedCode($bundle, PyStringNode $embed_code, $field = 'embed_code') {
    $entity = \Drupal::entityTypeManager()
      ->getStorage('media')
      ->create([
        'bundle' => $bundle,
        'name' => $this->getRandom()->string(),
        $field => (string) $embed_code,
        'field_media_in_library' => TRUE,
        'status' => TRUE,
      ]);
    $entity->save();

    /** @var \EntitySubContext $context */
    $context = $this->getContext('EntitySubContext');
    $context->queueEntity($entity);
  }

  /**
   * Scrolls an element into the viewport.
   *
   * @param string $selector
   *   The element's CSS selector.
   *
   * @When I scroll to the :selector element
   */
  public function scrollToElement($selector) {
    $this->getSession()
      ->executeScript('document.querySelector("' . addslashes($selector) . '").scrollIntoView()');
  }

  /**
   * Expands a drop button.
   *
   * @param string $selector
   *   The data-drupal-selector attribute of the drop button.
   *
   * @When I expand the :selector drop button
   */
  public function expandDropButton($selector) {
    $selector = 'ul.dropbutton[data-drupal-selector="' . $selector . '"]';
    $this->clickElementBySelector($selector . ' li.dropbutton-toggle');
    $this->scrollToElement($selector);
  }

  /**
   * Waits a while, for debugging.
   *
   * @param int $seconds
   *   How long to wait.
   *
   * @When I wait :seconds seconds
   */
  public function wait($seconds) {
    sleep($seconds);
  }

  /**
   * Maximizes the window (probably for debugging purposes).
   *
   * @When I maximize the window
   */
  public function maximize() {
    $this->getSession()->getDriver()->maximizeWindow();
  }

  /**
   * Takes a screenshot for debugging purposes.
   *
   * @param string $filename
   *   The name of the screenshot file.
   *
   * @When I take a screenshot named :filename
   */
  public function takeScreenshot($filename) {
    $screenshot = $this->getSession()->getDriver()->getScreenshot();
    // If this file is in tests/features/bootstrap, the screenshot be in tests.
    file_put_contents(__DIR__ . '../../' . $filename . '.png', $screenshot);
  }

  /**
   * Fills in an autocompleting entity reference field.
   *
   * @param string $entity_type
   *   The target entity type.
   * @param string $label
   *   The target entity's label.
   * @param string $field
   *   Entity reference field locator.
   *
   * @throws \Exception
   *   If no entities of the given type, with the given label, are found.
   *
   * @When I reference :entity_type :label in :field
   */
  public function referenceEntityByLabel($entity_type, $label, $field) {
    $label_property = \Drupal::entityTypeManager()
      ->getDefinition($entity_type)
      ->getKey('label');

    $entities = \Drupal::entityTypeManager()
      ->getStorage($entity_type)
      ->loadByProperties([
        $label_property => $label,
      ]);

    if ($entities) {
      $this->minkContext->fillField($field, $label . ' (' . reset($entities)->id() . ')');
    }
    else {
      throw new \Exception("No $entity_type labeled '$label' was found.");
    }
  }

  /**
   * Asserts the presence of a live preview element.
   *
   * @Then I should see a preview
   */
  public function assertPreviewElement() {
    $this->assertSession()->elementExists('css', '#edit-preview article');
  }

  /**
   * Assets that a field exists.
   *
   * @param string $locator
   *   The field ID, name, label, or value.
   *
   * @Then I should see a :locator field
   */
  public function assertFieldExists($locator) {
    $this->assertSession()->fieldExists($locator);
  }

  /**
   * Asserts that we are visiting a media entity.
   *
   * @Then I should be visiting a media entity
   */
  public function assertVisitingMediaEntity() {
    $this->assertSession()->addressMatches('/\/media\/[0-9]+$/');

    try {
      $this->assertSession()->statusCodeEquals(200);
    }
    catch (UnsupportedDriverActionException $e) {
      // This isn't a critically important assertion, so don't worry about it.
    }
  }

  /**
   * Sets the absolute path to the directory containing uploadable files.
   *
   * @BeforeScenario
   */
  public function setFilePath() {
    $this->minkContext->setMinkParameter('files_path', __DIR__ . '/../../files');
  }

  /**
   * Switches out of an iframe into the main window.
   *
   * @When I switch to the window
   */
  public function switchToWindow() {
    $this->getSession()->switchToWindow();
  }

  /**
   * Asserts that a given field has the disabled attribute.
   *
   * @param string $field
   *   The label, placeholder, ID or name of the field to check.
   *
   * @Then the :field field should be disabled
   *
   * @throws ExpectationException
   *   If the field does not have the disabled attribute.
   */
  public function assertDisabledField($field) {
    $element = $this->assertSession()->fieldExists($field);
    if (!$element->hasAttribute('disabled')) {
      throw new ExpectationException("Expected '{$field}' field to be disabled.", $this->getSession()->getDriver());
    }
  }

  /**
   * Submits the image browser.
   *
   * @When I submit the entity browser
   */
  public function submitEntityBrowser() {
    $this->getSession()
      ->executeScript('window.frames["entity_browser_iframe_image_browser"].document.forms[0].op.click()');
  }

  /**
   * Switches to an iframe.
   *
   * @param string $name
   *   The iframe name.
   *
   * @When I switch to the :name frame
   */
  public function switchToFrame($name) {
    $this->getSession()->switchToIFrame($name);
  }

}
