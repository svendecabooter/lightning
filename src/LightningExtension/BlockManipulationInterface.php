<?php

namespace Acquia\LightningExtension;

/**
 * Interface for contexts which can place and manipulate blocks.
 */
interface BlockManipulationInterface {

  /**
   * Places a block into a layout region.
   *
   * @param string $identifier
   *   An identifier for the block to place. The meaning of this value varies
   *   according to the implementing class.
   * @param string $region
   *   The region in which to place the block.
   */
  public function placeBlock($identifier, $region);

  /**
   * Removes a block from a layout region.
   *
   * @param string $identifier
   *   An identifier for the block to remove. The meaning of this value varies
   *   according to the implementing class.
   * @param string $region
   *   The region from which to remove the block.
   */
  public function removeBlock($identifier, $region);

}
