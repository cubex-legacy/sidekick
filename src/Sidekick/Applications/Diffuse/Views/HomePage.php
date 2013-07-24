<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 17/07/13
 * Time: 17:14
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\Version;

class HomePage extends TemplatedViewModel
{
  public function getAttentionVersions()
  {
    $versions = Version::collection();
    return $versions->loadWhere(
      [
      "version_state" => "pending"
      ]
    );
  }
}