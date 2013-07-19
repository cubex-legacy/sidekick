<?php
/**
 * Author: oke.ugwu
 * Date: 15/07/13 09:55
 */

namespace Sidekick\Applications\Login\Controllers;

use Cubex\Auth\StdLoginCredentials;
use Cubex\Core\Controllers\WebpageController;
use Cubex\Facade\Auth;
use Cubex\Facade\Redirect;
use Cubex\View\Impart;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Login\Views\Login;

class DefaultController extends BaseControl
{
  public function renderIndex()
  {
    return new Login();
  }

  public function canProcess()
  {
    return true;
  }

  public function postIndex()
  {
    $postData = $this->request()->postVariables();
    $user     = Auth::authByCredentials(
      StdLoginCredentials::make(
        $postData['username'],
        md5($postData['password'])
      )
    );

    if($user && Auth::loggedIn())
    {
      Redirect::to('/')->now();
    }
    else
    {
      Redirect::to('/')->with(
        'msg',
        'Login Failed, please check username and password is correct'
      )->now();
    }
  }

  public function getHeader()
  {
    return new RenderGroup(
      '<a id="sidekick-logo" class="brand" href="/">',
      ('<img src="' . $this->imgUrl('/logo.png') . '"/></a>'));
  }

  public function getSidebar()
  {
    return null;
  }

  public function getRoutes()
  {
    return [
      '*' => 'index'
    ];
  }
}
