<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Enums;

use Cubex\Type\Enum;

class BuildLevel extends Enum
{
  const __default = 'build';
  const BUILD     = 'build'; //Revision
  const MINOR = 'minor'; //Schema Change
  const MAJOR = 'major'; //Major Change
}
