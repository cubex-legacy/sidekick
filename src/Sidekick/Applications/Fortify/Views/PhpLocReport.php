<?php
/**
 * Author: oke.ugwu
 * Date: 20/06/13 14:36
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;

class PhpLocReport extends TemplatedViewModel
{
  private $_file;
  private $_parsedData;
  public $reportFileFound = true;

  public function __construct($file)
  {
    $this->_file       = $file;
    $this->_parsedData = $this->_parseFile();
  }

  public function getStats()
  {
    if($this->_parsedData == null)
    {
      $this->_parsedData = $this->_parseFile();
    }
    return $this->_parsedData;
  }

  protected function _parseFile()
  {
    $data = [];
    if(file_exists($this->_file))
    {
      if(($handle = fopen($this->_file, "r")) !== false)
      {
        $t = [];
        while(($data = fgetcsv($handle)) !== false)
        {
          $t[] = $data;
        }
        $data = array_combine($t[0], $t[1]);
      }
    }
    else
    {
      $this->reportFileFound = false;
    }
    return $data;
  }
}
