<?php

namespace Acquia\LightningExtension;

/**
 * Contains a mechanism for automatically undoing operations.
 */
trait UndoTrait {

  /**
   * The operations to run during undo.
   *
   * Each item in the array is an array consisting of a callable and an array
   * of arguments to pass to the callable.
   *
   * @var array
   */
  protected $undo = [];

  /**
   * Queues an operation to run during undo.
   *
   * @param callable $operation
   *   The operation to execute.
   * @param array $arguments
   *   (optional) Arguments to pass to the callable.
   */
  protected function undo(callable $operation, array $arguments = []) {
    array_push($this->undo, func_get_args());
  }

  /**
   * Executes all queued undo operations.
   */
  protected function undoAll() {
    while ($this->undo) {
      list ($function, $arguments) = array_pop($this->undo);
      call_user_func_array($function, $arguments);
    }
  }

}
