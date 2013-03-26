<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Mappers;

use Cubex\Type\Enum;

class UserRole extends Enum
{
  const __default = 'user';
  const USER      = 'user';
  const TESTER    = 'tester';
  const MANAGER   = 'manager';
  const DEVELOPER = 'developer';
}
