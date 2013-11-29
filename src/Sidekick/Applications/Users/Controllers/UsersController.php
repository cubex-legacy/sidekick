<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Users\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Users\Views\UsersSidebar;

class UsersController extends BaseControl
{
  protected $_titlePrefix = 'Users';

  public function preRender()
  {
    parent::preRender();
    $this->nest('sidebar', new UsersSidebar($this->appBaseUri()));
  }
}
