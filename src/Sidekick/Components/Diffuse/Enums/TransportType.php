<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Enums;

use Cubex\Type\Enum;

class TransportType extends Enum
{
  const __default         = 'phuse';
  const SFTP              = 'sftp';
  const FTP               = 'ftp';
  const GOOGLE_APP_ENGINE = 'gae';
  const PHUSE             = 'phuse';
  const RSYNC             = 'rsync';
}
