<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Controllers;

use Cubex\Core\Http\Response;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Phuse\Views\PackageResults;
use Sidekick\Applications\Phuse\Views\PackageView;
use Sidekick\Applications\Phuse\Views\PhuseIndex;
use Sidekick\Components\Phuse\Mappers\Package;
use Sidekick\Components\Phuse\Mappers\Release;

class PhuseController extends BaseControl
{
  public function preRender()
  {
    parent::preRender();
    $this->requireCss('base');
    $this->requireJs('search');
  }

  public function renderIndex()
  {
    return new PhuseIndex();
  }

  public function renderViewPackage()
  {
    $packageId = $this->getInt('packageId');
    $package = new Package($packageId);
    $releases = Release::collection()->loadWhere(['package_id' => $packageId]);
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
      'search/'         => 'search',
      'search/(?<query>.*)/'   => 'search',
      'view/:packageId' => 'viewPackage'
    ];
  }
}
