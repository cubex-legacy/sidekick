<?php
/**
 * Author: oke.ugwu
 * Date: 17/07/13 15:39
 */

namespace Sidekick\Applications\Docs\Controllers;

use Cubex\Core\Http\Response;
use Cubex\Foundation\Container;
use Cubex\View\HtmlElement;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedView;
use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Projects\Mappers\Project;

class DefaultController extends BaseControl
{
  public function getSidebar()
  {
    $projects    = Project::collection()->loadAll()->setOrderBy('name');
    $sidebarMenu = [];
    foreach($projects as $project)
    {
      $sidebarMenu[$this->baseUri() . '/' . $project->id()] = $project->name;
    }

    return new Sidebar($this->request()->path(2), $sidebarMenu);
  }

  public function renderIndex()
  {
    $projectId = $this->getInt('projectId');
    $versions  = Version::collection(['project_id' => $projectId]);

    if($versions->hasMappers())
    {
      $versionList = new Partial('<li><a href="%s">v%s</a></li>');
      foreach($versions as $version)
      {
        $docIndex = realpath(
          WEB_ROOT . '/../docs/' . $version->id() . '/index.html'
        );

        //only show versions we can find doc index for.
        //this assumes there is documentation if index.html exists in the right
        //directory
        if(file_exists($docIndex))
        {
          $versionList->addElement(
            $this->baseUri() . '/' . $projectId . '/' . $version->id(
            ) . '/view',
            VersionHelper::getVersionString($version)
          );
        }
      }
    }
    else
    {
      $versionList = "<p>No Documentation. No Versions exist for this Project.</p>";
    }

    return new RenderGroup(
      '<h1>Documentation By Version</h1>',
      $versionList
    );
  }

  public function renderDocview()
  {
    $projectId = $this->getInt('projectId');
    $versionId = $this->getInt('versionId');
    return new HtmlElement(
      'iframe',
      [
      'src'   => "/docs/$projectId/$versionId/",
      'style' => 'width:100%; height:800px; border:0;'
      ]
    );
  }

  public function renderDoc()
  {
    $versionId = $this->getInt('versionId');
    $file      = $this->getStr('file', 'index.html');
    $pathInfo  = pathinfo($file);

    if($pathInfo['dirname'] == '.')
    {
      $pathInfo['dirname'] = '';
    }
    $dir  = realpath(
      WEB_ROOT . '/../docs/' . $versionId . '/' . $pathInfo['dirname']
    );
    $ext  = $pathInfo['extension'];
    $file = $pathInfo['filename'];

    $doc = new TemplatedView($file, $this);
    $doc->setDirectory($dir);
    $doc->setExtension($ext);
    $response = new Response($doc);

    $type = [
      'css'  => 'text/css',
      'js'   => 'text/js',
      'jpg'  => 'image/jpg',
      'png'  => 'image/png',
      'html' => 'text/html'
    ];

    $response->addHeader('Content-Type', $type[$ext]);
    return $response;
  }

  public function getRoutes()
  {
    return [
      ':projectId/:versionId/view'      => 'docview',
      ':projectId/:versionId/'          => 'doc',
      ':projectId/:versionId/:file@all' => 'doc',
      ':projectId/'                     => 'index',
    ];
  }
}
