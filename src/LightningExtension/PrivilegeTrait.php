<?php

namespace Acquia\LightningExtension;

use Drupal\DrupalExtension\Context\DrupalContext;

/**
 * Contains helper methods for temporarily escalating privileges.
 */
trait PrivilegeTrait {

  /**
   * The previous, unprivileged user.
   *
   * @var \stdClass
   */
  protected $previousUser;

  /**
   * Switches to a user account with a set of roles.
   *
   * @param string[] $roles
   *    The roles to acquire.
   *
   * @throws \Exception
   *   If DrupalContext is not available.
   */
  protected function acquireRoles(array $roles) {
    $context = $this->getContext(DrupalContext::class);

    if ($context) {
      $this->previousUser = $context->user;

      $roles = implode(',', $roles);
      $context->assertAuthenticatedByRole($roles);
    }
    else {
      throw new \Exception('Cannot acquire roles without DrupalContext.');
    }
  }

  /**
   * Returns to the previous, unprivileged user context.
   *
   * @throws \Exception
   *   If DrupalContext is not available.
   */
  protected function releasePrivileges() {
    $context = $this->getContext(DrupalContext::class);

    if ($context) {
      if ($this->previousUser) {
        $context->assertLoggedInByName($this->previousUser->name);
      }
      else {
        $context->assertAnonymousUser();
      }
      $this->previousUser = NULL;
    }
    else {
      throw new \Exception('Cannot release privileges without DrupalContext.');
    }
  }

}
