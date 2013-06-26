<?php
/**
 * Author: oke.ugwu
 * Date: 24/06/13 09:47
 */

namespace Sidekick\Applications\Fortify\Reports\PhpUnit;

class PhpUnitTestSummary
{
  public $name;
  public $tests;
  public $assertions;
  public $failures;
  public $errors;
  public $time;

  /**
   * @param mixed $assertions
   */
  public function setAssertions($assertions)
  {
    $this->assertions = $assertions;
  }

  /**
   * @param mixed $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }

  /**
   * @param mixed $failures
   */
  public function setFailures($failures)
  {
    $this->failures = $failures;
  }

  /**
   * @param mixed $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @param mixed $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
}
