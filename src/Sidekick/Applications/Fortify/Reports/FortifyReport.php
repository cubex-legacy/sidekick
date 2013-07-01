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

  public function __construct($runId, $filter = null, $basePath = null)
  {
    $this->runId    = $runId;
    $this->filter   = $filter;
    $this->basePath = $basePath;
  }

  public function getFileBase()
  {
    return Container::config()->get('_cubex_')->getStr('project_base') . '../';
  }

  public static function getReportProviderClass($namespace)
  {
    if(starts_with($namespace, '\\'))
    {
      $base = '';
    }
    else
    {
      $base = "\\Sidekick\\Applications\\Fortify\\Reports\\";
    }

    $class = $base . $namespace . "\\ReportProvider";
    if(class_exists($class))
    {
      return $class;
    }
    else
    {
      throw new \Exception("Namespace does not contain a valid ReportProvider");
    }
  }

  public function reportFileExists()
  {
    if(file_exists($this->getReportFile()))
    {
      return true;
    }
    return false;
  }

  abstract public function getView();

  abstract public function getReportFile();
}
