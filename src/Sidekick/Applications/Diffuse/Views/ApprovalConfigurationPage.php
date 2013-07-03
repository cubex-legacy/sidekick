<?php
/**
 * Author: oke.ugwu
 * Date: 02/07/13 17:16
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\TemplatedViewModel;

class ApprovalConfigurationPage extends TemplatedViewModel
{
  protected $_form;
  protected $_config;
  public $projectId;
  public $role;

  public function __construct($form, $config, $projectId, $role = null)
  {
    $this->_config   = $config;
    $this->_form     = $form;
    $this->projectId = $projectId;
    $this->role      = $role;
  }

  public function getConfig()
  {
    return $this->_config;
  }

  public function form()
  {
    return $this->_form;
  }
}
