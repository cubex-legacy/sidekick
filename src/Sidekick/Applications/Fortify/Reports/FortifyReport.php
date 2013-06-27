<?php
/**
 * Author: oke.ugwu
 * Date: 26/06/13 17:46
 */

namespace Sidekick\Applications\Fortify\Reports;

use Cubex\Foundation\Container;

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
    return Container::config()->get('_cubex_')->getStr('project_base').'../';
  }

  public static function getReportProviderClass($namespace)
  {
    $base = "\\Sidekick\\Applications\\Fortify\\Reports\\";
    return $base . $namespace . "\\ReportProvider";
  }

  abstract public function getView();
  abstract public function getReportFile();
}
