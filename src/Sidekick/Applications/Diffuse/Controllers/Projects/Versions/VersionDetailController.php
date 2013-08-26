<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Versions;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Form\Form;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionChangeLogView;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionDetailsView;
use Sidekick\Components\Repository\Mappers\Commit;

class VersionDetailController extends VersionsController
{
  public function renderIndex()
  {
    $view = new VersionDetailsView(
      $this->_version, $this->_platforms, $this->_platformStates
    );

    if($this->_version->fromCommitHash !== null && $this->_version->toCommitHash !== null)
    {
      $view->setCommits(
        Commit::collectionBetween(
          $this->_version->fromCommitHash,
          $this->_version->toCommitHash
        )
      );
    }

    return $this->_buildView($view);
  }

  public function renderChangeLog()
  {
    $form = new Form('ChangeLogForm');
    $form->addTextareaElement("changeLog", $this->_version->changeLog);
    $view = new VersionChangeLogView();
    $view->setForm($form);
    return $this->_buildView($view);
  }

  public function postChangeLog()
  {
    $this->_version->changeLog = $this->postVariables(
      "changeLog",
      $this->_version->changeLog
    );

    $this->_version->saveChanges();
    \Session::flash(
      "msg",
      new TransportMessage(
        "success",
        "Change Log Updated",
        "Congrats"
      )
    );
    return (new \Redirect())->to($this->baseUri());
  }

  public function getRoutes()
  {
    return [
      '/changelog' => 'changelog'
    ];
  }
}
