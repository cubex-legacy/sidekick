<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Configuration;

use Cubex\Form\Form;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Projects\Mappers\ProjectUser;

class ApprovalConfigurationView extends TemplatedViewModel
{
  protected $_project;
  protected $_approvals;
  protected $_platforms;
  protected $_users;
  protected $_form;

  /**
   * @param Project                                  $project
   * @param RecordCollection|ApprovalConfiguration[] $approvalConfigs
   * @param RecordCollection|Platform[]              $platforms
   * @param RecordCollection|ProjectUser[]           $users
   */
  public function __construct(
    Project $project, $approvalConfigs, $platforms, $users
  )
  {
    $this->_project   = $project;
    $this->_approvals = $approvalConfigs;
    $this->_platforms = $platforms;
    $this->_users     = $users;
    $roles            = [];
    foreach($users as $user)
    {
      foreach($user->roles as $role)
      {
        $roles[$role]++;
      }
    }
    $this->_roles = $roles;
    $form         = new Form('ApprovalConfigForm');

    foreach($platforms as $platform)
    {
      foreach($roles as $role => $roleCount)
      {
        $options = ['none' => 'None (0)'];
        if($roleCount > 0)
        {
          $options['one'] = 'One (1)';
        }
        if($roleCount > 1)
        {
          $options['two'] = 'Two (2)';
        }
        $options['quorum'] = 'Quorum ' . (ceil($roleCount / 2) + 1) . ')';
        $options['all']    = 'All (' . $roleCount . ')';

        $current = new ApprovalConfiguration(
          [
          $platform->id(),
          $project->id(),
          $role
          ]
        );

        $form->addSelectElement(
          $platform->id() . '-' . $role,
          $options,
          $current->consistencyLevel,
          Form::LABEL_NONE
        );
      }
    }
    $this->_form = $form;
  }

  public function project()
  {
    return $this->_project;
  }

  public function approvals()
  {
    return $this->_approvals;
  }

  public function platforms()
  {
    return $this->_platforms;
  }

  public function users()
  {
    return $this->_users;
  }

  public function roles()
  {
    return $this->_roles;
  }

  public function form()
  {
    return $this->_form;
  }
}
