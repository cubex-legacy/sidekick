<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify;

class FortifyHelper
{
  public static function buildPath($buildId)
  {
    return dirname(WEB_ROOT) . '/builds/' . $buildId;
  }
}
