<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Enums;


use Cubex\Type\Enum;

class EventType extends Enum
{
  const __default = "System";
  const SYSTEM = "System";
  const USER = "User";
}
