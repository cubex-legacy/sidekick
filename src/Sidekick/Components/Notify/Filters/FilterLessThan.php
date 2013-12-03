<?php
/**
 * Author: oke.ugwu
 * Date: 29/11/13 16:09
 */

namespace Sidekick\Components\Notify\Filters;

class FilterLessThan extends AbstractFilter
{
  protected function _validate($input)
  {
    return $this->getValue() < $input;
  }
}
