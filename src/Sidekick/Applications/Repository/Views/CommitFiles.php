<?php
/**
 * Author: oke.ugwu
 * Date: 28/06/13 10:12
 */

namespace Sidekick\Applications\Repository\Views;

use Cubex\Foundation\Container;
use Cubex\View\TemplatedViewModel;

class CommitFiles extends TemplatedViewModel
{
  /**
   * @var $commitFiles \Sidekick\Components\Repository\Mappers\CommitFile[]
   */
  public $commitFiles;
  private $_runId;

  public function __construct($commitFiles, $runId)
  {
    $this->commitFiles = $commitFiles;
    $this->_runId    = $runId;
  }

  public function getFullPath($filePath)
  {
    $base = Container::config()->get('_cubex_')->getStr('project_base') . '../';
    if(!starts_with($filePath, $base))
    {
      return $base . 'builds/' . $this->_runId . '/sourcecode/' . $filePath;
    }
    return $filePath;
  }
}
