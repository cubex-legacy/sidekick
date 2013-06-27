<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Platform extends RecordMapper
{
  public $name;
  public $description;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Enums\TransportType
   */
  public $transportType;
  public $configuration = [];
  /**
   * Build IDs required to pass before can process upload
   * (builds must cover every commit contained in version)
   */
  public $requiredBuilds = [];
  /**
   * @var bool
   * Require approval before upload can commence
   */
  public $requireApproval = true;

  protected function _configure()
  {
    $this->_setSerializer("configuration");
    $this->_setSerializer("requiredBuilds");
  }
}
