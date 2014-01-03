<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Analysers;

abstract class AbstractAnalyser implements FortifyAnalyser
{
  protected $_files = [];
  protected $_basePath = '';

  public function setFileBasePath($basePath)
  {
    $this->_basePath = $basePath;
    return $this;
  }

  public function addFile($filePath)
  {
    $this->_files[] = $filePath;
    return $this;
  }

  protected function _getFiles()
  {
    return $this->_files;
  }

  public function clearFiles()
  {
    $this->_files = [];
    return $this;
  }
}
