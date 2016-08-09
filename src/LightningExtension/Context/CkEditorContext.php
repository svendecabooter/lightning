<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\CkEditorApiTrait;
use Behat\Mink\Exception\ExpectationException;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for working with CKEditor instances.
 */
class CkEditorContext extends DrupalSubContextBase {

  use CkEditorApiTrait {
    insert as doInsert;
    execute as doExecute;
    getContents as doGetContents;
  }

  /**
   * Asserts that a CKEditor instance exists.
   *
   * @param string $id
   *   (optional) The instance ID.
   *
   * @throws ExpectationException
   *   If an instance ID is specified and does not exist.
   * @throws ExpectationException
   *   If no instance ID is specified and no instances exist.
   *
   * @Given CKEditor :id exists
   * @Given CKEditor exists
   *
   * @Then CKEditor :id should exist
   * @Then CKEditor should exist
   */
  public function assertInstance($id = NULL) {
    $driver = $this->getSession()->getDriver();

    $instances = $this->getInstances();

    if ($id && !in_array($id, $instances)) {
      throw new ExpectationException(
        'CKEditor instance ' . $id . ' does not exist.',
        $driver
      );
    }
    elseif (empty($instances)) {
      throw new ExpectationException('No CKEditor instances exist.', $driver);
    }
  }

  /**
   * Puts text or HTML into a CKEditor instance.
   *
   * @param string $text
   *   The text (or HTML) to insert into the editor.
   * @param string $id
   *   (optional) The instance ID.
   *
   * @When I put :text into CKEditor
   * @When I put :text into CKEditor :id
   */
  public function insert($text, $id = NULL) {
    if (empty($id)) {
      $id = $this->defaultInstance();
    }
    $this->doInsert($id, $text);
  }

  /**
   * Asserts that a CKEditor's content contains a snippet of text.
   *
   * @param string $text
   *   The text (or HTML) snippet to look for.
   * @param string $id
   *   (optional) The instance ID.
   *
   * @throws \Exception
   *   If the editor does not contain the specified text.
   *
   * @Then CKEditor should contain :text
   * @Then CKEditor :id should contain :text
   */
  public function assertEditorContains($text, $id = NULL) {
    $content = $this->getContents($id);

    if (strpos($content, $text) == FALSE) {
      throw new \Exception("CKEditor $id did not contain '$text'.");
    }
  }

  /**
   * Assert that a CKEditor's content matches a regular expression.
   *
   * @param string $expression
   *   The regular expression to match.
   * @param string $id
   *   (optional) The instance ID.
   *
   * @throws \Exception
   *   If the expression does not match.
   *
   * @Then CKEditor should match :expression
   * @Then CKEditor :id should match :expression
   */
  public function assertEditorMatch($expression, $id = NULL) {
    $content = $this->getContents($id);

    if (preg_match($expression, $content) == 0) {
      throw new \Exception("CKEditor $id did not match '$expression'.");
    }
  }

  /**
   * Executes a CKEditor command.
   *
   * @param string $command
   *   The command ID, as known to CKEditor's API.
   * @param string $id
   *   (optional) The instance ID.
   * @param mixed $data
   *   Additional data to pass to the executed command.
   *
   * @When I execute the :command command in CKEditor
   * @When I execute the :command command in CKEditor :id
   */
  public function execute($command, $id = NULL, $data = NULL) {
    if (empty($id)) {
      $id = $this->defaultInstance();
    }
    $this->doExecute($id, $command, $data);
  }

  /**
   * Returns the contents of a CKEditor instance.
   *
   * @param string $id
   *   (optional) The instance ID.
   *
   * @return string
   *   The instance's contents.
   */
  protected function getContents($id = NULL) {
    if (empty($id)) {
      $id = $this->defaultInstance();
    }
    return $this->doGetContents($id);
  }

  /**
   * Returns the first available CKEditor instance ID.
   *
   * @return string|false
   *   The first CKEditor instance ID, or FALSE if there are no instances.
   */
  protected function defaultInstance() {
    $instances = $this->getInstances();
    return reset($instances);
  }

}
