<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Enums;

use Cubex\Type\Enum;

class BuildType extends Enum
{
  const __default  = 'repo';
  const REPOSITORY = 'repo';
  const PATCH      = 'patch';
}
