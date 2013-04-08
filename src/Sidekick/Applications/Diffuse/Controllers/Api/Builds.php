<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Api;

use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use Sidekick\Components\Diffuse\Mappers\BuildRun;

class Builds extends DiffuseController
{
  public function buildStatus($projectId, $buildId)
  {
    $buildRuns = BuildRun::collection();
    $buildRuns->loadWhere(['project_id' => $projectId, 'build_id' => $buildId]);
    $buildRuns->setOrderBy("end_time", 'DESC');
    $buildRuns->setLimit(0, 1);

    if($buildRuns->count() === 1)
    {
      $buildRun = $buildRuns->first();
      /**
       * @var $buildRun BuildRun
       */
      return [
        'runId'     => $buildRun->id,
        'result'    => $buildRun->result,
        'startTime' => $buildRun->startTime,
        'endTime'   => $buildRun->endTime,
        'commands'  => $buildRun->commands,
      ];
    }
    return [];
  }

  public function getRoutes()
  {
    return [
      'status/:projectId/:buildId' => 'buildStatus'
    ];
  }
}
