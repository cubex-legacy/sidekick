<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Enums;

use Cubex\Type\Enum;

class ApprovalState extends Enum
{
  const __default = 'unknown';
  const UNKNOWN   = 'unknown';
  const PENDING   = 'pending';
  const REVIEW    = 'review';
  const APPROVED  = 'approved';
  const REJECTED  = 'rejected';
}
