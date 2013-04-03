<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Enums;

use Cubex\Type\Enum;

class RepositoryProvider extends Enum
{
  const __default = 'git';
  const GIT       = 'git';
}
