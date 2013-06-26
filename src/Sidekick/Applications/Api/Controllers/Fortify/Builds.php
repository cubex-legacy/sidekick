<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Api\Controllers\Fortify;

use Sidekick\Applications\Api\Controllers\ApiController;
use Sidekick\Components\Fortify\Enums\BuildResult;
use Sidekick\Components\Fortify\Mappers\Command;
use Sidekick\Components\Fortify\Mappers\BuildRun;

class Builds extends ApiController
{
  public function buildStatus($projectId, $buildId)
  {
    $buildRuns = BuildRun::collection();
    $buildRuns->loadWhere(['project_id' => $projectId, 'build_id' => $buildId]);
    $buildRuns->setOrderBy("end_time", 'DESC');
    $buildRuns->setLimit(0, 1)->get();

    if($buildRuns->count() === 1)
    {
      $buildRun = $buildRuns->first();
      /**
       * @var $buildRun BuildRun
       */

      $msg = '';
      if($buildRun->result !== BuildResult::PASS)
      {
        $runningCommand = end($buildRun->commands);
        if(is_int($runningCommand))
        {
          $command = new Command($runningCommand);
          $msg     = $command->name;
        }
        else
        {
          $msg = $runningCommand;
        }
      }

      return [
        'runId'     => $buildRun->id,
        'result'    => $buildRun->result,
        'startTime' => $buildRun->startTime,
        'endTime'   => $buildRun->endTime,
        'commands'  => $buildRun->commands,
        'msg'       => $msg,
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
