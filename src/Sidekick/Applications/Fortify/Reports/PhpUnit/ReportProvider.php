<?php
/**
 * Author: oke.ugwu
 * Date: 26/06/13 17:47
 */

namespace Sidekick\Applications\Fortify\Reports\PhpUnit;

use Sidekick\Applications\Fortify\Reports\FortifyReport;
use Sidekick\Applications\Fortify\Views\PhpUnitReport;

class ReportProvider extends FortifyReport
{
  public function getView()
  {
    return new PhpUnitReport(
      $this->getReportFile()
    );
  }

  public function getReportFile()
  {
    return $this->getFileBase() . "builds\\$this->runId\\logs\\junit.xml";
  }
}
