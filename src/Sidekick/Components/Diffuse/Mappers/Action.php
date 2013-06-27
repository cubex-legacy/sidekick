<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Action extends RecordMapper
{
  public $versionId;
  public $userId;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Enums\ActionType
   */
  public $actionType;
  /**
   * @datatype text
   */
  public $comment;
}
