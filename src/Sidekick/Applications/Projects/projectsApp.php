<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Projects;

class ProjectsApp
{
  public function defaultController()
  {
    return new DefaultController();
  }

  public function name()
  {
    return "Projects";
  }

  public function description()
  {
    return "Projects Manager";
  }
}