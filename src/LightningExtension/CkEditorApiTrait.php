<?php

namespace Acquia\LightningExtension;

use Drupal\Component\Serialization\Json;

/**
 * Contains helper methods for interacting with CKEditor instances.
 */
trait CkEditorApiTrait {

  /**
   * Inserts text into a CKEditor instance.
   *
   * @param string $id
   *   The instance ID.
   * @param string $text
   *   The text to insert.
   */
  protected function insert($id, $text) {
    $js = sprintf(
      'CKEDITOR.instances["%s"].insertHtml("%s")',
      $id,
      addslashes($text)
    );

    $this->getSession()->executeScript($js);
  }

  /**
   * Returns the contents of a CKEditor instance.
   *
   * @param string $id
   *   The instance ID.
   *
   * @return string
   *   The contents of the instance.
   */
  protected function getContents($id) {
    return $this->getSession()
      ->evaluateScript('CKEDITOR.instances["' . $id . '"].getData()');
  }

  /**
   * Executes a command in a CKEditor instance.
   *
   * @param string $id
   *   The instance ID.
   * @param string $command
   *   The command to execute.
   * @param array $data
   *   (optional) Additional data to pass to the command.
   *
   * @return mixed
   *   The value returned by the command.
   */
  protected function execute($id, $command, array $data = NULL) {
    $js = sprintf(
      'CKEDITOR.instances["%s"].execCommand("%s"%s)',
      $id,
      $command,
      isset($data) ? ', ' . Json::encode($data) : NULL
    );

    return $this->getSession()->evaluateScript($js);
  }

  /**
   * Returns all available CKEditor instance IDs.
   *
   * @return string[]
   *   All available CKEditor instance IDs.
   */
  protected function getInstances() {
    $instances = $this->getSession()
      ->evaluateScript('Object.keys( CKEDITOR.instances ).join(",")');

    return explode(',', $instances);
  }

}
