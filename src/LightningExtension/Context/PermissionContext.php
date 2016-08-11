<?php

namespace Acquia\LightningExtension\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains step definitions for dealing with permissions.
 */
class PermissionContext extends DrupalSubContextBase {

  /**
   * Asserts that a role has a permission or set of permissions.
   *
   * @param string $rid
   *   The role ID.
   * @param string $single
   *   (optional) A single permission to assert.
   * @param \Behat\Gherkin\Node\PyStringNode $set
   *   (optional) A set of permissions to assert.
   *
   * @Given the :rid role has the :permission permission
   * @Given the :rid role has permissions:
   *
   * @Then the :rid role should have the :permission permission
   * @Then the :rid role should have permissions:
   */
  public function assertPermission($rid, $single = NULL, PyStringNode $set = NULL) {
    $this->visitPath('admin/people/permissions/' . $rid);

    $assert = $this->assertSession();

    $checkboxes = $this->getCheckboxes($rid, $single, $set);
    // If you're looking at this and wondering why we didn't use array_walk(),
    // you must think you're pretty clever. Turns out the array_walk() callback
    // function will receive the value first, and the key second...but
    // WebAssert::checkboxChecked() expects a containing element as the second
    // argument. So using array_walk() will produce a recoverable fatal error.
    foreach ($checkboxes as $checkbox) {
      $assert->checkboxChecked($checkbox);
    }
  }

  /**
   * Asserts that a role does not have a permission or set of permissions.
   *
   * @param string $rid
   *   The role ID.
   * @param string $single
   *   (optional) A single permission to assert.
   * @param \Behat\Gherkin\Node\PyStringNode $set
   *   (optional) A set of permissions to assert.
   *
   * @Given the :rid role does not have the :permission permission
   * @Given the :rid role does not have permissions:
   *
   * @Then the :rid role should not have the :permission permission
   * @Then the :rid role should not have permissions:
   */
  public function assertNoPermission($rid, $single = NULL, PyStringNode $set = NULL) {
    $this->visitPath('admin/people/permissions/' . $rid);

    $checkboxes = $this->getCheckboxes($rid, $single, $set);

    foreach ($checkboxes as $checkbox) {
      try {
        $this->assertSession()->checkboxNotChecked($checkbox);
      }
      catch (ElementNotFoundException $e) {
        // If the checkbox doesn't exist, the role doesn't have the permission.
      }
    }
  }

  /**
   * Returns the names of permission checkboxes for a role.
   *
   * @param string $rid
   *   The role ID.
   * @param string $single
   *   (optional) A single permission.
   * @param \Behat\Gherkin\Node\PyStringNode $set
   *   (optional) A set of permissions.
   *
   * @return string[]
   *   A set of checkbox names.
   */
  protected function getCheckboxes($rid, $single = NULL, PyStringNode $set = NULL) {
    return array_map(
      function ($permission) use ($rid) {
        return $rid . '[' . $permission . ']';
      },
      $this->resolvePermissions($single, $set)
    );
  }

  /**
   * Returns a normalized array of permissions.
   *
   * @param string $single
   *   (optional) A single permission.
   * @param \Behat\Gherkin\Node\PyStringNode $set
   *   (optional) A set of permissions.
   *
   * @return string[]
   *   The permissions.
   *
   * @throws \InvalidArgumentException
   *   If neither a single permission nor a set of permissions is supplied.
   */
  protected function resolvePermissions($single = NULL, PyStringNode $set = NULL) {
    if ($set) {
      return $set->getStrings();
    }
    elseif ($single) {
      return [$single];
    }
    else {
      throw new \InvalidArgumentException('Expected single permission or pystring list of permissions.');
    }
  }

}
