<?php
/**
 * Author: oke.ugwu
 * Date: 29/11/13 16:03
 */

namespace Sidekick\Components\Notify\Filters;

abstract class AbstractFilter
{
  protected $_name;
  protected $_options;
  protected $_value;

  public final function __construct($name, $options)
  {
    $this->_name    = $name;
    $this->_options = $options;
  }

  public function getName()
  {
    return $this->_name;
  }

  public function getValue()
  {
    return $this->_value;
  }

  public function setValue($value)
  {
    //all the time this method is called is just before filter is stored
    //in db, so it is safe to unset options here
    unset($this->_options);
    $this->_value = $value;
  }

  public function getOptions()
  {
    return $this->_options;
  }

  public function validate($input)
  {
    return $this->_validate($input);
  }

  abstract protected function _validate($input);
}
