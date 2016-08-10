<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\ElementManipulationTrait;
use Acquia\LightningExtension\FieldUiTrait;
use Acquia\LightningExtension\TableTrait;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains steps for interacting with Panelizer and panelized layouts.
 */
class PanelizerContext extends DrupalSubContextBase {

  use ElementManipulationTrait;
  use FieldUiTrait;
  use TableTrait;

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
    $this->minkContext = $this->getContext(MinkContext::class);
  }

  /**
   * Places a block into a panelizer layout via Wizard.
   *
   * @param string $label
   *   The text name of the block.
   * @param string $region
   *   The name of the region in which to place the block.
   *
   * @When I place the :label block into the :region panelizer region
   */
  public function placeBlock($label, $region) {
    $this->minkContext->clickLink('Add new block');
    $this->minkContext->iWaitForAjaxToFinish();

    $this->minkContext->clickLink($label);
    $this->minkContext->iWaitForAjaxToFinish();

    $this->minkContext->selectOption('region', $region);
    $this->minkContext->iWaitForAjaxToFinish();

    $this->minkContext->pressButton('Add block');
    $this->minkContext->iWaitForAjaxToFinish();

    $this->assertBlock($label, $region);
  }

  /**
   * Removes a block from the panelizer layout via the Wizard.
   *
   * Assumes that exactly one block with the given name exists in the given
   * region.
   *
   * @param string $label
   *   The label of the block to remove.
   * @param string $region
   *   The machine name of the region in which the block is currently placed.
   *
   * @When I remove the :label block from the :region panelizer region
   */
  public function removeBlock($label, $region) {
    $row = $this->assertBlock($label, $region);

    $drop_button = $row->find('css', 'ul.dropbutton');
    $drop_button->find('css', 'li.dropbutton-toggle')->click();
    $drop_button->findLink('Delete')->click();
  }

  /**
   * Asserts that a block is present in a specific region of a Panelizer layout.
   *
   * @param string $label
   *   The block label.
   * @param string $region
   *   The machine name of the region in which the block is expected to be.
   *
   * @return NodeElement
   *   The block's row in the table.
   *
   * @throws ExpectationException
   *   If the block is not present as expected.
   *
   * @Given the :label block is in the :region region
   *
   * @Then the :label block should be in the :region region
   */
  public function assertBlock($label, $region) {
    $filter = function (NodeElement $row) use ($label, $region) {
      try {
        $select = $this->assertSession()
          ->elementExists('css', 'select.block-region-select', $row);
      }
      catch (ElementNotFoundException $e) {
        return FALSE;
      }

      if ($select->getValue() == $region) {
        $text = $row->find('css', 'td')->getText();
        $text = trim($text);

        return $text == $label;
      }
      else {
        return FALSE;
      }
    };

    $rows = $this->getTableRows('table#blocks', $filter);

    if ($rows) {
      return reset($rows);
    }
    else {
      throw new ExpectationException(
        'Expected block "' . $label . '" to be present in "' . $region . '" region.',
        $this->getSession()->getDriver()
      );
    }
  }

  /**
   * Saves the current Panels layout as a custom layout.
   *
   * @When I save the layout as a custom override
   */
  public function saveCustomLayout() {
    $this->saveLayout('custom');
  }

  /**
   * Saves the current Panels layout as a default layout.
   *
   * @When I save the layout as the default
   */
  public function saveDefaultLayout() {
    $this->saveLayout('default');
  }

  /**
   * Saves the current Panels layout.
   *
   * @param string $type
   *   How to save the layout. Can be 'default' or 'custom'.
   */
  protected function saveLayout($type) {
    $this->clickSelector('a[title = "Save"]');
    $this->minkContext->iWaitForAjaxToFinish();

    $this->clickSelector('a.panelizer-ipe-save-' . $type);
    $this->minkContext->iWaitForAjaxToFinish();
  }

  /**
   * Applies Panelizer to a view of mode of an entity type and bundle.
   *
   * @param string $bundle
   *   The node type ID.
   *
   * @Given I have panelized the :view_mode view mode of the :bundle :entity_type type
   *
   * @When I panelize the :view_mode view mode of the :bundle :entity_type type
   */
  public function panelize($entity_type, $view_mode, $bundle = NULL) {
    $this->manageDisplay($entity_type, $bundle);
    $this->minkContext->clickLink($view_mode);
    $this->minkContext->checkOption('panelizer[enable]');
    $this->minkContext->checkOption('panelizer[custom]');
    $this->minkContext->pressButton('Save');
  }

  /**
   * Removes Panelizer from a node type.
   *
   * @param string $bundle
   *   The node type ID.
   *
   * @Given I have unpanelized the :view_mode view of the :bundle :entity_type type
   *
   * @When I unpanelize the :view_mode view mode of the :bundle :entity_type type
   */
  public function unpanelize($entity_type, $view_mode, $bundle = NULL) {
    $this->manageDisplay($entity_type, $bundle);
    $this->minkContext->clickLink($view_mode);
    $this->minkContext->uncheckOption('panelizer[enable]');
    $this->minkContext->uncheckOption('panelizer[custom]');
    $this->minkContext->pressButton('Save');
  }

}
