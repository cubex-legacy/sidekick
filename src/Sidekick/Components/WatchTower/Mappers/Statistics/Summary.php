<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower\Mappers\Statistics;

use Cubex\Cassandra\CassandraMapper;

class Summary extends CassandraMapper
{
  public static function storeValue($checkId, $property, $value)
  {
    $prefix = $checkId . '-' . $property . '-';

    self::cf()->openBatch();

    //Per minute statistics
    $daily = new self();
    $daily->setId($prefix . date("Y-m-d"));
    $daily->setData(date("H:i"), $value);
    $daily->saveChanges();

    //15 Minute Statistics
    $monthly = new self();
    $monthly->setId($prefix . date("Y-m"));
    $monthly->setData(
      date("H:i", $monthly->_makeIntervalTime(time(), 15)),
      $value
    );
    $monthly->saveChanges();

    //Hourly Statistics
    $annual = new self();
    $annual->setId($prefix . date("Y"));
    $annual->setData(
      date("H:i", $annual->_makeIntervalTime(time(), 60)),
      $value
    );
    $annual->saveChanges();

    self::cf()->closeBatch();
  }

  protected function _makeIntervalTime($time, $intervalMinutes)
  {
    return floor($time / ($intervalMinutes * 60)) * ($intervalMinutes * 60);
  }
}
