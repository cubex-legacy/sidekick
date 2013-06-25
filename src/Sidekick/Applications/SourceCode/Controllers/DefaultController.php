<?php
/**
 * Author: oke.ugwu
 * Date: 25/06/13 11:06
 */

namespace Sidekick\Applications\SourceCode\Controllers;

use \Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\SourceCode\Views\SourceCodeView;

class DefaultController extends BaseControl
{
  public function renderIndex()
  {
    echo "welcome to source code viewer";
    $sourceFile = $this->getStr('sourceFile');
    $lineNumber = $this->getInt('lineNumber');
    return new SourceCodeView($sourceFile, $lineNumber);
  }

  public function getRoutes()
  {
    return [
      '/:lineNumber/(?<sourceFile>.*)/' => 'index'
    ];
  }
}
