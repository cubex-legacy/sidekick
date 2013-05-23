<?php
/**
 * @author: brooke.bryan
 *        Component: Projects
 */
namespace Sidekick\Components\Projects\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Cubex\Sprintf\ParseQuery;

class Project extends RecordMapper
{
  public $name;
  public $description;
  public $parentId;


  public static function getProjects($projectId = null)
  {
    $projectCollection = self::collection();
    $projects          = $projectCollection->loadWhere(
      "%C %=d",
      "parent_id",
      $projectId
    );

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
        FROM %T p",
        self::tableName(),
        self::tableName()
      )
    );

    return $result;
  }
}
