<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Evento\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Evento\Enums\Resolution;
use Sidekick\Components\Users\Mappers\User;

class EventUpdate extends RecordMapper
{
  public $eventId;
  public $userId;
  public $description;
  /**
   * @enumclass \Sidekick\Components\Evento\Enums\Resolution
   */
  public $resolution = Resolution::ONGOING;

  public function user()
  {
    return new User($this->userId);
  }
}
