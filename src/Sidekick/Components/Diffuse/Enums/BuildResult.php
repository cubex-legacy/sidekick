<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Enums;

use Cubex\Type\Enum;

class BuildResult extends Enum
{
  const __default = 'fail';
  const FAIL      = 'fail';
  const PASS      = 'pass';
  const RUNNING   = 'running';
  const ERROR     = 'error';
}
