<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Scripture\Controllers;

use Cubex\Helpers\Strings;
use Cubex\View\TemplatedView;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Scripture\Views\Scripture;
use Sidekick\Applications\Scripture\Views\ScriptureRepos;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Source;

class ScriptureController extends BaseControl
{
  protected $_titlePrefix = 'Scripture';

  public function renderScripture($id = null, $file = 'README')
  {
    $this->setTitle('Scripture');
    if($id !== null)
    {
      $source = (new Project($id))->repository();
      if($source == null) //Can be null, so will not have ->exists()!
      {
        return new TemplatedView("Failure", $this);
      }
      if($source->exists())
      {
        $this->setTitle(Strings::humanize($source->name));

        $contents = $source->localpath . DS . 'contents.md';

        $filename = $source->localpath . DS . $file . '.md';
        if(!file_exists($filename))
        {
          return new TemplatedView("FileDoesNotExist", $this);
        }

        $sidebar = null;
        if(file_exists($contents))
        {
          $sidebar = file_get_contents($contents);
        }

        $readme = file_get_contents($filename);
        if(!$readme)
        {
          $readme = 'YOU ARE TRYING TO VIEW AN EMPTY FILE';
          $readme .= "\n====";
        }

        return $this->createView(new Scripture($sidebar, $readme));
      }
      else
      {
        return new TemplatedView("InvalidRepo", $this);
      }
    }
    return new TemplatedView("Homepage", $this);
  }

  public function getSidebar()
  {
    return $this->createView(
      new ScriptureRepos(
        Project::collection()->preFetch('repository')->setOrderBy("name"),
        $this->getInt('id')
      )
    );
  }

  public function getRoutes()
  {
    return [
      ''                   => 'scripture',
      '{id}/(?P<file>.*)/' => 'scripture',
    ];
  }
}
