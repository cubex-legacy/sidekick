<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Controllers;

class ComposedController extends PhuseController
{
  public function renderIndex()
  {
    $packages = [];

    $versions                       = [];
    $versions['dev-master']         = [
      "name"    => 'smarty/smarty',
      "version" => '3.1.7',
      "dist"    => [
        'url'  => 'http://www.smarty.net/files/Smarty-3.1.7.zip',
        'type' => 'zip'
      ]
    ];
    $packages['vendor/packagename'] = $versions;

    return ['packages' => $packages];
  }
}
