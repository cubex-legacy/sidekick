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
}
