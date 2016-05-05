<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick;

use Bundl\Debugger\DebuggerBundle;
use Bundl\DebugToolbar\DebugToolbarBundl;
use Cubex\Core\Application\Application;
use Cubex\Core\Http\Request;
use Cubex\Facade\Auth;
use Sidekick\Applications\Api\ApiApp;
use Sidekick\Applications\BaseApp\ProjectAwareApplication;
use Sidekick\Applications\Configurator\ConfiguratorApp;
use Sidekick\Applications\Diffuse\DiffuseApp;
use Sidekick\Applications\Dispatcher\DispatcherApp;
use Sidekick\Applications\Docs\DocsApp;
use Sidekick\Applications\Evento\EventoApp;
use Sidekick\Applications\Fortify\FortifyApp;
use Sidekick\Applications\Login\LoginApp;
use Sidekick\Applications\Notify\NotifyApp;
use Sidekick\Applications\Overview\OverviewApp;
use Sidekick\Applications\Phuse\PhuseApp;
use Sidekick\Applications\PreviewApp\PreviewApp;
use Sidekick\Applications\Projects\ProjectsApp;
use Sidekick\Applications\Repository\RepositoryApp;
use Sidekick\Applications\Rosetta\RosettaApp;
use Sidekick\Applications\Scripture\ScriptureApp;
use Sidekick\Applications\SourceCode\SourceCodeApp;
use Sidekick\Applications\Users\UsersApp;

class Project extends \Cubex\Core\Project\Project
{
  protected $_apps;
  protected $_projectId;

  protected function _configure()
  {
    if(!CUBEX_CLI)
    {
      $this->_attemptProject();
    }

    $this->addApplication('phuse', new PhuseApp());
    $this->addApplication('projects', new ProjectsApp());
    $this->addApplication('fortify', new FortifyApp());
    $this->addApplication('diffuse', new DiffuseApp());
    $this->addApplication('users', new UsersApp());
    $this->addApplication('rosetta', new RosettaApp());
  }

  public function getProjectId()
  {
    return $this->_projectId;
  }

  protected function _attemptProject()
  {
    $tryProject = $this->request()->path(1);
    if(starts_with($tryProject, '/P'))
    {
      $projectId = (int)substr($tryProject, 2);
      if($projectId > 0)
      {
        $this->_projectId = $projectId;
      }
    }
  }

  public function getBundles()
  {
    //return [new DebuggerBundle()];
    return [
      //new DebugToolbarBundl()
    ];
  }

  public function getApplication(Request $req)
  {
    if((isset($_SERVER['HTTP_X_PURPOSE'])
    && $_SERVER['HTTP_X_PURPOSE'] == 'preview')
    || $req->path() == '/preview'
    )
    {
      return new PreviewApp();
    }

    return parent::getApplication($req);
  }

  public function addApplication($path, Application $application)
  {
    if($application instanceof ProjectAwareApplication)
    {
      //Do not allow projectaware applications to run without a project selected
      if($this->_projectId < 1)
      {
        return $this;
      }
      $path = 'P' . $this->_projectId . '/' . $path;
    }

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
    if($subdomain == 'phuse' || $subdomain == 'phuse.sidekick')
    {
      return new PhuseApp(true);
    }
    else if($subdomain == 'api' || $subdomain == 'api.sidekick')
    {
      return new ApiApp();
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
    if(Auth::loggedin())
    {
      return new OverviewApp();
    }
    else
    {
      return new LoginApp();
    }
  }
}
