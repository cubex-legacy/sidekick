<?php
/**
 * Author: oke.ugwu
 * Date: 20/06/13 14:52
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Fortify\Reports\PhpCs\PhpCsError;
use Sidekick\Applications\Fortify\Reports\PhpCs\PhpCsFile;

class PhpCsReport extends TemplatedViewModel
{
  private $_runId;
  private $_file;
  private $_filter;
  private $_parsedData;
  public $basePath;
  public $reportFileFound = true;

  public function __construct($file, $filter, $basePath, $runId)
  {
    if($filter !== null)
    {
      $filter = explode(':', $filter)[1];
    }
    else
    {
      $filter = 'all';
    }
    $this->_file    = $file;
    $this->_filter  = $filter;
    $this->basePath = $basePath;
    $this->_setParsedData();
    $this->_runId = $runId;

    $this->requireJs(
      'https://google-code-prettify.googlecode.com/svn/'
      . 'loader/run_prettify.js?skin=sons-of-obsidian'
    );
  }

  public function getRunId()
  {
    return $this->_runId;
  }

  /**
   * @return PhpCsFile[]
   */
  public function getErrorFiles()
  {
    if(isset($this->_parsedData[$this->_filter]))
    {
      return $this->_parsedData[$this->_filter];
    }
    return [];
  }

  public function getErrorFilesCount()
  {
    if(isset($this->_parsedData['all']))
    {
      return count($this->_parsedData['all']);
    }
    return 0;
  }

  public function getErrorsCount()
  {
    if(isset($this->_parsedData['errorsCount'][$this->_filter]))
    {
      return $this->_parsedData['errorsCount'][$this->_filter];
    }
    return 0;
  }

  public function getCodeStandardErrorSummary()
  {
    if(isset($this->_parsedData['summary']['standard']))
    {
      return $this->_parsedData['summary']['standard'];
    }
    return [];
  }

  public function getCategoryErrorSummary()
  {
    if(isset($this->_parsedData['summary']['category']))
    {
      return $this->_parsedData['summary']['category'];
    }
    return [];
  }

  public function getSubCategoryErrorSummary()
  {
    if(isset($this->_parsedData['summary']['subCategory']))
    {
      return $this->_parsedData['summary']['subCategory'];
    }
    return [];
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
    $data = [];
    if(file_exists($this->_file))
    {
      $xml = simplexml_load_string(file_get_contents($this->_file));
      foreach($xml->file as $file)
      {
        $fileName     = basename((string)$file['name']);
        $fullFileName = (string)$file['name'];

        //get just the relative file name starting from src
        $fullFileName   = substr($fullFileName, strpos($fullFileName, 'src'));
        $errors         = [];
        $filteredErrors = [];
        foreach($file->error as $error)
        {
          $e = new PhpCsError();
          $e->setLine((int)$error['line'])
            ->setColumn((int)$error['column'])
            ->setMessage((string)$error['message'])
            ->setSource((string)$error['source'])
            ->setFileName($fullFileName);

          list($standard, $category, $subCategory, $type) = explode(
            '.',
            $e->source
          );

          $e->setStandard($standard)
            ->setCategory($category)
            ->setSubCategory($subCategory)
            ->setType($type);

          $errors[]                             = $e;
          $filteredErrors[$standard][]          = $e;
          $filteredErrors[md5($fullFileName)][] = $e;

          if(!isset($data['summary']['standard'][$standard]))
          {
            $data['summary']['standard'][$standard] = 0;
          }
          $data['summary']['standard'][$standard] += 1;

          if(!isset($data['summary']['category'][$category]))
          {
            $data['summary']['category'][$category] = 0;
          }
          $data['summary']['category'][$category] += 1;

          if(!isset($data['summary']['subCategory'][$subCategory]))
          {
            $data['summary']['subCategory'][$subCategory] = 0;
          }
          $data['summary']['subCategory'][$subCategory] += 1;

          if(!isset($data['summary']['type'][$type]))
          {
            $data['summary']['type'][$type] = 0;
          }
          $data['summary']['type'][$type] += 1;

          $data['hierarchy'][$standard][$category][$subCategory][$type] = 1;

          $data['errorsCount']['all'] += 1;
          $data['errorsCount'][$standard] += 1;
          $data['errorsCount'][md5($fullFileName)] += 1;
        }

        $data['all'][]              = new PhpCsFile($fileName, $errors);
        $data[$standard][]          = new PhpCsFile(
          $fileName, $filteredErrors[$standard]
        );
        $data[md5($fullFileName)][] = new PhpCsFile(
          $fileName, $filteredErrors[md5($fullFileName)]
        );
      }
    }
    else
    {
      $this->reportFileFound = false;
    }

    return $data;
  }
}
