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
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\Source;

class CommitsIndex extends TemplatedViewModel
{
  public $repo;
  protected $_commits;

  public function __construct($repoId)
  {
    $this->_commits = Commit::collection()->loadWhere(
                        ['repository_id' => $repoId]
                      )
                      ->setOrderBy('committed_at', 'DESC')
                      ->setLimit(0, 50);

    $this->repo = new Source($repoId);
  }

  public function getCommits()
  {
    return $this->_commits;
  }
}
