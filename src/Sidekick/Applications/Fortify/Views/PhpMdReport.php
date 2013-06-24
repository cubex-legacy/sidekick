<?php
/**
 * Author: oke.ugwu
 * Date: 20/06/13 13:19
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Fortify\Reports\PhpMdError;
use Sidekick\Applications\Fortify\Reports\PhpMdFile;

class PhpMdReport extends TemplatedViewModel
{
  private $_file;
  private $_parsedData;
  public $filter;
  public $filterType;
  public $group;
  public $reportFileFound = true;

  public function __construct($file, $filter)
  {
    $this->_file = $file;
    list($this->filterType, $this->filter) = explode(':', $filter);
    $this->group = 'rule_files';
    if($this->filterType != 'mess')
    {
      $this->group = 'package_files';
    }

    $this->_setParsedData();
  }

  public function getErrorsCount()
  {
    return isset($this->_parsedData['overview']['errors']) ?
      $this->_parsedData['overview']['errors'] : '';
  }

  public function getViolationsCount()
  {
    return isset($this->_parsedData['overview']['violations']) ?
      $this->_parsedData['overview']['violations'] : '';
  }

  /**
   * @return PhpMdFile[]
   */
  public function getFiles()
  {
    return $this->_parsedData[$this->group][$this->filter];
  }

  public function getMessTypeSummary()
  {
    return isset($this->_parsedData['rule']) ? $this->_parsedData['rule'] : [];
  }

  public function getPackageSummary()
  {
    return isset($this->_parsedData['package']) ?
      $this->_parsedData['package'] : [];
  }

  /**
   * @return PhpMdError[]
   */
  public function getErrors()
  {
    return isset($this->_parsedData['errors']) ?
      $this->_parsedData['errors'] : [];
  }

  public function getAppliedFilter()
  {
    if($this->filterType == 'mess')
    {
      return $this->filter;
    }
    else
    {
      return $this->_parsedData['packageKeys'][$this->filter];
    }
  }

  protected function _setParsedData()
  {
    if($this->_parsedData === null)
    {
      $this->_parsedData = $this->_parseFile();
    }
  }

  protected function _parseFile()
  {
    $d = [];
    if(file_exists($this->_file))
    {
      $xml            = simplexml_load_file($this->_file);
      $violationCount = 0;
      foreach($xml->file as $file)
      {
        $filename       = (string)$file['name'];
        $filenameShort  = basename($filename);
        $filenameUnique = md5($filename);

        //a file can have more than one violation
        foreach($file->violation as $v)
        {
          $beginLine       = (int)$v['beginline'];
          $endLine         = (int)$v['endline'];
          $priority        = (int)$v['priority'];
          $rule            = (string)$v['rule'];
          $ruleset         = (string)$v['ruleset'];
          $package         = (string)$v['package'];
          $packageKey      = md5($package);
          $class           = (string)$v['class'];
          $method          = (string)$v['method'];
          $externalInfoUrl = (string)$v['externalInfoUrl'];

          $fileData = new PhpMdFile();
          $fileData->setFileName($filenameShort)
          ->setBeginLine($beginLine)
          ->setEndLine($endLine)
          ->setPriority($priority)
          ->setRule($rule)
          ->setRuleSet($ruleset)
          ->setPackage($package)
          ->setClass($class)
          ->setMethod($method)
          ->setExternalInfoUrl($externalInfoUrl);

          if(!isset($d['filename'][$filenameUnique]))
          {
            $d['filename'][$filenameUnique] = 0;
          }
          if(!isset($d['rule'][$rule]))
          {
            $d['rule'][$rule] = 0;
          }
          if(!isset($d['rule_packages'][$rule][$package]))
          {
            $d['rule_packages'][$rule][$package] = 0;
          }
          if(!isset($d['package'][$package]))
          {
            $d['package'][$package] = 0;
          }

          $d['filename'][$filenameUnique] += 1;
          $d['rule'][$rule] += 1;

          $d['rule_files'][$rule][] = $fileData;
          $d['rule_packages'][$rule][$package] += 1;

          $d['package'][$package] += 1;
          $d['package_files'][$packageKey][] = $fileData;

          $d['packageKeys'][$packageKey] = $package;

          $violationCount++;
        }

        $d['overview']['violations'] = $violationCount;
        $d['overview']['errors']     = count($xml->error);
      }

      foreach($xml->error as $file)
      {
        $error         = new PhpMdError($file['filename'], $file['msg']);
        $d['errors'][] = $error;
      }
    }
    else
    {
      $this->reportFileFound = false;
    }

    return $d;
  }
}
