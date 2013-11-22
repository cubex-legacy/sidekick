<?php
/**
 * Author: oke.ugwu
 * Date: 25/06/13 12:37
 */

namespace Sidekick\Applications\SourceCode\Views;

use Cubex\Dispatch\Dependency\Resource\TypeEnum;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class SourceCodeView extends ViewModel
{
  protected $_sourceFile;
  protected $_lineNumber;

  public function __construct($sourceFile, $lineNumber)
  {
    $this->_sourceFile = $sourceFile;
    $this->_lineNumber = $lineNumber;
  }

  public function render()
  {
    $this->requireJsLibrary('jquery');
    $this->requireJs(
      'https://google-code-prettify.googlecode.com/svn/'
      . 'loader/run_prettify.js?skin=sons-of-obsidian&callback=highlightLine'
    );

    $fileName = basename($this->_sourceFile);
    $title    = 'Source Code: ' . $fileName;
    if(file_exists($this->_sourceFile))
    {
      //go into snippet mode
      $offset           = 10;
      $snippetThreshold = 200;
      $fileLines        = file($this->_sourceFile);
      $lineToHighlight  = $this->_lineNumber - 1;
      if(count($fileLines) > $snippetThreshold && $this->_lineNumber)
      {
        $title .= ' (Snippet Mode)';
        $startLine = $this->_lineNumber - $offset;
        $startLine = ($startLine > 0) ? $startLine : 1;

        $endLine = $this->_lineNumber + $offset;
        $endLine = ($endLine > 0) ? $endLine : $this->_lineNumber;

        $sourceText = '';
        for($i = $startLine; $i <= $endLine; $i++)
        {
          $sourceText .= $fileLines[$i];
        }

        $lineToHighlight = ($this->_lineNumber > $offset) ?
          $offset : $lineToHighlight;

        $class = 'prettyprint lang-scm linenums:' . $startLine;
      }
      else
      {
        $sourceText = htmlentities(file_get_contents($this->_sourceFile));
        //taking into account the zero-based index
        $class = 'prettyprint lang-scm linenums';
      }

      $code = new HtmlElement(
        'pre',
        ['class' => $class],
        $sourceText
      );

      $this->addCssBlock(
        'ol.linenums {margin: 0 0 10px 50px;}'
      );

      $this->requireJs('main');
      $this->addJsBlock("var lineNumber = $lineToHighlight;");
    }
    else
    {
      $code = "<p><strong>Sorry I could not find the file: </strong>" .
        "<i>$this->_sourceFile</i></p>";
    }

    return new RenderGroup(
      '<h1>' . $title . '</h1>',
      $code
    );
  }
}
