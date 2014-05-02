<?php
/**
 * @author oke.ugwu
 */

namespace Sidekick\Applications\Rosetta\Controllers;

use Cubex\Facade\Auth;
use Cubex\Facade\Redirect;
use Cubex\Core\Http\Response;
use Cubex\View\RenderGroup;
use Cubex\View\Templates\Errors\Error404;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Controllers\ProjectAwareBaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Rosetta\Views\RosettaIndex;
use Sidekick\Applications\Rosetta\Views\Translations;
use Sidekick\Components\Helpers\Paginator;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Rosetta\Helpers\TranslatorHelper;
use Sidekick\Components\Rosetta\Mappers\PendingTranslation;
use Sidekick\Components\Rosetta\Mappers\Translation;

class DefaultController extends ProjectAwareBaseControl
{
  protected $_titlePrefix = 'Rosetta';
  private $_lang;

  public function preRender()
  {
    parent::preRender();
    $this->requireCss('base');
  }

  public function getSidebar()
  {
    return null;
  }

  public function renderIndex()
  {
    $this->requireJs('editing');

    //determine which language to pre select. This is based on the language
    //with the most translation entries
    /**
     * SELECT lang
     * FROM PendingTranslations
     * GROUP BY lang
     * ORDER BY COUNT(*) DESC
     * LIMIT 1
     */

    $pendingTranslation = PendingTranslation::collection();
    $pendingTranslation->setColumns(['lang']);
    $pendingTranslation->setGroupBy('lang');
    $pendingTranslation->setOrderBy('COUNT(*)', 'DESC');
    $pendingTranslation->setLimit(0, 1);
    $mostPopular = $pendingTranslation->first();

    $this->_lang = $this->getStr('lang', '');
    if($this->_lang == '' && $mostPopular)
    {
      $this->_lang = $this->request()->getVariables(
        'lang',
        $mostPopular->lang
      );
    }

    $projectId           = $this->getProjectId();
    $pendingTranslations = PendingTranslation::collection(
      ['lang' => $this->_lang, 'project_id' => $projectId]
    );

    $getLanguages           = PendingTranslation::collection(
      ['project_id' => $projectId]
    )->setGroupBy('lang');
    $availableLanguageCodes = $getLanguages->getUniqueField('lang');

    $page      = $this->getInt('page', 1);
    $perPage   = 100;
    $count     = $pendingTranslations->count();
    $baseUri   = $this->appBaseUri() . '/' . $this->_lang . '/page/';
    $paginator = $this->_getPaginator($page, $count, $perPage, $baseUri);
    $pendingTranslations->setLimit($paginator->getOffset(), $perPage);

    $pendingTranslationsData = $this->_pendingTranslationsData(
      $pendingTranslations
    );

    return new RenderGroup(
      $this->createView(
        new RosettaIndex(
          $projectId,
          $this->_lang,
          $pendingTranslationsData,
          $availableLanguageCodes
        )
      ),
      $paginator->getPager()
    );
  }

  private function _pendingTranslationsData($pendingTranslations)
  {
    $result = [];
    foreach($pendingTranslations as $pending)
    {
      $translationCf = Translation::cf();
      $translation   = $translationCf->get(
        $pending->rowKey,
        ['lang:' . $pending->lang, 'lang:en']
      );

      $englishData    = idx($translation, 'lang:en');
      $translatedData = idx($translation, 'lang:' . $pending->lang);

      if($englishData && $translatedData)
      {
        $row = [
          'rowKey'      => $pending->rowKey,
          'lang'        => $pending->lang,
          'english'     => json_decode($englishData)->translated,
          'translation' => json_decode($translatedData)->translated
        ];

        $result[] = (object)$row;
      }
    }

    return $result;
  }

  private function _getPaginator($pageNumber, $count, $perPage, $baseUri)
  {
    $paginator = new Paginator();
    $paginator->setNumResults($count);
    $paginator->setNumResultsPerPage($perPage);
    $paginator->setPage($pageNumber);
    $paginator->setUri($baseUri);
    return $paginator;
  }

  public function renderApprove()
  {
    $rowKey = $this->getStr('rowKey');
    $lang   = $this->getStr('lang');

    $this->_approve($rowKey, $lang);

    Redirect::to($this->appBaseUri() . '/' . $lang)->now();
  }

  public function ajaxApprove()
  {
    $rowKey = $this->request()->postVariables('rowKey');
    $lang   = $this->request()->postVariables('lang');

    $this->_approve($rowKey, $lang);

    return ['approved' => true];
  }

