<?php
/**
 * Author: oke.ugwu
 * Date: 03/09/13 10:34
 */
namespace Sidekick\Cli\Users;

use Cubex\Cli\CliCommand;
use Cubex\Cli\UserPrompt;
use Sidekick\Components\Users\Mappers\User;

class Create extends CliCommand
{
  /**
   * @required
   * @valuerequired
   */
  public $username;
  /**
   * @required
   * @valuerequired
   */
  public $password;

  public function execute()
  {
    $user = User::collection(['username' => $this->username])->first();
    if($user !== null && $user->exists())
    {
      $updatePassword = UserPrompt::confirm(
        "$this->username already exists. Do you wish to update password?"
      );
      if(!$updatePassword)
      {
        exit;
      }
      else
      {
        $user->password = password_hash($this->password, PASSWORD_DEFAULT);
        $user->saveChanges();
        echo "Password updated!" . PHP_EOL;
      }
    }
    else
    {
      $user           = new User();
      $user->username = $this->username;
      $user->password = password_hash($this->password, PASSWORD_DEFAULT);
      $user->saveChanges();
      echo "User Account created!" . PHP_EOL;
    }
  }
}
