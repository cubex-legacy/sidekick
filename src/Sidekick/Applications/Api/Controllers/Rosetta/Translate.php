<?php
/**
 * Author: oke.ugwu
 * Date: 19/11/13 13:25
 */

namespace Sidekick\Applications\Api\Controllers\Rosetta;

use Sidekick\Applications\Api\Controllers\ApiController;
use Sidekick\Components\Rosetta\Helpers\TranslatorHelper;
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

      $projectId = empty($params['projectId']) ? 0 : $params['projectId'];

      $key           = md5($params['text']) . strlen($params['text']);
      $sourceLang    = $params['source'];
      $targetLang    = $params['target'];
      $translationCf = Translation::cf();

      $data = $translationCf->get($key, ['lang:' . $targetLang]);

      if(!count($data))
      {
        TranslatorHelper::saveTranslation(
          $key,
          $params['text'],
          $sourceLang,
          $projectId
        );

        $translatedText = TranslatorHelper::translate(
          $params['text'],
          $sourceLang,
          $targetLang
        );

        TranslatorHelper::saveTranslation(
          $key,
          $translatedText,
          $targetLang,
          $projectId
        );
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

      return [
        'error'  => [
          'code'    => 200,
          'message' => 'Text translated successfully'
        ],
        'result' => [
          'text' => $translatedText
        ]
      ];
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

  public function getRoutes()
  {
    return [
      'translate/(.*)' => 'translate'
    ];
  }
}
