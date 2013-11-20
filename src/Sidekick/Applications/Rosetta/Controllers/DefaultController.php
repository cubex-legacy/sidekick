<?php
/**
 * @author oke.ugwu
 */

namespace Sidekick\Applications\Rosetta\Controllers;

use Cubex\Facade\Auth;
use Cubex\Facade\Redirect;
use Cubex\Core\Http\Response;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Rosetta\Views\RosettaIndex;
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
    $lang = $this->request()->getVariables('lang', 'de');
    return new RosettaIndex($lang);
  }

  public function renderApprove()
  {
    $rowKey = $this->getStr('rowKey');
    $lang   = $this->getStr('lang');

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

    Redirect::to($this->baseUri() . '?lang=' . $lang)->now();
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
    $lang   = $this->getStr('lang');
    $this->_deleteTranslation($rowKey, $lang);
    Redirect::to($this->baseUri() . '?lang=' . $lang)->now();
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

    print_r($text);

    exit(1);
  }

  public function renderSearch()
  {
    $this->requireJs('search');
    //TODO: implement once elastic search is ready
  }

  public function getRoutes()
  {
    return [
      '/'                          => 'index',
      '/approve/:rowKey/:lang'     => 'approve',
      '/delete/:rowKey/:lang'      => 'delete',
      '/retranslate/:rowKey/:lang' => 'retranslate',
      '/search/'                   => 'search',
      '/search/:term/'             => 'search',
      '/edit/'                     => 'edit',
    ];
  }
}
