<?php
/**
 * Author: oke.ugwu
 * Date: 20/06/13 17:43
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Fortify\Reports\PhpUnitTestSummary;

class PhpUnitReport extends TemplatedViewModel
{
  private $_file;
  private $_parsedData;
  public $reportFileFound = true;

  public function __construct($file)
  {
    $this->_file       = $file;
    $this->_parsedData = $this->_parseFile();
  }

  /**
   * @return PhpUnitTestSummary
   */
  public function getTestSummary()
  {
    return $this->_parsedData['testSummary'];
  }

  public function getTestData()
  {
    return $this->_parsedData['testSuites'];
  }

  protected function _parseFile()
  {
    $data = [];
    if(file_exists($this->_file))
    {
      $xml = simplexml_load_file($this->_file);

      $summary             = new PhpUnitTestSummary();
      $summary->tests      = (int)$xml->testsuite['tests'];
      $summary->name       = (string)$xml->testsuite['name'];
      $summary->assertions = (int)$xml->testsuite['assertions'];
      $summary->failures   = (int)$xml->testsuite['failures'];
      $summary->errors     = (int)$xml->testsuite['errors'];
      $summary->time       = (float)$xml->testsuite['time'];

      $data['testSummary'] = $summary;

      foreach($xml as $testSuite)
      {
        foreach($testSuite as $testsuite)
        {
          $data['testSuites'][] = $this->_getTestSuite($testsuite);
        }
      }
    }
    else
    {
      $this->reportFileFound = false;
    }
    return $data;
  }

  private function _getTestSuite($node)
  {
    $return                = [];
    $return['name']        = (string)$node['name'];
    $return['file']        = (string)$node['file'];
    $return['namespace']   = (string)$node['namespace'];
    $return['fullPackage'] = (string)$node['fullPackage'];
    $return['tests']       = (int)$node['tests'];
    $return['assertions']  = (int)$node['assertions'];
    $return['failures']    = (int)$node['failures'];
    $return['errors']      = (int)$node['errors'];
    $return['time']        = (float)$node['time'];

    $return['testSuite'] = [];
    $return['testCase']  = [];
    foreach($node->children() as $child)
    {
      if($child->getName() == 'testsuite')
      {
        $return['testSuite'][] = $this->_getTestSuite($child);
      }
      elseif($child->getName() == 'testcase')
      {
        $return['testCase'][] = $this->_getTestCases($child);
      }
    }

    return $return;
  }

  private function _getTestCases($node)
  {
    $return               = [];
    $return['name']       = (string)$node['name'];
    $return['class']      = (string)$node['class'];
    $return['file']       = (string)$node['file'];
    $return['line']       = (int)$node['line'];
    $return['assertions'] = (int)$node['assertions'];
    $return['time']       = (float)$node['time'];

    return $return;
  }
}
