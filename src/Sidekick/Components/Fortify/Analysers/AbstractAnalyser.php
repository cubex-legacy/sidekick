<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Analysers;

use Sidekick\Components\Fortify\AbstractFortifyElement;
use Sidekick\Components\Repository\Enums\ChangeType;
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\CommitFile;

abstract class AbstractAnalyser extends AbstractFortifyElement
  implements FortifyAnalyser
{
  /**
   * Return all files within the repository
   *
   * @return array
   */
  protected function _getRepositoryFiles()
  {
    return $this->_getFullFilelisting($this->_basePath);
  }

  protected function _getFullFilelisting($directory, $filePatten = '.*')
  {
    $files = [];

    $recursive = new \RecursiveDirectoryIterator($directory);
    foreach($recursive as $file)
    {
      /***
       * @var $file \SplFileInfo
       */
      $filePath = $file->getRealPath();
      if($file->isDir() && !in_array($file->getFilename(), ['.', '..']))
      {
        $files = array_merge(
          $files,
          $this->_getFullFilelisting($filePath, $filePatten)
        );
      }
      else if(preg_match("/$filePatten/", $filePath))
      {
        $files[] = $filePath;
      }
    }

    return $files;
  }

  /**
   * @param Commit $commit
   *
   * @return CommitFile[]|\Cubex\Mapper\Database\RecordCollection
   */
  protected function _getCommitFiles(Commit $commit)
  {
    return $commit->commitFiles();
  }

  /**
   * @param Commit $commit
   *
   * @return CommitFile[]|\Cubex\Mapper\Database\RecordCollection
   */
  protected function _getModifiedFiles(Commit $commit)
  {
    return $commit->commitFiles([ChangeType::MODIFIED]);
  }

  /**
   * @param Commit $commit
   *
   * @return CommitFile[]|\Cubex\Mapper\Database\RecordCollection
   */
  protected function _getAddedFiles(Commit $commit)
  {
    return $commit->commitFiles([ChangeType::ADDED]);
  }

  /**
   * @param Commit $commit
   *
   * @return CommitFile[]|\Cubex\Mapper\Database\RecordCollection
   */
  protected function _getCurrentFiles(Commit $commit)
  {
    return $commit->commitFiles([ChangeType::ADDED, ChangeType::MODIFIED]);
  }

  /**
   * @param Commit $commit
   *
   * @return CommitFile[]|\Cubex\Mapper\Database\RecordCollection
   */
  protected function _getDeletedFiles(Commit $commit)
  {
    return $commit->commitFiles([ChangeType::DELETED]);
  }

  protected function _trackInsight($key, $value)
  {
    if($this->_alias === null)
    {
      $alias = class_shortname(get_called_class());
    }
    else
    {
      $alias = $this->_alias;
    }
    $this->_insight->setInsight($alias, $key, $value);
  }
}
