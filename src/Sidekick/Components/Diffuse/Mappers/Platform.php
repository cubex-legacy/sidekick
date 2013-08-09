<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Helpers\DependencyArray;
use Cubex\Mapper\Database\RecordMapper;

/**
 * e.g. Live Stage, Live Prod, Dev Stage, Dev Prod
 */
class Platform extends RecordMapper
{
  public $name;
  public $description;

  /**
   * Build IDs required to pass before can process upload
   * (builds must cover every commit contained in version)
   */
  public $requiredBuilds = [];
  public $requiredPlatforms = [];

  protected function _configure()
  {
    $this->_setSerializer("requiredBuilds");
    $this->_setSerializer("requiredPlatforms");
  }

  public static function orderedCollection()
  {
    $collection = static::collection();
    $order      = new DependencyArray();
    foreach($collection as $platform)
    {
      /**
       * @var $platform self
       */
      $order->add($platform->id(), $platform->requiredPlatforms);
    }
    $collection->orderByKeys($order->getLoadOrder());
    return $collection;
  }
}
