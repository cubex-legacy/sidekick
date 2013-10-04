<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Versions;

use Cubex\Helpers\Strings;
use Sidekick\Components\Enums\ApprovalState;

class VersionsViewHelper
{
  public static function colourState(
    $versionState, $default = 'Pending', $returnData = false
  )
  {
    $text = Strings::humanize($versionState);
    switch($versionState)
    {
      case ApprovalState::APPROVED:
        $class = 'success';
        break;
      case ApprovalState::REJECTED:
        $class = 'error';
        break;
      case ApprovalState::REVIEW:
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
