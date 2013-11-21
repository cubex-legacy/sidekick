<?php
/**
 * @author oke.ugwu
 */

namespace Sidekick\Applications\Rosetta\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Rosetta\Helpers\LanguageCodes;
use Sidekick\Components\Rosetta\Mappers\PendingTranslation;
use Sidekick\Components\Rosetta\Mappers\Translation;

class RosettaIndex extends TemplatedViewModel
{
  protected $language;
  protected $_pendingTranslations;

  public function __construct($language, $pendingTranslations)
  {
    $this->_language            = $language;
    $this->_pendingTranslations = $pendingTranslations;
  }

  /**
   * @return array|object[]
   */
  public function getPendingTranslations()
  {
    $result = [];
    foreach($this->_pendingTranslations as $pending)
    {
      $translationCf  = Translation::cf();
      $englishData    = $translationCf->get($pending->rowKey, ['lang:en']);
      $translatedData = $translationCf->get(
        $pending->rowKey,
        ['lang:' . $pending->lang]
      );

      if($englishData && $translatedData)
      {
        $row = [
          'rowKey'      => $pending->rowKey,
          'lang'        => $pending->lang,
          'english'     => json_decode(current($englishData))->translated,
          'translation' => json_decode(current($translatedData))->translated
        ];

        $result[] = (object)$row;
      }
    }

    return $result;
  }

  public function getSelectedLanguages()
  {
    return [
      LanguageCodes::getLanguageFromCode('en'),
      LanguageCodes::getLanguageFromCode($this->_language)
    ];
  }

  public function getAllLanguages()
  {
    return LanguageCodes::getAllLanguages();
  }

  public function getLanguage()
  {
    return $this->_language;
  }
}
