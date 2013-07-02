<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Users\Mappers;

use Cubex\Data\Validator\Validator;
use Cubex\Mapper\Database\RecordMapper;

class User extends RecordMapper
{
  public $username;
  public $email;
  public $password;
  public $displayName;
}
