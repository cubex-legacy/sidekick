<?php
/**
 * Author: oke.ugwu
 * Date: 29/11/13 16:03
 */

namespace Sidekick\Components\Notify\Filters;

class FilterEquals extends AbstractFilter
{
  protected function _validate($input)
  {
    return $this->getValue() === $input;
  }
}
