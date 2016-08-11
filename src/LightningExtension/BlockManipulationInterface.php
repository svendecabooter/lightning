<?php

namespace Acquia\LightningExtension;

/**
 * Interface for contexts which can place and manipulate blocks.
 */
interface BlockManipulationInterface {

  /**
   * Places a block into a layout.
   *
   * @param string $label
   *   The text name of the block.
   * @param string $region
   *   The name of the region in which to place the block.
   */
  public function placeBlock($label, $region);

  /**
   * Removes a block from a layout.
   *
   * Assumes that exactly one block with the given name exists in the given
   * region.
   *
   * @param string $label
   *   The label of the block to remove.
   * @param string $region
   *   The machine name of the region in which the block is currently placed.
   */
  public function removeBlock($label, $region);

}
