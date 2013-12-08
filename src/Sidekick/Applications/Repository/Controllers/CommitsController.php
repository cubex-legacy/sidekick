<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 31/05/13
 * Time: 17:31
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Repository\Controllers;

use Sidekick\Applications\Repository\Views\CommitFiles;
use Sidekick\Applications\Repository\Views\CommitsIndex;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\CommitFile;
use Sidekick\Components\Repository\Mappers\Source;

class CommitsController extends RepositoryController
{
  public function renderIndex()
  {
    $branchId  = $this->getInt('branchId');
    $branch    = new Branch($branchId);
    $commits = Commit::collection(['branch_id' => $branchId])
    ->setOrderBy('committed_at', 'DESC')
    ->setLimit(0, 50);
    return $this->createView(new CommitsIndex($branch, $commits));
  }
}
