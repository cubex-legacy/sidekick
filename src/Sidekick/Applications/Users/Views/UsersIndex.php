<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 30/05/13
 * Time: 12:42
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Users\Views;

use Cubex\Facade\Auth;
use Cubex\View\TemplatedViewModel;

class UsersIndex extends TemplatedViewModel
{
  protected $_users;

  public function __construct($users)
  {
    $this->_users = $users;
  }

  /**
   * @return \Sidekick\Components\Projects\Mappers\Project[]
   */
  public function getUsers()
  {
    return $this->_users;
  }

  /**
   * @return bool
   */
  public function canDelete()
  {
    return Auth::user()->getDetails()->user_role == 'administrator';
  }

  /**
   * @return bool
   */
  public function canEdit()
  {
    return Auth::user()->getDetails()->user_role == 'administrator';
  }

  /**
   * @return bool
   */
  public function canCreate()
  {
    return Auth::user()->getDetails()->user_role == 'administrator';
  }
}
