<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 31/05/13
 * Time: 09:36
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Repository\Views;

use Cubex\View\TemplatedViewModel;

class CommitsIndex extends TemplatedViewModel
{
  public $repo;
  protected $_commits;

  public function __construct($repo, $commits)
  {
    $this->repo = $repo;
    $this->_commits = $commits;
  }

  public function getCommits()
  {
    return $this->_commits;
  }
}
