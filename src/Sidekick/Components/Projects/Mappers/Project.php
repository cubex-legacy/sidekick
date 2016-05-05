<?php
/**
 * @author: brooke.bryan
 *        Component: Projects
 */
namespace Sidekick\Components\Projects\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Cubex\Sprintf\ParseQuery;
use Sidekick\Components\Repository\Mappers\Repository;
use Sidekick\Components\Repository\Mappers\Source;

class Project extends RecordMapper
{
  public $name;
  public $parentId;
  public $description;

  public static function getProjects($projectId = null)
  {
    $projectCollection = self::collection();
    $projects          = $projectCollection->loadWhere(
      "%C %=d",
      "parent_id",
      $projectId
    )->setOrderBy('name');

    return $projects;
  }

  public static function getSubProjectsCount()
  {
    $result = self::conn()->getKeyedRows(
      ParseQuery::parse(
        self::conn(),
        "SELECT id, (
          SELECT count(*) FROM %T
          WHERE parent_id= p.id
        ) as sub_projects
        FROM %T p
        ORDER BY name ASC",
        self::tableName(),
        self::tableName()
      )
    );

    return $result;
  }

  public function parent()
  {
    return $this->belongsTo(new Project(), 'parent_id');
  }

  public function repositories()
  {
    return $this->hasMany(new Repository());
  }

  /**
   * @param string $branch
   *
   * @return Source
   */
  public function repository($branch = "master")
  {
    return $this->hasMany(new Source())->whereEq("branch", $branch)->first();
  }
}
