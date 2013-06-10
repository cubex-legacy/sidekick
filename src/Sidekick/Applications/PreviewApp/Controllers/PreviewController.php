<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\PreviewApp\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Cubex\View\TemplatedView;

class PreviewController extends WebpageController
{
  public function renderPreview()
  {
    $this->requireCss('base');
    $this->setTitle("Cubex Sidekick");
    return new TemplatedView('Logo', $this);
  }

  public function defaultAction()
  {
    return 'preview';
  }
}
