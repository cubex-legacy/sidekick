<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

/**
 * Class Release
 * @unique versionId,platformId
 */
class Release extends RecordMapper
{
  public $versionId;
  public $platformId;

  public static function locate($versionId, $platformId)
  {
    return self::collection(
             [
             'version_id'  => $versionId,
             'platform_id' => $platformId
             ]
           )->first();
  }

  public function pushes()
  {
    return $this->hasMany(new ReleasePush());
  }
}
