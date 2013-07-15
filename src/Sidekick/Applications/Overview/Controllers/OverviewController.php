<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Overview\Controllers;

use Cubex\Facade\Redirect;
use Cubex\View\TemplatedView;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Overview\Views\Releases;

class OverviewController extends BaseControl
{
  public function renderIndex()
  {
    return new TemplatedView('Homepage', $this);
  }

  public function renderReleases()
  {
    return new Releases();
  }

  public function logout()
  {
    \Auth::logout();
    Redirect::to('/')->now();
  }

  public function getRoutes()
  {
    return [
      '/releases' => 'releases',
      'logout'    => 'logout'
    ];
  }
}
