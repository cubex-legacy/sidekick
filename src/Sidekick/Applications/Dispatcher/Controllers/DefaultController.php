<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Dispatcher\Controllers;

class DefaultController extends DispatcherController
{
  public function renderIndex()
  {
    echo "My Dispatcher App";
  }
}
