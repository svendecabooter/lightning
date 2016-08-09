<?php

namespace Acquia\LightningExtension;

/**
 * Contains helper methods for dealing with HTML tables.
 */
trait TableTrait {

  /**
   * Returns all rows in a table that optionally pass a filter function.
   *
   * @param string $table_selector
   *   The table's CSS selector.
   * @param callable $filter
   *   (optional) The filter function. If omitted, all rows will be returned.
   *
   * @return \Behat\Mink\Element\NodeElement[]
   *   The rows of the table, excluding any in the table header (thead) and
   *   footer (tfoot).
   */
  protected function getTableRows($table_selector, callable $filter = NULL) {
    $table = $this->assertSession()->elementExists('css', $table_selector);

    // Try to find the tbody, if there is one, so that we ignore the header and
    // footer rows. Semantic markup FTW!
    $tbody = $table->find('css', 'tbody') ?: $table;

    return array_filter($tbody->findAll('css', 'tr'), $filter);
  }

}
