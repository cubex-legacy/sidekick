<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class BuildLog extends RecordMapper
{
  public $buildId;
  public $commandId;
  /**
   * @datatype smallint
   */
  public $exitCode;
  /**
   * @datatype MediumText
   */
  public $output;
  /**
   * @datatype MediumText
   */
  public $errorOut;

  public function writeBuffer($type, $buffer)
  {
    if('err' === $type)
    {
      $this->errorOut .= $buffer;
    }
    else
    {
      $this->output .= $buffer;
    }
    $this->saveChanges();
  }
}
