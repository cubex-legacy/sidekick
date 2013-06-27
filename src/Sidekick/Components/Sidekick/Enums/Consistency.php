<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Sidekick\Enums;

use Cubex\Type\Enum;

class Consistency extends Enum
{
  const __default = 'none';
  const NONE      = 'none';
  const ONE       = 'one';
  const TWO       = 'two';
  const ALL       = 'all';
  const QUORUM    = 'quorum';
}
