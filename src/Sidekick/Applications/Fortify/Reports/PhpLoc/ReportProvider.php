<?php
/**
 * Author: oke.ugwu
 * Date: 26/06/13 17:47
 */

namespace Sidekick\Applications\Fortify\Reports\PhpLoc;

use Sidekick\Applications\Fortify\Reports\FortifyReport;
use Sidekick\Applications\Fortify\Views\PhpLocReport;

class ReportProvider extends FortifyReport
{
  public function getView()
  {
    return new PhpLocReport(
      $this->getReportFile()
    );
  }

  public function getReportFile()
  {
    return $this->getFileBase() . "builds/$this->runId/logs/phploc.csv";
  }
}
