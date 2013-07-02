<?php
/**
 * Author: oke.ugwu
 * Date: 02/07/13 10:59
 */

namespace Sidekick\Applications\Projects\Views;

use Cubex\View\TemplatedViewModel;

class ProjectUsersList extends TemplatedViewModel
{
  protected $_users;

  public function __construct($users)
  {
    $this->_users = $users;
  }

  /**
   * @return \Sidekick\Components\Projects\Mappers\ProjectUser[]
   */
  public function getUsers()
  {
    return $this->_users;
  }
}
