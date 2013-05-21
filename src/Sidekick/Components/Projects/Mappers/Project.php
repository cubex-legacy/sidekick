<?php
/**
 * @author: brooke.bryan
 *        Component: Projects
 */
namespace Sidekick\Components\Projects\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Project extends RecordMapper
{
  public $name;
  public $description;
  public $parentId;


  public static function getProjects($projectId = null)
  {
    $projectCollection = self::collection();
    if($projectId === null)
    {
      $projects = $projectCollection->loadWhere("%C IS NULL", "parent_id");
    }
    else
    {
      $projects = $projectCollection->loadWhere(
        ["parent_id" => $projectId]
      );
    }

    return $projects;
  }

  public static function getSubProjectsCount()
  {
    $result = self::conn()->getKeyedRows(
      "SELECT id, (
        SELECT count(*) FROM " . self::tableName() . " WHERE parent_id= p.id
        ) as sub_projects
        FROM " . self::tableName() . " p
      "
    );

    return $result;
  }
}
