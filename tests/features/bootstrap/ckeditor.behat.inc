<?php

/**
 * @file
 * Contains \CkEditorSubContext.
 */

use Acquia\LightningExtension\CkEditorApiTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for working with CKEditor instances.
 */
class CkEditorSubContext extends DrupalSubContextBase {

  use CkEditorApiTrait {
    insert as doInsert;
    execute as doExecute;
  }

  /**
   * Asserts that a CKEditor instance exists.
   *
   * @param string $id
   *   The editor instance ID.
   *
   * @throws \Exception
   *   If the specified CKEditor instance does not exist.
   *
   * @Given CKEditor :id exists
   *
   * @Then CKEditor :id should exist
   */
  public function assertEditorExists($id) {
    $exists = in_array($id, $this->getInstances());

    if ($exists == FALSE) {
      throw new \Exception("CKEditor '$id' does not exist.");
    }
  }

  /**
   * Puts text or HTML into a CKEditor instance.
   *
   * @param string $text
   *   The text (or HTML) to insert into the editor.
   * @param string $id
   *   (optional) The editor instance ID.
   *
   * @When I put :text into CKEditor
   * @When I put :text into CKEditor :id
   */
  public function insert($text, $id = NULL) {
    $this->doInsert($id ?: $this->getDefaultEditorId(), $text);
  }

  /**
   * Asserts that a CKEditor's content contains a snippet of text.
   *
   * @param string $text
   *   The text (or HTML) snippet to look for.
   * @param string $id
   *   (optional) The editor instance ID.
   *
   * @throws \Exception
   *   If the editor does not contain the specified text.
   *
   * @Then CKEditor should contain :text
   * @Then CKEditor :id should contain :text
   */
  public function assertEditorContains($text, $id = NULL) {
    $content = $this->getContents($id ?: $this->getDefaultEditorId());

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
   *   (optional) The editor instance ID.
   *
   * @throws \Exception
   *   If the expression does not match.
   *
   * @Then CKEditor should match :expression
   * @Then CKEditor :id should match :expression
   */
  public function assertEditorMatch($expression, $id = NULL) {
    $content = $this->getContents($id ?: $this->getDefaultEditorId());

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
   *   (optional) The editor instance ID.
   * @param mixed $data
   *   Additional data to pass to the executed command.
   *
   * @When I execute the :command command in CKEditor
   * @When I execute the :command command in CKEditor :id
   */
  public function execute($command, $id = NULL, $data = NULL) {
    $this->doExecute($id ?: $this->getDefaultEditorId(), $command, $data);
  }

  /**
   * Returns the first available CKEditor instance ID.
   *
   * @return string|false
   *   The first CKEditor instance ID, or FALSE if there are no instances.
   */
  protected function getDefaultEditorId() {
    $instances = $this->getInstances();
    return reset($instances);
  }

}
