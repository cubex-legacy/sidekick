<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Type\Enum;

class PushType extends Enum
{
  const __default               = 'doubleauth';
  const SINGLE_AUTH             = 'singleauth';
  const MANAGER_ONLY            = 'manageronly';
  const DOUBLE_AUTH_OR_MANAGER  = 'doubleauth';
  const DOUBLE_AUTH_INC_MANAGER = 'doubleauthwithmgr';
}
