<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Enums;

use Cubex\Type\Enum;

class VersionType extends Enum
{
  const __default         = 'std';
  const STANDARD          = 'std';
  const RELEASE_CANDIDATE = 'RC';
  const BETA              = 'beta';
  const ALPHA             = 'alpha';
  const PATCH             = 'p';
}
