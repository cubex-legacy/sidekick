<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Configure\Enums;

use Cubex\Type\Enum;

class ConfigItemType extends Enum
{
  const __default   = 'simple';
  const SIMPLE      = 'simple'; //Simple output
  const MULTI_KEYED = 'multikeyed'; //Keyed array
  const MULTI_ITEM  = 'multiitem'; //Non keyed array
}
