<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Enums;

use Cubex\Type\Enum;

class BuildStatus extends Enum
{
  const __default = 'pending';
  const PENDING   = 'pending';
  const RUNNING   = 'running';
  const FAILED    = 'failure';
  const SUCCESS   = 'success';
  const UNKNOWN   = 'unknown';
}
