<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects;

use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use Sidekick\Applications\Diffuse\Views\Projects\OverviewView;

class OverviewController extends DiffuseController
{
  public function renderIndex()
  {
    return new OverviewView();
  }
}
