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

  public function __construct($language)
  {
    $this->_language = $language;
  }

  /**
   * @return array|object[]
   */
  public function getPendingTranslations()
  {
    $result              = [];
    $pendingTranslations = PendingTranslation::collection(
      ['lang' => $this->_language]
    );
    foreach($pendingTranslations as $pending)
    {
      $translationCf  = Translation::cf();
      $translatedData = $translationCf->get(
        $pending->rowKey,
        ['lang:' . $pending->lang]
      );
      $englishData    = $translationCf->get($pending->rowKey, ['lang:en']);

      $result[$pending->rowKey]['en']           = json_decode(
        current($englishData)
      );
      $result[$pending->rowKey][$pending->lang] = json_decode(
        current($translatedData)
      );
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
