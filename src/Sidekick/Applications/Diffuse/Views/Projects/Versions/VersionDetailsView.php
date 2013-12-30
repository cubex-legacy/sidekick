<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Versions;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Enums\ApprovalState;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Repository\Mappers\Commit;

class VersionDetailsView extends TemplatedViewModel
{
  protected $_version;
  protected $_platforms;
  /**
   * @var RecordCollection|PlatformVersionState[]
   */
  protected $_platformStates;
  /**
   * @var RecordCollection|Commit[]
   */
  protected $_commits;
  protected $_rejectButton = false;

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

  public function setCommits($commits)
  {
    $this->_commits = $commits;
    return $this;
  }

  public function addCommit(Commit $commit)
  {
    $this->_commits[] = $commit;
    return $this;
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
      case ApprovalState::APPROVED:
        return 'success';
      case ApprovalState::REJECTED:
        return 'error';
      case ApprovalState::REVIEW:
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
        case ApprovalState::APPROVED;
          $progress = 'success';
          break;
        case ApprovalState::REJECTED;
          $progress = 'danger';
          break;
        case ApprovalState::REVIEW;
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

  public function enableRejectButton()
  {
    $this->_rejectButton = true;
  }

  public function disableRejectButton()
  {
    $this->_rejectButton = false;
  }

  public function rejectButtonStatus()
  {
    return (bool)$this->_rejectButton;
  }
}
