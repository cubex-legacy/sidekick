<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Configuration;

use Cubex\Helpers\Strings;
use Cubex\View\TemplatedViewModel;
use Cubex\Form\Form;
use Cubex\Mapper\Database\RecordCollection;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformProjectConfig;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Projects\Mappers\ProjectUser;

class PlatformConfigurationView extends TemplatedViewModel
{
  protected $_project;
  protected $_configs;
  protected $_platforms;
  protected $_form;

  /**
   * @param Project                                  $project
   * @param RecordCollection|PlatformProjectConfig[] $configs
   * @param RecordCollection|Platform[]              $platforms
   */
  public function __construct(
    Project $project, $configs, $platforms
  )
  {
    $this->_project   = $project;
    $this->_configs   = $configs;
    $this->_platforms = $platforms;
    $form             = new Form('PlatformConfigForm');

    foreach($platforms as $platform)
    {
      $cnf = new PlatformProjectConfig([$platform->id(), $project->id()]);
      foreach($this->configurables() as $itm)
      {
        $key = $platform->id() . '-' . $itm;
        $form->addTextElement($key, $cnf->$itm);
        $form->getElement($key)->setLabel(Strings::titleize($itm));
      }
    }
    $this->_form = $form;
  }

  public function project()
  {
    return $this->_project;
  }

  public function configs()
  {
    return $this->_configs;
  }

  public function platforms()
  {
    return $this->_platforms;
  }

  public function form()
  {
    return $this->_form;
  }

  public function configurables()
  {
    return ["cookieName", "testUrl"];
  }
}
