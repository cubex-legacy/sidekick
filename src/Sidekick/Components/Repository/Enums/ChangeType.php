<?php
namespace Sidekick\Components\Repository\Enums;

use Cubex\Type\Enum;

class ChangeType extends Enum
{
  const __default      = 'X';
  const ADDED          = 'A';
  const COPIED         = 'C';
  const DELETED        = 'D';
  const MODIFIED       = 'M';
  const RENAMED        = 'R';
  const MODE_CHANGED   = 'T';
  const UNMERGED       = 'U';
  const UNKNOWN        = 'X';
  const BROKEN_PAIRING = 'B';
}
