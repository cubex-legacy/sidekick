<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Scripts;

interface FortifyScript
{
  public function configure($configuration);

  public function execute();
}
