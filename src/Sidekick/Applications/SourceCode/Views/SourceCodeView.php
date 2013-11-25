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
  protected $_totalLines;

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
      $offset            = 10;
      $snippetThreshold  = 200;
      $snippetMaxLines   = 20;
      $fileLines         = file($this->_sourceFile);
      $lineToHighlight   = $this->_lineNumber - 1;
      $this->_totalLines = count($fileLines);
      if($this->_totalLines > $snippetThreshold && $this->_lineNumber)
      {
        $title .= ' (Snippet Mode)';
        $startLine = $this->_lineNumber - $offset;
        $startLine = ($startLine > 0) ? $startLine : 0;

        $lines = array_slice($fileLines, $startLine, $snippetMaxLines);
        $sourceText = $this->_buildSourceText($lines);

        $lineToHighlight = ($this->_lineNumber > $offset) ?
          $offset - 1 : $lineToHighlight;

        $lineNums = $startLine + 1;
        $class    = 'prettyprint lang-scm linenums:' . $lineNums;
      }
      else
      {
        $sourceText = $this->_buildSourceText($fileLines);
        //taking into account the zero-based index
        $class = 'prettyprint lang-scm linenums';
      }

      $sourceText = htmlentities(
        $sourceText,
        ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE | ENT_DISALLOWED
      );

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
      $code,
      '<p class="pull-right"><small>Total Lines: ' . $this->_totalLines . '</p>'
    );
  }

  /**
   * Safely build source lines of code
   * When the first 2 lines of the snippet are
   * blank lines (just a new line), code pretty print does not work well
   * as it merges it with the next line.
   * We make it work by changing all such lines to a SPACE.NEW_LINE
   *
   * @param array $lines
   *
   * @return string
   */
  private function _buildSourceText($lines)
  {
    $sourceText = '';
    foreach($lines as $line)
    {
      $line = trim($line);
      if($line == '')
      {
        $line = ' ';
      }
      $line = $line . PHP_EOL;

      $sourceText .= $line;
    }

    return $sourceText;
  }
}
