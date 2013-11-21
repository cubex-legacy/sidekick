<?php
/**
 * @author oke.ugwu
 */

namespace Sidekick\Applications\Rosetta\Controllers;

use Cubex\Facade\Auth;
use Cubex\Facade\Redirect;
use Cubex\Core\Http\Response;
use Cubex\View\Templates\Errors\Error404;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Rosetta\Views\RosettaIndex;
use Sidekick\Applications\Rosetta\Views\Translations;
use Sidekick\Components\Rosetta\Helpers\TranslatorHelper;
use Sidekick\Components\Rosetta\Mappers\PendingTranslation;
use Sidekick\Components\Rosetta\Mappers\Translation;

class DefaultController extends BaseControl
{
  protected $_titlePrefix = 'Rosetta';

  public function preRender()
  {
    parent::preRender();
    $this->requireCss('base');
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

    $lang = $this->request()->getVariables('lang', '');
    if($mostPopular)
    {
      $lang = $this->request()->getVariables(
        'lang',
        $mostPopular->lang
      );
    }

    $pendingTranslations = PendingTranslation::collection(
      ['lang' => $lang]
    );
    return new RosettaIndex($lang, $pendingTranslations);
  }

  public function renderApprove()
  {
    $rowKey = $this->getStr('rowKey');
    $lang   = $this->getStr('lang');

    $this->_approve($rowKey, $lang);

    Redirect::to($this->baseUri() . '?lang=' . $lang)->now();
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
    $pendingTranslation = new PendingTranslation([$rowKey, $lang]);
    $pendingTranslation->delete();
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
  }

  public function renderDelete()
  {
    $rowKey = $this->getStr('rowKey');
    $this->_deleteAllTranslation($rowKey);
    Redirect::to($this->baseUri())->now();
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
    $pendingTranslation = new PendingTranslation([$rowKey, $lang]);
    $pendingTranslation->delete();
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

  public function renderSearch()
  {
    $this->requireJs('search');
    //TODO: implement once elastic search is ready
  }

  public function renderTranslations()
  {
    $this->requireJs('editing');
    $rowKey       = $this->getStr('rowKey');
    $translations = new Translation($rowKey);
    if($translations->exists())
    {
      return new Translations($rowKey, $translations);
    }
    return new Error404();
  }

  public function getRoutes()
  {
    return [
      '/'                          => 'index',
      '/approve'                   => 'approve',
      '/approve/:rowKey/:lang'     => 'approve',
      '/retranslate/:rowKey/:lang' => 'retranslate',
      '/delete/:rowKey'            => 'delete',
      '/translations/:rowKey'      => 'translations',
      '/search/'                   => 'search',
      '/search/:term/'             => 'search',
      '/edit/'                     => 'edit',
    ];
  }
}
