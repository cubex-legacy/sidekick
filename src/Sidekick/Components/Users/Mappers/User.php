<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Users\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class User extends RecordMapper
{
  public $username;
  public $password;
  public $displayName;
}