  private function _approve($rowKey, $lang)
  {
    //update translation in cassandra
    $translationCf = Translation::cf();
    $data          = $translationCf->get($rowKey, ['lang:' . $lang]);

    $columnValue = json_encode(
      [
        'translated' => json_decode(current($data))->translated,
        'approved'   => true,
        'approver'   => Auth::user()->getId()
      ]
    );
    $translationCf->insert(
      $rowKey,
      ["lang:$lang" => $columnValue]
    );

    //delete from pendingTranslations
    $pendingTranslations = PendingTranslation::collection(
      ['row_key' => $rowKey, 'lang' => $lang]
    );
    foreach($pendingTranslations as $pendingTranslation)
    {
      $pendingTranslation->delete();
    }
  }

  public function renderRetranslate()
  {
    $rowKey = $this->getStr('rowKey');
    $lang   = $this->getStr('lang');

    //get english data
    $translationCf = Translation::cf();
    $data          = $translationCf->get($rowKey, ['lang:en']);

    $englishText    = json_decode(current($data))->translated;
    $translatedText = TranslatorHelper::translate($englishText, 'en', $lang);

    $this->_deleteTranslation($rowKey, $lang);

    TranslatorHelper::saveTranslation(
      $rowKey,
      $translatedText,
      $lang
    );

    Redirect::to(
      $this->appBaseUri() . '/translations/' . $rowKey
    )->now();
  }

  public function renderDelete()
  {
    $rowKey = $this->getStr('rowKey');

    $this->_deleteAllTranslation($rowKey);
    Redirect::to($this->appBaseUri())->now();
  }

  private function _deleteAllTranslation($rowKey)
  {
    //update translation in cassandra
    $translationCf = Translation::cf();
    $translationCf->remove($rowKey);

    //delete from pendingTranslations
    $pendingTranslations = PendingTranslation::collection(
      ['row_key' => $rowKey]
    );
    foreach($pendingTranslations as $pendingTranslation)
    {
      $pendingTranslation->delete();
    }
  }

  private function _deleteTranslation($rowKey, $lang)
  {
    //update translation in cassandra
    $translationCf = Translation::cf();
    $translationCf->remove($rowKey, ['lang:' . $lang]);

    //delete from pendingTranslations
    $pendingTranslations = PendingTranslation::collection(
      ['row_key' => $rowKey, 'lang' => $lang]
    );
    foreach($pendingTranslations as $pendingTranslation)
    {
      $pendingTranslation->delete();
    }
  }

  public function ajaxEdit()
  {
    $rowKey = $this->request()->postVariables('rowKey');
    $lang   = $this->request()->postVariables('lang');
    $text   = $this->request()->postVariables('text');

    $translationCf = Translation::cf();
    $columnValue   = json_encode(
      [
        'translated' => $text,
        'approved'   => false,
        'approver'   => null
      ]
    );
    $translationCf->insert(
      $rowKey,
      ["lang:$lang" => $columnValue]
    );

    return ['updated' => true];
  }

  public function postSearch()
  {
    $term = trim($this->request()->postVariables('term'));
    if($term !== null)
    {
      if(strlen($term) > 32 && !stristr($term, ' '))
      {
        Redirect::to($this->appBaseUri() . '/translations/' . $term)->now();
      }
      else
      {
        $hash = md5($term) . strlen($term);
        Redirect::to($this->appBaseUri() . '/translations/' . $hash)->now();
      }
    }
    else
    {
      Redirect::back()->now();
    }
  }

  public function renderTranslations()
  {
    $this->requireJs('editing');
    $rowKey    = $this->getStr('rowKey');
    $rowKey    = str_replace('-', '', $rowKey);
    $projectId = $this->getProjectId();

    $translations = new Translation($rowKey);
    if($translations->exists())
    {
      return new Translations($projectId, $rowKey, $translations);
    }
    else
    {
      echo new Error404();
      echo "The Key '$rowKey' could not be located";
    }
  }

  public function getRoutes()
  {
    return [
      '/'                          => 'index',
      '/edit'                      => 'edit',
      '/delete/:rowKey'            => 'delete',
      '/approve'                   => 'approve',
      '/approve/:rowKey/:lang'     => 'approve',
      '/retranslate/:rowKey/:lang' => 'retranslate',
      '/translations/:rowKey'      => 'translations',
      '/search/'                   => 'search',
      '/:lang'                     => 'index',
      '/:lang/page/:page'          => 'index',
    ];
  }
}
