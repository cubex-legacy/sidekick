<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Evento\Enums;

use Cubex\Type\Enum;

class Resolution extends Enum
{
  const __default = 'ongoing';
  const ONGOING   = 'ongoing';
  const RESOLVED  = 'resolved';
  const CLOSED    = 'closed';
}
