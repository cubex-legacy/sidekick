<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Mappers;

use Cubex\Type\Enum;

class VersionState extends Enum
{
  const __default = 'unknown';
  const UNKNOWN   = 'unknown';
  const PASSED    = 'passed';
  const FAILED    = 'failed';
  const PENDING   = 'pending';
  const REVIEWING = 'reviewing';
}
