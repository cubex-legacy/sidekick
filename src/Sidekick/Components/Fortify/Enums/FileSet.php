<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Enums;

use Cubex\Type\Enum;

class FileSet extends Enum
{
  const __default = 'none';
  const NONE      = 'none';
  const ALL       = 'all';
  const MODIFIED  = 'modified';
}
