<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\BaseApp;

class ProjectAwareApplication extends SidekickApplication
{
  public function getProjectId()
  {
    return $this->project()->getProjectId();
  }

  /**
   * @return \Sidekick\Project
   */
  public function project()
  {
    return parent::project();
  }
}
