<?php
/**
 * @author Luke',Rodham
 */

namespace Sidekick\Components\Rosetta\Helpers;

class LanguageCodes
{
  private static $_languageCodes = array(
    'af' => 'Afrikaans',
    'ar' => 'Arabic',
    'bg' => 'Bulgarian',
    'ca' => 'Catalan',
    'cs' => 'Czech',
    'da' => 'Danish',
    'de' => 'German',
    'el' => 'Greek',
    'en' => 'English',
    'es' => 'Spanish',
    'et' => 'Estonian',
    'fi' => 'Finnish',
    'fr' => 'French',
    'gl' => 'Galician',
    'he' => 'Hebrew',
    'hi' => 'Hindi',
    'hr' => 'Croatian',
    'hu' => 'Hungarian',
    'id' => 'Indonesian',
    'it' => 'Italian',
    'ja' => 'Japanese',
    'ko' => 'Korean',
    'ka' => 'Georgian',
    'lt' => 'Lithuanian',
    'lv' => 'Latvian',
    'ms' => 'Malay',
    'nl' => 'Dutch',
    'no' => 'Norwegian',
    'pl' => 'Polish',
    'pt' => 'Portuguese',
    'ro' => 'Romanian',
    'ru' => 'Russian',
    'sk' => 'Slovak',
    'sl' => 'Slovenian',
    'sq' => 'Albanian',
    'sr' => 'Serbian',
    'sv' => 'Swedish',
    'th' => 'Thai',
    'tr' => 'Turkish',
    'uk' => ' Ukrainian',
    'zh' => 'Chinese'
  );

  /**
   * @param string $code
   *
   * @return string
   *
   */
  public static function getLanguageFromCode($code)
  {
    return isset(self::$_languageCodes[$code]) ?
      self::$_languageCodes[$code] : 'Unknown';
  }

  public static function getLanguageOptions($options)
  {
    return array_intersect_key(self::$_languageCodes, array_flip($options));
  }

  public static function getAllLanguages()
  {
    return self::$_languageCodes;
  }
}
