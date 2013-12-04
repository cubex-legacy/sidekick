<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\BaseApp\Controllers;

class ProjectAwareBaseControl extends BaseControl
{
  /**
   * @return \Sidekick\Applications\BaseApp\ProjectAwareApplication
   */
  public function application()
  {
    return $this->_application;
  }

  public function getProjectId()
  {
    return $this->application()->getProjectId();
  }

  public function preRender()
  {
    parent::preRender();
    $this->addJsBlock("var PROJECTID=" . $this->getProjectId());
  }
}
