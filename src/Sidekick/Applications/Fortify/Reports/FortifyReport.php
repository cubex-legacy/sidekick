<?php
/**
 * Author: oke.ugwu
 * Date: 26/06/13 17:46
 */

namespace Sidekick\Applications\Fortify\Reports;

use Cubex\Foundation\Container;

abstract class FortifyReport
{
  public $buildId;
  public $rundId;
  public $filter;
  public $basePath;

  public function __construct(
    $buildId, $runId, $filter = null, $basePath = null
  )
  {
    $this->buildId  = $buildId;
    $this->runId    = $runId;
    $this->filter   = $filter;
    $this->basePath = $basePath;
  }

  public function getFileBase()
  {
    return dirname(Container::config()->get('_cubex_')->getStr('project_base'))
    . DIRECTORY_SEPARATOR;
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
    return file_exists($this->getReportFile());
  }

  abstract public function getView();

  abstract public function getReportFile();
}
