<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Phuse;

use Cubex\Foundation\Config\Config;

class PhuseHelper
{
  public static function getArchiveDir(Config $config)
  {
    return $config->getStr(
      "compiled",
      (dirname(WEB_ROOT) . DS . 'phuse' . DS . 'archive' . DS)
    );
  }

  public static function safePackageName($package)
  {
    return str_replace('/', '_', $package);
  }
}
