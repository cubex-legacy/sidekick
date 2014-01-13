<?php
namespace Sidekick\Components\Fortify;

class FortifyHelper
{
  public static function buildPath($buildId)
  {
    return dirname(WEB_ROOT) . '/builds/' . $buildId;
  }

  public static function configAliasToClass($alias, $stage = 'analyse')
  {
    if(strpos($alias, '\\') !== false)
    {
      return $alias;
    }

    switch($stage)
    {
      case 'analyse':
        $prefix = '\Sidekick\Components\Fortify\Analysers';
        $class  = "$prefix\\$alias\\$alias";
        if(class_exists($class))
        {
          return $class;
        }
        break;
      case 'install':
      case 'uninstall':
      case 'script':
      case 'passed':
      case 'failed':
        $prefix = '\Sidekick\Components\Fortify\Processes';
        $class  = "$prefix\\$alias\\$alias";
        if(class_exists($class))
        {
          return $class;
        }
        break;
      default:
        throw new \Exception("'$stage' is not a valid group");
    }

    if(class_exists($alias))
    {
      return $alias;
    }
    else
    {
      throw new \Exception("Unable to locate class '$alias'");
    }
  }
}
