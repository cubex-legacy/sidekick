<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Enums;

use Cubex\Type\Enum;

class BuildType extends Enum
{
  const __default  = 'repo';
  const REPOSITORY = 'repo';
  const PATCH      = 'patch';
}
