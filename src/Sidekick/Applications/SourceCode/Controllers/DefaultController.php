<?php
/**
 * Author: oke.ugwu
 * Date: 25/06/13 11:06
 */

namespace Sidekick\Applications\SourceCode\Controllers;

use Cubex\View\Templates\Errors\Error404;
use \Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\SourceCode\Views\SourceCodeView;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Repository\Mappers\Source;

class DefaultController extends BaseControl
{
  public function renderIndex()
  {
    list($fileName, $lineNumber) = explode(';', $this->getStr('sourceFile'));
    $type   = $this->getStr('type');
    $typeId = $this->getInt('typeId');

    $sourceFile = '';
    switch($type)
    {
      case 'build':
        $sourceFile = CUBEX_PROJECT_ROOT . DS . 'builds' . DS . $typeId .
          DS . 'sourcecode' . DS . $fileName;
        break;
      case 'repo':
        $repo       = new Source($typeId);
        $sourceFile = $repo->localpath . DS . $fileName;
        break;
      case 'diffuse':
        $version    = new Version($typeId);
        $sourceFile = VersionHelper::sourceLocation($version) . $fileName;
        break;
    }

    if($sourceFile)
    {
      return new SourceCodeView($sourceFile, $lineNumber);
    }
    return new Error404();
  }

  public function getRoutes()
  {
    return [
      '/:type/:typeId@num/(?<sourceFile>.*)/' => 'index'
    ];
  }
}
