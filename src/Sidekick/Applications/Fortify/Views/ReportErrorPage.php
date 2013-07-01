<?php
/**
 * Author: oke.ugwu
 * Date: 01/07/13 10:52
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;

class ReportErrorPage extends TemplatedViewModel
{
  public $reportName;
  public $reason;
  public $basePath;

  public function __construct($reportName, $errorReason, $basePath)
  {
    $this->reportName = $reportName;
    $this->reason     = $errorReason;
    $this->basePath   = $basePath;
  }
}
