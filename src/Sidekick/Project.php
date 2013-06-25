<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick;

use Cubex\Core\Application\Application;
use Cubex\Core\Http\Request;
use Sidekick\Applications\Configurator\ConfiguratorApp;
use Sidekick\Applications\Diffuse\DiffuseApp;
use Sidekick\Applications\Dispatcher\DispatcherApp;
use Sidekick\Applications\Fortify\FortifyApp;
use Sidekick\Applications\Overview\OverviewApp;
use Sidekick\Applications\Phuse\PhuseApp;
use Sidekick\Applications\PreviewApp\PreviewApp;
use Sidekick\Applications\Projects\ProjectsApp;
use Sidekick\Applications\Repository\RepositoryApp;
use Sidekick\Applications\Scripture\ScriptureApp;
use Sidekick\Applications\SourceCode\SourceCodeApp;

class Project extends \Cubex\Core\Project\Project
{
  protected $_apps;

  protected function _configure()
  {
    $this->addApplication('projects', new ProjectsApp());
    $this->addApplication('phuse', new PhuseApp());
    $this->addApplication('repository', new RepositoryApp());
    $this->addApplication('configurator', new ConfiguratorApp());
    $this->addApplication('fortify', new FortifyApp());
    $this->addApplication('diffuse', new DiffuseApp());
    $this->addApplication('dispatcher', new DispatcherApp());
    $this->addApplication('scripture', new ScriptureApp());
  }

  public function getApplication(Request $req)
  {
    if($_SERVER['HTTP_X_PURPOSE'] == 'preview' || $req->path() == '/preview')
    {
      return new PreviewApp();
    }

    return parent::getApplication($req);
  }

  public function addApplication($path, Application $application)
  {
    $this->_apps[$path] = $application;
    return $this;
  }

  /**
   * Project Name
   *
   * @return string
   */
  public function name()
  {
    return "Cubex Sidekick";
  }

  /**
   * @return \Cubex\Core\Application\Application[]
   */
  public function getApplications()
  {
    return $this->_apps;
  }

  public function getBySubDomain($subdomain)
  {
    if($subdomain == 'phuse')
    {
      return new PhuseApp(true);
    }
    return null;
  }

  public function getByPath($path)
  {
    if(starts_with($path, '/overview'))
    {
      $app = new OverviewApp();
      $app->setBaseUri('/' . 'overview');
      return $app;
    }

    if(starts_with($path, '/sourcecode'))
    {
      $app = new SourceCodeApp();
      $app->setBaseUri('/' . 'sourcecode');
      return $app;
    }

    $apps = $this->getApplications();
    foreach($apps as $appPath => $app)
    {
      if(starts_with($path, '/' . $appPath))
      {
        $newApp = new $app();
        if($newApp instanceof Application)
        {
          $newApp->setBaseUri('/' . $appPath);
        }
        return $newApp;
      }
    }
    return null;
  }

  /**
   * @return \Cubex\Core\Application\Application
   */
  public function defaultApplication()
  {
    return new OverviewApp();
  }
}
