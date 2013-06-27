<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Controllers;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Core\Http\Response;
use Sidekick\Components\Phuse\Mappers\Package;
use Sidekick\Components\Phuse\PhuseHelper;

class ComposedController extends PhuseController
{
  public function renderIndex()
  {
    $versions    = $packages = $releases = [];
    $rawPackages = Package::collection()->preFetch("releases");

    /**
     * @var $pack \Sidekick\Components\Phuse\Mappers\Package
     * @var $rel  \Sidekick\Components\Phuse\Mappers\Release
     */
    foreach($rawPackages as $pack)
    {
      foreach($pack->releases() as $rel)
      {
        $releases[] = ['name' => $pack->name, 'version' => $rel->version];

        $updateTag               = strtotime($rel->updatedAt);
        $rawComposer             = (array)$pack->rawComposer;
        $append                  = [
          "name"    => $pack->name,
          "version" => $rel->version,
          "time"    => date("Y-m-d H:i:s", $updateTag),
          "dist"    => [
            'url'       => url() . '/download/' .
            ($pack->name . '/' . $rel->version . '/' . $updateTag),
            'type'      => 'zip',
            'shasum'    => "",
            'reference' => $rel->zipHash
          ],
        ];
        $versions[$rel->version] = $rawComposer + $append;
        $packages[$pack->name]   = $versions;
      }
    }
    return ['packages' => $packages];
  }

  public function renderDownload($package, $version = 'dev-master')
  {
    /**
     * @var $pack \Sidekick\Components\Phuse\Mappers\Package
     * @var $rel  \Sidekick\Components\Phuse\Mappers\Release
     */
    $pack = Package::loadWhere(['name' => $package]);
    if($pack === null || !$pack->exists())
    {
      throw new \Exception("The package $package does not exist");
    }

    $rel = $pack->releases()->whereEq('version', $version)->first();

    if($rel === null || !$rel->exists())
    {
      throw new \Exception("The version $version does not exist on $package");
    }
    else
    {
      $filename = PhuseHelper::safePackageName($package);
      $filename .= '-' . $version . '.zip';
      return $this->_response->createDownload($rel->zipLocation, $filename);
    }
  }

  public function getRoutes()
  {
    return [
      '/download/(?P<package>.*)/(?P<version>[a-zA-Z0-9_\.\-]*)/[0-9]+/' => 'download'
    ];
  }
}
