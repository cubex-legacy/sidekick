<?php
/**
 * Author: oke.ugwu
 * Date: 11/06/13 11:00
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Facade\Redirect;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Components\Fortify\Mappers\BuildsProjects;
use Sidekick\Components\Projects\Mappers\Project;

class FortifyRepositoryController extends BaseControl
{

  public function renderCreate()
  {
    $postData                 = $this->request()->postVariables();
    $buildRepo                = new BuildsProjects();
    $buildRepo->projectId     = $postData['projectId'];
    $buildRepo->buildId       = $postData['buildId'];
    $buildRepo->buildSourceId = $postData['repository'];

    $buildRepo->saveChanges();

    Redirect::to(
      '/fortify/' . $postData['projectId'] . '/' .
      $postData['buildId'] . '/repository'
    )->now();
  }

  public function getRoutes()
  {
    return [
      '/create' => 'create',
    ];
  }
}
