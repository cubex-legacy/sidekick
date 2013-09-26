<?php
/**
 * Author: oke.ugwu
 * Date: 26/06/13 17:47
 */

namespace Sidekick\Applications\Fortify\Reports\PhpCs;

use Sidekick\Applications\Fortify\Reports\FortifyReport;
use Sidekick\Applications\Fortify\Views\PhpCsReport;

class ReportProvider extends FortifyReport
{
  public function getView()
  {
    return new PhpCsReport(
      $this->getReportFile(),
      $this->filter,
      $this->basePath,
      $this->buildId
    );
  }

  public function getReportFile()
  {
    return $this->getFileBase() . "builds/$this->runId/logs/checkstyle.xml";
  }
}
