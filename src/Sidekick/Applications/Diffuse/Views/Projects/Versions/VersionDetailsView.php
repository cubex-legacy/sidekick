<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Versions;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;

class VersionDetailsView extends TemplatedViewModel
{
  protected $_version;
  protected $_platforms;
  /**
   * @var RecordCollection|PlatformVersionState[]
   */
  protected $_platformStates;

  public function __construct(Version $version, $platforms, $platformStates)
  {
    $this->_version        = $version;
    $this->_platforms      = $platforms;
    $this->_platformStates = $platformStates;
  }

  public function getPlatforms()
  {
    return $this->_platforms;
  }

  /**
   * @param $platformId
   *
   * @return PlatformVersionState
   */
  public function getPlatformState($platformId)
  {
    return $this->_platformStates->getById(
      [$platformId, $this->_version->id()]
    );
  }

  public function getPlatformStateClass($platformId)
  {
    switch($this->getPlatformState($platformId)->state)
    {
      case VersionState::APPROVED:
        return 'success';
      case VersionState::REJECTED:
        return 'error';
      case VersionState::REVIEW:
        return 'warning';
      default:
        return 'pending';
    }
  }

  public function getPlatformPercentages()
  {
    $perPlatform = floor(100 / count($this->_platforms));
    $result      = [];
    foreach($this->_platforms as $platform)
    {
      /**
       * @var $platform Platform
       */
      $state    = $this->getPlatformState($platform->id());
      $progress = 'none';
      switch($state->state)
      {
        case VersionState::APPROVED;
          $progress = 'success';
          break;
        case VersionState::REJECTED;
          $progress = 'danger';
          break;
        case VersionState::REVIEW;
          $progress = 'warning';
          break;
      }
      $result[] = [$progress, $perPlatform];
    }
    return $result;
  }

  /**
   * @return PlatformVersionState[]
   */
  public function getPlatformStates()
  {
    return $this->_platformStates;
  }

  /**
   * @return Version
   */
  public function getVersion()
  {
    return $this->_version;
  }
}
