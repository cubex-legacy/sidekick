<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 31/05/13
 * Time: 08:46
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Repository\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Repository\Mappers\Source;

class RepositoryIndex extends TemplatedViewModel
{
  protected $_repositories;

  public function __construct($repositories)
  {
    $this->_repositories = $repositories;
  }

  /**
   * @return Source[]
   */
  public function getRepositories()
  {
    return $this->_repositories;
  }
}
