<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Users;

class UserHelper
{
  public static function passwordVerify($input, $hash)
  {
    if(md5($input) === $hash)
    {
      return true;
    }
    else
    {
      return false;
    }
  }
}
