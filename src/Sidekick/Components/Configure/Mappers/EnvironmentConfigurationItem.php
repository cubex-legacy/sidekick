<?php
/**
 * @author  oke.ugwu 
 */

namespace Sidekick\Components\Configure\Mappers;


use Cubex\Data\Attribute;
use Cubex\Mapper\Database\RecordMapper;

class EnvironmentConfigurationItem extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;
  protected $_autoTimestamp = false;

  public $environmentId;
  public $projectId;
  public $configurationItemId;
  public $customItemId;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      array(
           "projectId",
           "environmentId",
           "configurationItemId"
      )
    );
  }
}