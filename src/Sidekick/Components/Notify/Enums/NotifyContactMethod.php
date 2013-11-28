<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Enums;

use Cubex\Type\Enum;

class NotifyContactMethod extends Enum
{
  const __default = '\Sidekick\Components\Notify\Notifiers\Email';
  const EMAIL     = '\Sidekick\Components\Notify\Notifiers\Email';
  const SMS       = '\Sidekick\Components\Notify\Notifiers\SMS';
  const JABBER    = '\Sidekick\Components\Notify\Notifiers\Jabber';
  const PUSH      = '\Sidekick\Components\Notify\Notifiers\Push';
}
