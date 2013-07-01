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
  protected $_reportName;
  protected $_reason;
  protected $_basePath;

  public function __construct($reportName, $errorReason, $basePath)
  {
    $this->_reportName = $reportName;
    $this->_reason     = $errorReason;
    $this->_basePath   = $basePath;
  }
}
