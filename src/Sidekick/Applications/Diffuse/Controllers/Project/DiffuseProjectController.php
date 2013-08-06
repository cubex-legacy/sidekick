<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Project;

use Cubex\View\HtmlElement;
use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use Sidekick\Applications\Diffuse\Views\Project\VersionsList;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\Version;

class DiffuseProjectController extends DiffuseController
{
  public function renderIndex()
  {
    $projectId = $this->getInt('projectId');

    $versions = Version::collection(['project_id' => $projectId])
    ->load()
    ->setOrderBy("updated_at", "DESC");
    return new VersionsList($versions, $projectId);
  }

  public function getNav($page = "")
  {
    $project = $this->getInt("projectId");
    $version = $this->getInt("versionId");
    $active  = ["class" => "active"];
    $list    = new HtmlElement("ul", ["class" => "nav nav-tabs"]);
    $list->nestElement(
      "li",
      ($page == "") ? $active : [],
      "<a href='/diffuse/$project/v/$version/'>Details</a>"
    );
    $list->nestElement(
      "li",
      ($page == "changelog") ? $active : [],
      "<a href='/diffuse/$project/v/$version/changelog'>Change Log</a>"
    );
    $platforms = Platform::collection()->loadAll();
    foreach($platforms as $platform)
    {
      $list->nestElement(
        "li",
        ($page == $platform->name) ? $active : [],
      "<a href='/diffuse/$project/v/$version/p/" . $platform->id . "'>" . $platform->name . "</a>"
      );
    }
    return $list;
  }
}
