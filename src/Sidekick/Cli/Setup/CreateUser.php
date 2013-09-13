<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Setup;

use Cubex\Cli\CliCommand;
use Cubex\Cli\UserPrompt;
use Sidekick\Components\Users\Enums\UserRole;
use Sidekick\Components\Users\Mappers\User;

class CreateUser extends CliCommand
{
  /**
   * @valuerequired
   * @required
   * @validate length 2
   */
  public $username;
  /**
   * @valuerequired
   * @required
   * @validate length 2
   */
  public $password;

  public function execute()
  {
    echo "Sidekick User Creation\n\n";
    $user              = new User();
    $user->username    = $this->username;
    $user->userRole    = UserRole::ADMINISTRATOR;
    $user->password    = password_hash($this->password, PASSWORD_DEFAULT);
    $user->displayName = $this->username;
    $user->email       = UserPrompt::prompt(
      "What is your email address?",
      $this->username
    );
    $user->saveChanges();

    echo "\nYour user account has been created\n";
  }
}
