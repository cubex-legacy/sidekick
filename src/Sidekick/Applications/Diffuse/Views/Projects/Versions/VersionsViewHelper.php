<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Versions;

use Cubex\Helpers\Strings;
use Sidekick\Components\Diffuse\Enums\VersionState;

class VersionsViewHelper
{
  public static function colourState(
    $versionState, $default = 'Pending', $returnData = false
  )
  {
    $text = Strings::humanize($versionState);
    switch($versionState)
    {
      case VersionState::APPROVED:
        $class = 'success';
        break;
      case VersionState::REJECTED:
        $class = 'error';
        break;
      case VersionState::REVIEW:
        $class = 'warning';
        $text  = 'In Review';
        break;
      default:
        $class = 'info';
        $text  = $default;
        break;
    }
    if($returnData)
    {
      return ['class' => $class, 'text' => $text];
    }
    else
    {
      return '<span class="text-' . $class . '">' . $text . '</span>';
    }
  }
}
