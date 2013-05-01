<?php
/**
* @author: oke.ugwu
* Application: 
*/
namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\TemplatedViewModel;

class ProjectList extends TemplatedViewModel
{
  public function __construct($viewData)
  {
    foreach($viewData as $prop => $data)
    {
      $this->{$prop}  = $data;
    }
  }
}