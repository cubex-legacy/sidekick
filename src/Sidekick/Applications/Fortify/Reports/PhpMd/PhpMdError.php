<?php
/**
 * Author: oke.ugwu
 * Date: 21/06/13 09:50
 */

namespace Sidekick\Applications\Fortify\Reports\PhpMd;

class PhpMdError
{
  public $fileName;
  public $message;

  public function __construct($fileName, $message)
  {
    $this->fileName = $fileName;
    $this->message  = $message;
  }

  /**
   * @param mixed $fileName
   *
   * @return PhpMdError
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
    return $this;
  }

  /**
   * @param $message
   *
   * @return $this
   */
  public function setMesage($message)
  {
    $this->message = $message;
    return $this;
  }
}
