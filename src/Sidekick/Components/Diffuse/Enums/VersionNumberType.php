<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 15:19
 */

namespace Sidekick\Components\Diffuse\Enums;

use Cubex\Type\Enum;

class VersionNumberType extends Enum
{
  const __default = 'revision';
  const MAJOR     = 'major';
  const MINOR     = 'minor';
  const BUILD     = 'build';
  const REVISION  = 'revision';
}
