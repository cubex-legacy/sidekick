<?php
/**
 * Author: oke.ugwu
 * Date: 21/06/13 11:17
 */

namespace Sidekick\Applications\Fortify\Reports;

class PhpCsFile
{
  public $fileName;
  /**
   * @var PhpCsError[]
   */
  public $errors;

  public function __construct($fileName, $errors)
  {
    $this->fileName = $fileName;
    $this->errors   = $errors;
  }
}
