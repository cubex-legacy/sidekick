<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 03/06/13
 * Time: 17:30
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Phuse\Controllers;

use Cubex\Core\Http\Response;
use Sidekick\Applications\Phuse\Views\PackageResults;
use Sidekick\Applications\Phuse\Views\PackageView;
use Sidekick\Applications\Phuse\Views\PhuseIndex;
use Sidekick\Components\Phuse\Mappers\Package;
use Sidekick\Components\Phuse\Mappers\Release;

class DefaultController extends PhuseController
{
  public function preRender()
  {
    parent::preRender();
    $this->requireJs('search');
  }

  public function renderIndex()
  {
    return new PhuseIndex();
  }

  public function renderViewPackage()
  {
    $packageId = $this->getInt('packageId');
    $package   = new Package($packageId);
    $releases  = Release::collection()->loadWhere(['package_id' => $packageId]);
    return new PackageView($package, $releases);
  }

  public function renderSearch()
  {
    $query    = $this->getStr('query');
    $packages = Package::collection()->whereLike(
      'name',
      $query
    );
    return new Response($this->createView(new PackageResults($packages)));
  }


  public function getRoutes()
  {
    return [
      'search/'              => 'search',
      'search/(?<query>.*)/' => 'search',
      'view/:packageId'      => 'viewPackage'
    ];
  }
}
