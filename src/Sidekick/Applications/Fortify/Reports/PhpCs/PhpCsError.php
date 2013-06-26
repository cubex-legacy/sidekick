<?php
/**
 * Author: oke.ugwu
 * Date: 21/06/13 11:07
 */

namespace Sidekick\Applications\Fortify\Reports\PhpCs;

class PhpCsError
{
  public $fileName;
  public $line;
  public $column;
  public $message;
  public $source;
  public $standard;
  public $category;
  public $subCategory;
  public $type;

  /**
   * @param mixed $category
   *
   * @return PhpCsError
   */
  public function setCategory($category)
  {
    $this->category = $category;
    return $this;
  }

  /**
   * @param mixed $fileName
   *
   * @return PhPCsError
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
    return $this;
  }

  /**
   * @param mixed $column
   *
   * @return PhpCsError
   */
  public function setColumn($column)
  {
    $this->column = $column;
    return $this;
  }

  /**
   * @param mixed $source
   *
   * @return PhpCsError
   */
  public function setSource($source)
  {
    $this->source = $source;
    return $this;
  }

  /**
   * @param mixed $line
   *
   * @return PhpCsError
   */
  public function setLine($line)
  {
    $this->line = $line;
    return $this;
  }

  /**
   * @param mixed $message
   *
   * @return PhpCsError
   */
  public function setMessage($message)
  {
    $this->message = $message;
    return $this;
  }

  /**
   * @param mixed $standard
   *
   * @return PhpCsError
   */
  public function setStandard($standard)
  {
    $this->standard = $standard;
    return $this;
  }

  /**
   * @param mixed $subCategory
   *
   * @return PhpCsError
   */
  public function setSubCategory($subCategory)
  {
    $this->subCategory = $subCategory;
    return $this;
  }

  /**
   * @param mixed $type
   *
   * @return PhpCsError
   */
  public function setType($type)
  {
    $this->type = $type;
    return $this;
  }
}
