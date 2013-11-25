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
  protected $_availableLanguages;

  public function __construct(
    $projectId, $language, $pendingTranslations, $availableLanguages
  )
  {
    $this->_projectId           = $projectId;
    $this->_language            = $language;
    $this->_pendingTranslations = $pendingTranslations;
    $this->_availableLanguages  = $availableLanguages;
  }

  /**
   * @return array|object[]
   */
  public function getPendingTranslations()
  {
    return $this->_pendingTranslations;
  }

  public function getSelectedLanguages()
  {
    return [
      LanguageCodes::getLanguageFromCode('en'),
      LanguageCodes::getLanguageFromCode($this->_language)
    ];
  }

  public function getLanguageOptions()
  {
    return LanguageCodes::getLanguageOptions($this->_availableLanguages);
  }

  public function getLanguage()
  {
    return $this->_language;
  }

  public function getProjectId()
  {
    return $this->_projectId;
  }
}
