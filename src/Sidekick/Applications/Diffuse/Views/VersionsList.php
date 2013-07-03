<?php
/**
 * Author: oke.ugwu
 * Date: 02/07/13 12:13
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\TemplatedViewModel;

class VersionsList extends TemplatedViewModel
{
  protected $_versions;
  protected $_projectId;

  public function __construct($versions, $projectId)
  {
    $this->_versions  = $versions;
    $this->_projectId = $projectId;
  }

  /**
   * @return \Sidekick\Components\Diffuse\Mappers\Version[]
   */
  public function getVersions()
  {
    return $this->_versions;
  }
}
