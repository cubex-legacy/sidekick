<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 06/06/13
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Routing\StdRoute;
use Sidekick\Components\Fortify\Mappers\BuildsCommands;

class FortifyBuildCommandsController extends FortifyController
{

  public function renderIndex()
  {
    //todo do something meaningful here
  }

  public function renderCreate()
  {
    $postData = $this->request()->postVariables();

    $buildCommand               = new BuildsCommands();
    $buildCommand->commandId    = $postData['commandId'];
    $buildCommand->buildId      = $postData['buildId'];
    $buildCommand->dependencies = $postData['dependencies'];
    $buildCommand->saveChanges();

    //add dependencies to build commands
    foreach($postData['dependencies'] as $depCommandId)
    {
      $depBuildCommand            = new BuildsCommands();
      $depBuildCommand->commandId = $depCommandId;
      $depBuildCommand->buildId   = $postData['buildId'];
      $depBuildCommand->saveChanges();
    }

    Redirect::to('/fortify/builds/' . $postData['buildId'] . '/edit')->now();
  }

  public function renderUpdate()
  {
    //todo at some point, when it becomes an important feature :)
  }

  public function renderDelete()
  {
    $commandId = $this->getInt('commandId');
    $buildId   = $this->getInt('buildId');

    $buildCommand = new BuildsCommands([$buildId, $commandId]);
    $buildCommand->delete();

    Redirect::to('/fortify/builds/' . $buildId . '/edit')->now();
  }

  public function getRoutes()
  {
    //extending ResourceTemplate routes
    $routes = parent::getRoutes();

    //put overrides on top of routes so they take priority
    array_unshift(
      $routes,
      new StdRoute('/create', 'create'),
      new StdRoute('/:commandId/:buildId/delete', 'delete')
    );

    return $routes;
  }
}
