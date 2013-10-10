<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Evento\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Evento\Enums\Resolution;

class EventUpdate extends RecordMapper
{
  public $userId;
  public $description;
  /**
   * @enumclass \Sidekick\Components\Evento\Enums\Resolution
   */
  public $resolution = Resolution::ONGOING;
}
