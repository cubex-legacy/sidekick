<?php
/**
 * @author  brooke.bryan
 */
namespace Sidekick\Components\Servers\Enums;

use Cubex\Type\Enum;

class ConnectType extends Enum
{
  const __default = 'hostname';
  const HOSTNAME  = 'hostname';
  const IPV4      = 'ipv4';
  const IPV6      = 'ipv6';
  const IP        = 'ip';
}
