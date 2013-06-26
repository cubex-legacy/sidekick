<?php
/**
 * Author: oke.ugwu
 * Date: 26/06/13 17:47
 */

namespace Sidekick\Applications\Fortify\Reports\PhpMd;

use Sidekick\Applications\Fortify\Reports\FortifyReport;
use Sidekick\Applications\Fortify\Views\PhpMdReport;

class ReportProvider extends FortifyReport
{
  public function getView()
  {
    return new PhpMdReport(
      $this->getReportFile(),
      $this->filter,
      $this->basePath
    );
  }

  public function getReportFile()
  {
    return $this->getFileBase() . "/builds/$this->runId/logs/pmd.report.xml";
  }
}
