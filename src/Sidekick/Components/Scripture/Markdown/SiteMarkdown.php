<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Scripture\Markdown;

use dflydev\markdown\MarkdownExtraParser;

class SiteMarkdown extends MarkdownExtraParser
{
  const DEFAULT_TAB_WIDTH = 2;

  function _doCodeBlocks_callback($matches)
  {
    $codeblock = $matches[1];

    $codeblock = $this->outdent($codeblock);
    $codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);

    # trim leading newlines and trailing newlines
    $codeblock = preg_replace('/\A\n+|\n+\z/', '', $codeblock);
    return $this->wrapCodeBlock($codeblock);
  }

  function _doFencedCodeBlocks_callback($matches)
  {
    $codeblock = $matches[2];
    $codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);
    $codeblock = preg_replace_callback(
      '/^\n+/',
      array(&$this, '_doFencedCodeBlocks_newlines'),
      $codeblock
    );
    return $this->wrapCodeBlock($codeblock);
  }

  public function wrapCodeBlock($code)
  {
    $code = "<pre class='prettyprint'><code>$code\n</code></pre>";
    return "\n\n" . $this->hashBlock($code) . "\n\n";
  }
}
