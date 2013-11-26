<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Widgets\Gravatar;

use Cubex\View\HtmlElement;
use Cubex\View\Widgets\Widget;

class Gravatar extends Widget
{
  public static function create($email, $size = 50)
  {
    return new HtmlElement(
      'img',
      [
      'src'   => 'http://www.gravatar.com/avatar/' .
      md5(strtolower($email)) . '?s=' . $size,
      'class' => 'media-object'
      ]
    );
  }
}
