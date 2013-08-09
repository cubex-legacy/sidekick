<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Core\Http\Response;
use Cubex\Form\Form;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
Use Sidekick\Applications\Diffuse\Views\HomePage;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Projects\Mappers\ProjectUser;

class DefaultController extends DiffuseController
{
  public function renderIndex()
  {
    $state    = $this->_request->getVariables("stateSelect");
    $platform = $this->_request->getVariables("allPlatforms");
    return $this->createView(
      new HomePage($state, ($platform != null) ? true : false)
    );
  }

  /*
   * Creates a stdclass for each version, with members corresponding to the version details
   * States are a nested stdclass (see getStatesForVersion)
   *
   * Returns a json response (from the stdclass) with the matching versions
   */
  public function ajaxIndex()
  {
    $requestedState = $this->_request->postVariables("state");
    $allPlatforms   = ($this->_request->postVariables(
      "allplatforms"
    ) == "true") ? true : false;
    $versions       = Version::collection()->loadAll();
    $json           = new \StdClass;
    foreach($versions as $version)
    {

      $versionObj            = new \StdClass;
      $project               = new Project($version->projectId);
      $versionObj->projectid = $version->projectId;
      $versionObj->project   = $project->name;
      $versionObj->version   = $version->major . "." . $version->minor . "." . $version->build;
      $versionObj->type      = $version->type;
      $versionObj->states    = $this->getStatesForVersion(
        $version->id,
        $requestedState,
        $allPlatforms
      );
      if($versionObj->states == null)
      {
        continue;
      }
      $versionObj->updated  = date("d/M/Y", strtotime($version->updatedAt));
      $json->{$version->id} = $versionObj;
    }
    return new Response($json);
  }

  /*
     * Creates a stdclass with the states as members, e.g.
     * "dev" => "approved"
     * "stage" => "review"
     *
     * Returns null if none of the states match $requestedState
     * Returns null if $allPlatforms is true and not all of the states equal $requestedState
     * Returns the stdclass otherwise
     */
  public function getStatesForVersion(
    $versionId, $requestedState, $allPlatforms
  )
  {
    $states                = PlatformVersionState::collection()->loadWhere(
      ["version_id" => $versionId]
    );
    $stateObj              = new \StdClass;
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
      $platform                    = new Platform($state->platformId);
      $stateObj->{$platform->name} = $state->state;
    }
    if(!$hasRequestedState || ($allPlatforms && !$hasAllRequestedStates))
    {
      return null;
    }
    return $stateObj;
  }
}
