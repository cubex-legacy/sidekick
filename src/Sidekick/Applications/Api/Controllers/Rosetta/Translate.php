<?php
/**
 * Author: oke.ugwu
 * Date: 19/11/13 13:25
 */

namespace Sidekick\Applications\Api\Controllers\Rosetta;

use Cubex\I18n\Translator\GoogleTranslator;
use Sidekick\Applications\Api\Controllers\ApiController;
use Sidekick\Components\Rosetta\Mappers\PendingTranslation;
use Sidekick\Components\Rosetta\Mappers\Translation;

class Translate extends ApiController
{

  public function renderTranslate()
  {
    //always expect $_POST but fall back to $_GET
    $params = $this->request()->postVariables(
      ['text', 'source', 'target'],
      $this->request()->getVariables()
    );

    if(isset($params['text'], $params['source'], $params['target']))
    {
      if(empty($params['text']))
      {
        throw new \Exception("No Text to Translate", 400);
      }

      $projectId = empty($params['projectId'])? 0 : $params['projectId'];

      $key           = md5($params['text']) . strlen($params['text']);
      $sourceLang    = $params['source'];
      $targetLang    = $params['target'];
      $translationCf = Translation::cf();

      $data = $translationCf->get($key, ['lang:' . $targetLang]);

      if(!count($data))
      {
        $this->_saveTranslation($key, $params['text'], $sourceLang, $projectId);

        //get translated text from google
        $gt             = new GoogleTranslator();
        $translatedText = $gt->translate(
          $params['text'],
          $sourceLang,
          $targetLang
        );

        $this->_saveTranslation($key, $translatedText, $targetLang, $projectId);
      }
      else
      {
        $translatedText = json_decode(current($data))->translated;
      }

      if(!count($data) || !json_decode(current($data))->approved)
      {
        $pendingTranslation         = new PendingTranslation();
        $pendingTranslation->rowKey = $key;
        $pendingTranslation->lang   = $targetLang;
        $pendingTranslation->saveChanges();
      }

      return ['text' => $translatedText];
    }
    else
    {
      throw new \Exception(
        "Incomplete Parameters. Make sure you provide a " .
        "text, source and target language",
        400
      );
    }
  }

  private function _saveTranslation($key, $text, $lang, $projectId)
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

  public function getRoutes()
  {
    return [
      'translate/(.*)' => 'translate'
    ];
  }
}
