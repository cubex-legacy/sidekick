<?php
/**
 * Author: oke.ugwu
 * Date: 21/11/13 09:49
 */

namespace Sidekick\Applications\Rosetta\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Rosetta\Helpers\LanguageCodes;

class Translations extends TemplatedViewModel
{
  protected $projectId;
  protected $_rowKey;
  protected $_raw_translations;
  protected $_translations;

  public function __construct($projectId, $rowKey, $translations)
  {
    $this->_projectId        = $projectId;
    $this->_rowKey           = $rowKey;
    $this->_raw_translations = $translations;
  }

  /**
   * Pre-process translations before sending to view. Returns only
   * unapproved translations
   *
   * @return array
   */
  public function getTranslations()
  {
    if($this->_translations == null)
    {
      foreach($this->_raw_translations as $lang => $t)
      {
        if(substr($lang, 0, 4) == 'lang')
        {
          $data = json_decode($t);
          if($data->approved == false)
          {
            $data->lang                       = substr($lang, -2);
            $this->_translations[$data->lang] = $data;
          }
        }
      }
    }

    return $this->_translations;
  }

  public function getEnglish()
  {
    $this->getTranslations();
    return $this->_translations['en'];
  }

  public function getRowkey()
  {
    return $this->_rowKey;
  }

  public function getProjectId()
  {
    return $this->_projectId;
  }
}
