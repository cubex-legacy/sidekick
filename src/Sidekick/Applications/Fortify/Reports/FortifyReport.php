<?php
/**
 * Author: oke.ugwu
 * Date: 26/06/13 17:46
 */

namespace Sidekick\Applications\Fortify\Reports;

abstract class FortifyReport
{
  public $rundId;
  public $filter;
  public $basePath;

  public function __construct($runId, $filter=null, $basePath=null)
  {
    $this->runId = $runId;
    $this->filter = $filter;
    $this->basePath = $basePath;
  }

  public function getFileBase()
  {
    return realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
  }

  abstract public function getView();
  abstract public function getReportFile();
}
