<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Enums;

use Cubex\Type\Enum;

class Severity extends Enum
{
  const __default = 1;
  const LOW       = 1;
  const MINOR     = 2;
  const MODERATE  = 3;
  const HIGH      = 4;
  const CRITICAL  = 5;
}
