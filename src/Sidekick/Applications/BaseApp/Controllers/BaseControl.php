<?php
/**
 * @author: brooke.bryan
 *        Application: BaseApp
 */
namespace Sidekick\Applications\BaseApp\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Cubex\Facade\Redirect;
use Sidekick\Applications\BaseApp\Views\Header;
use Sidekick\Applications\BaseApp\Views\Sidebar;

class BaseControl extends WebpageController
{
  protected $_titlePrefix;

  public function canProcess()
  {
    if(!\Auth::loggedIn())
    {
      Redirect::to('/')->now();
    }
    return true;
  }

  public function preRender()
  {
    $this->setTitle("");
    $this->requireCss('/base');
    $this->requireJs("/hoverdrop");

    $this->tryNest("sidebar", $this->getSidebar());
    $this->tryNest("header", $this->getHeader());
  }

  public function setTitle($title = '')
  {
    if(empty($title) && !empty($this->_titlePrefix))
    {
      parent::setTitle($this->_titlePrefix);
    }
    else if(empty($this->_titlePrefix))
    {
      parent::setTitle("Sidekick");
    }
    else
    {
      parent::setTitle(implode(' - ', [$this->_titlePrefix, $title]));
    }
  }

  public function getSidebar()
  {
    $project = $this->application()->project();
    /**
     * @var $project \Sidekick\Project
     */
    return new Sidebar($project);
  }

  public function getHeader()
  {
    $project = $this->application()->project();
    /**
     * @var $project \Sidekick\Project
     */
    return new Header($project);
  }

  public function renderIndex()
  {
    echo "Hey";
  }

  public function defaultAction()
  {
    return "index";
  }
}
