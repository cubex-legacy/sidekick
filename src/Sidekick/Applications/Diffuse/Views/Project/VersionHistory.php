<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 23/07/13
 * Time: 10:20
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views\Project;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\Deployment;

class VersionHistory extends TemplatedViewModel
{
  protected $_versionID;
  protected $_nav;

  public function __construct($versionID, $nav)
  {
    $this->_versionID = $versionID;
    $this->_nav       = $nav;
  }

  public function getNav()
  {
    return $this->_nav;
  }

  public function getVersionHistory()
  {
    $deployments = Deployment::collection(
                     ['version_id' => $this->_versionID]
                   )->setOrderBy('created_at', 'DESC');
    return $deployments;
  }
}
