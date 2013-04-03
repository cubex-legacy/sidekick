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
}
