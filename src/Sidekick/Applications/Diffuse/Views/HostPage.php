<?php
/**
 * Author: oke.ugwu
 * Date: 04/07/13 11:55
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Form\Form;
use Cubex\View\TemplatedViewModel;

class HostPage extends TemplatedViewModel
{
  public $host;
  public $platforms;
  public $hostPlatforms;
  protected $_form;

  public function __construct($host, $platforms, $hostPlatforms)
  {
    $this->host          = $host;
    $this->platforms     = $platforms;
    $this->hostPlatforms = $hostPlatforms;
  }

  public function form()
  {
    if($this->_form == null)
    {
      $this->_form = new Form('hostPlatform', '/diffuse/hosts/add-platform');
      $this->_form->addAttribute('class', 'form-inline');
      $this->_form->setDefaultElementTemplate('{{input}}');
      $this->_form->addHiddenElement('hostId', $this->host->id());
      $this->_form->addSelectElement(
        'platformId',
        $this->platforms->getKeyPair('id', 'name')
      );
      $this->_form->addSubmitElement('Add Platform');

      $this->_form->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }

    return $this->_form;
  }
}
