<?php
/**
 * Author: oke.ugwu
 * Date: 21/06/13 09:25
 */

namespace Sidekick\Applications\Fortify\Reports\PhpMd;

class PhpMdFile
{
  public $fileName;
  public $beginLine;
  public $endLine;
  public $rule;
  public $ruleSet;
  public $package;
  public $class;
  public $method;
  public $priority;
  public $externalInfoUrl;

  /**
   * @param mixed $beginLine
   *
   * @return PhpMdFile
   */
  public function setBeginLine($beginLine)
  {
    $this->beginLine = $beginLine;
    return $this;
  }

  /**
   * @param mixed $rule
   *
   * @return PhpMdFile
   */
  public function setRule($rule)
  {
    $this->rule = $rule;
    return $this;
  }

  /**
   * @param mixed $class
   *
   * @return PhpMdFile
   */
  public function setClass($class)
  {
    $this->class = $class;
    return $this;
  }

  /**
   * @param mixed $endLine
   *
   * @return PhpMdFile
   */
  public function setEndLine($endLine)
  {
    $this->endLine = $endLine;
    return $this;
  }

  /**
   * @param mixed $externalInfoUrl
   *
   * @return PhpMdFile
   */
  public function setExternalInfoUrl($externalInfoUrl)
  {
    $this->externalInfoUrl = $externalInfoUrl;
    return $this;
  }

  /**
   * @param mixed $fileName
   *
   * @return PhpMdFile
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
    return $this;
  }

  /**
   * @param mixed $method
   *
   * @return PhpMdFile
   */
  public function setMethod($method)
  {
    $this->method = $method;
    return $this;
  }

  /**
   * @param mixed $package
   *
   * @return PhpMdFile
   */
  public function setPackage($package)
  {
    $this->package = $package;
    return $this;
  }

  /**
   * @param mixed $priority
   *
   * @return PhpMdFile
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
    return $this;
  }

  /**
   * @param mixed $ruleSet
   *
   * @return PhpMdFile
   */
  public function setRuleSet($ruleSet)
  {
    $this->ruleSet = $ruleSet;
    return $this;
  }
}
