<?php
/**
 * Author: oke.ugwu
 * Date: 20/06/13 14:52
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Fortify\Reports\PhpCsError;
use Sidekick\Applications\Fortify\Reports\PhpCsFile;

class PhpCsReport extends TemplatedViewModel
{
  private $_file;
  private $_parsedData;
  public $reportFileFound = true;

  public function __construct($file)
  {
    $this->_file = $file;
    $this->_setParsedData();
  }

  /**
   * @return PhpCsFile[]
   */
  public function getErrorFiles()
  {
    return $this->_parsedData['style'];
  }

  public function getErrorFilesCount()
  {
    return count($this->_parsedData['style']);
  }

  public function getCodeStandardErrorSummary()
  {
    return $this->_parsedData['summary']['standard'];
  }

  public function getCategoryErrorSummary()
  {
    return $this->_parsedData['summary']['category'];
  }

  public function getSubCategoryErrorSummary()
  {
    return $this->_parsedData['summary']['subCategory'];
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
      $xml = simplexml_load_file($this->_file);
      foreach($xml->file as $file)
      {
        $fileName = basename((string)$file['name']);
        $errors   = [];
        foreach($file->error as $error)
        {
          /**
           * XML can be a bitch sometimes. Casting to avoid issue
           * down the line
           */

          $e = new PhpCsError();
          $e->setLine((int)$error['line'])
          ->setColumn((int)$error['column'])
          ->setMessage((string)$error['message'])
          ->setSource((string)$error['source']);
          //->setFileName($fileName);

          list($standard, $category, $subCategory, $type) = explode(
            '.',
            $e->source
          );

          $e->setStandard($standard)
          ->setCategory($category)
          ->setSubCategory($subCategory)
          ->setType($type);

          $errors[] = $e;

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
        }

        $phpCsFile       = new PhpCsFile($fileName, $errors);
        $data['style'][] = $phpCsFile;
      }
    }
    else
    {
      $this->reportFileFound = false;
    }

    return $data;
  }
}
