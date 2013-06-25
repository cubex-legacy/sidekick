<?php
/**
 * Author: oke.ugwu
 * Date: 25/06/13 14:52
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;

class BuildDetailsView extends TemplatedViewModel
{
  /**
   * @var \Sidekick\Components\Fortify\Mappers\BuildRun
   */
  public $run;

  public function __construct($run)
  {
    $this->run = $run;
  }
}
