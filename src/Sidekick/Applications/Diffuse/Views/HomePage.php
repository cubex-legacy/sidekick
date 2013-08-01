<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 17/07/13
 * Time: 17:14
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Projects\Mappers\Project;

class HomePage extends TemplatedViewModel
{
  protected $_requestedState;
  protected $_allPlatforms;

  public function __construct($requestedstate = "", $allplatforms = false)
  {
    $this->_requestedState = $requestedstate;
    $this->_allPlatforms   = $allplatforms;
    $this->requireJsLibrary("jquery");
    //For ajax
    $this->requireJs("versionTable");
  }

  public function getStates()
  {
    $reflect = new \ReflectionClass(new VersionState);
    return $reflect->getConstants();
  }

  /*
   * Creates a stdclass for each matching version, and stores them in an array
   * States are a keyed array (see getStatesForVersion)
   *
   * Returns the array (of stdclass versions)
   */
  public function getMatchingVersions()
  {
    $versions         = Version::collection()->loadAll();
    $matchingVersions = [];
    foreach($versions as $version)
    {
      $versionObj            = new \StdClass;
      $project               = new Project($version->projectId);
      $versionObj->projectid = $version->projectId;
      $versionObj->project   = $project->name;
      $versionObj->versionid = $version->id;
      $versionObj->version   = $version->major . "." . $version->minor . "." . $version->build;
      $versionObj->type      = $version->type;
      $versionObj->states    = $this->getStatesForVersion(
        $version->id,
        $this->_requestedState,
        $this->_allPlatforms
      );
      if($versionObj->states == null)
      {
        continue;
      }
      $versionObj->updated = date("d/M/Y", strtotime($version->updatedAt));
      $matchingVersions[]  = $versionObj;
    }
    return $matchingVersions;
  }

  /*
   * Creates a keyed array with the states, e.g.
   * "dev" => "approved"
   * "stage" => "review"
   *
   * Returns null if none of the states match $requestedState
   * Returns null if $allPlatforms is true and not all of the states equal $requestedState
   * Returns the array otherwise
   */
  public function getStatesForVersion(
    $versionId, $requestedState, $allPlatforms
  )
  {
    $states                = PlatformVersionState::collection()->loadWhere(
      ["version_id" => $versionId]
    );
    $stateObj              = [];
    $hasRequestedState     = false;
    $hasAllRequestedStates = true;
    foreach($states as $state)
    {
      if($state->state == $requestedState)
      {
        $hasRequestedState = true;
      }
      else
      {
        $hasAllRequestedStates = false;
      }
      $platform                  = new Platform($state->platformId);
      $stateObj[$platform->name] = $state->state;
    }
    if(!$hasRequestedState || ($allPlatforms && !$hasAllRequestedStates))
    {
      return null;
    }
    return $stateObj;
  }
}
