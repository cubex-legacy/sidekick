<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Configure;

class ConfigWriter
{
  public function buildIni($inputArray, $hasSections = false)
  {
    $content = "";
    if(!$hasSections)
    {
      $inputArray = [null => $inputArray];
    }

    foreach($inputArray as $key => $elem)
    {
      if($key !== null)
      {
        $content .= "[" . $key . "]" . PHP_EOL;
      }
      foreach($elem as $keyTwo => $elemTwo)
      {
        if(is_array($elemTwo))
        {
          foreach($elemTwo as $k => $v)
          {
            //handle boolean values, so it prints nicely
            $v = (is_bool($v) ? ($v ? 'true' : 'false') : $v);
            if(is_int($k))
            {
              $content .= $keyTwo . "[] =  $v " . PHP_EOL;
            }
            else
            {
              $content .= $keyTwo . "[$k] = $v " . PHP_EOL;
            }
          }
        }
        else if($elemTwo == "")
        {
          $content .= $keyTwo . " = " . PHP_EOL;
        }
        else
        {
          $content .= $keyTwo . " = $elemTwo " . PHP_EOL;
        }
      }
      $content .= PHP_EOL;
    }

    return $content;
  }
}
