<?php
/**
 * Author: oke.ugwu
 * Date: 21/10/13 12:43
 */

namespace Sidekick\Components\Repository\Helpers;

class DiffusionHelper
{
  /**
   * @param $repo \Sidekick\Components\Repository\Mappers\Source
   *
   * @return array|null
   */
  public static function diffusionUrlCallsign($repo)
  {
    if(empty($repo->diffusionBaseUri))
    {
      return null;
    }
    else
    {
      $uriParts = explode('/', trim($repo->diffusionBaseUri, '/'));
      $callsign = end($uriParts);
      return [$repo->diffusionBaseUri, $callsign];
    }
  }
}
