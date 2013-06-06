<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 13/05/13
 * Time: 16:02
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Configurator\Controllers;

use Cubex\Facade\Redirect;
use Sidekick\Applications\Configurator\Views\EnvironmentList;
use Sidekick\Components\Configure\Mappers\Environment;

class EnvironmentsController extends ConfiguratorController
{

  public function preRender()
  {
    parent::preRender();
    $this->requireCssPackage('environments');
    $this->requireJs('environment');
  }

  public function renderIndex()
  {
    $envs = Environment::collection()->loadAll();
    return $this->createView(new EnvironmentList($envs));
  }

  public function update()
  {
    $id    = $this->getInt('id');
    $value = $this->getStr('value');

    $env           = new Environment($id);
    $env->name     = $value;
    $env->filename = strtolower(str_replace(' ', '_', $value)) . '.ini';
    $env->saveChanges();

    die(json_encode($env));
  }

  public function add()
  {
    $value         = $this->getStr('value');
    $env           = new Environment();
    $env->name     = $value;
    $env->filename = strtolower(str_replace(' ', '_', $value)) . '.ini';
    $env->saveChanges();
    die($value);
  }

  public function delete()
  {
    $id  = $this->getInt('id');
    $env = new Environment($id);
    $env->delete();

    Redirect::to($this->baseUri())->now();
  }

  public function getRoutes()
  {
    return array(
      '/'                  => 'index',
      '/add/:value'        => 'add',
      '/update/:id/:value' => 'update',
      '/delete/:id'        => 'delete',
    );
  }
}
