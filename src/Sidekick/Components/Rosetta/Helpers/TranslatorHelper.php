<?php
/**
 * Author: oke.ugwu
 * Date: 20/11/13 15:33
 */

namespace Sidekick\Components\Rosetta\Helpers;

use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Container;
use Cubex\I18n\Translator\ITranslator;
use Sidekick\Components\Rosetta\Mappers\Translation;

class TranslatorHelper
{
  /**
   * @param string $text
   * @param string $sourceLang
   * @param string $targetLang
   *
   * @return string
   * @throws \Exception
   */
  public static function translate($text, $sourceLang, $targetLang)
  {
    //get translated text from configured translator
    $config     = Container::config()->get("i18n", new Config());
    $translator = $config->getStr("translator", null);

    if($translator == null)
    {
      throw new \Exception(
        "No Translator found in config file",
        400
      );
    }

    $translatorObj = new $translator;
    if($translatorObj instanceof ITranslator)
    {
      $translatedText = $translatorObj->translate(
        $text,
        $sourceLang,
        $targetLang
      );

      return $translatedText;
    }
    else
    {
      throw new \Exception(
        "Translator specified in config file is not a valid ITranslator",
        400
      );
    }
  }

  /**
   * Write Translation data to Rosetta Cassandra Translation Column family
   *
   * @param string $key
   * @param string $text
   * @param string $lang
   * @param int    $projectId
   */
  public static function saveTranslation($key, $text, $lang, $projectId = 0)
  {
    $translationCf = Translation::cf();
    $columnValue   = json_encode(
      [
      'translated' => $text,
      'approved'   => false,
      'approver'   => null
      ]
    );
    $translationCf->insert(
      $key,
      ["lang:$lang" => $columnValue]
    );

    if($projectId)
    {
      $translationCf->insert(
        $key,
        ["used_by:$projectId" => time()]
      );
    }
  }
}
