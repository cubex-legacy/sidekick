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
      . 'loader/run_prettify.js?skin=sons-of-obsidian&callback=linehigh'
    );

    $sourceText = htmlentities(file_get_contents($this->_sourceFile));
    $fileName   = basename($this->_sourceFile);
    $code       = new HtmlElement(
      'pre',
      ['class' => 'prettyprint lang-scm linenums'],
      $sourceText
    );
    //zero-based index
    $line = $this->_lineNumber - 1;

    $this->addCssBlock(
      'ol.linenums {margin: 0 0 10px 50px;}'
    );

    $this->addJsBlock(
      <<<JSB
          window.exports = {
            linehigh: function(){
              line = \$(\$(".linenums li")[$line]);
              line.css("background-color","brown");
              $('html, body').animate({
                scrollTop: line.offset().top-43
              }, 2000);
          }
        };
JSB
    );

    return new RenderGroup(
      '<h1>' . $fileName . '</h1>',
      $code
    );
  }
}
