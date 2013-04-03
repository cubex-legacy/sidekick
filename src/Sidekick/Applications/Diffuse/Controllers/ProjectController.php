<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Sidekick\Components\Diffuse\Enums\RepositoryProvider;
use Sidekick\Components\Diffuse\Enums\BuildLevel;
use Sidekick\Components\Diffuse\Mappers\Build;
use Sidekick\Components\Diffuse\Mappers\BuildCommand;
use Sidekick\Components\Diffuse\Mappers\BuildSource;
use Sidekick\Components\Diffuse\Mappers\BuildsCommands;
use Sidekick\Components\Diffuse\Mappers\BuildsProjects;
use Sidekick\Components\Projects\Mappers\Project;

class ProjectController extends DiffuseController
{
  public function renderIndex()
  {
    $project              = new Project(1);
    $project->name        = "Cubex";
    $project->description = "Cubex Build";
    $project->saveChanges();

    $build             = new Build(1);
    $build->buildLevel = BuildLevel::MINOR;
    $build->name       = "Minor Build";
    $build->saveChanges();

    $proBuild = new BuildsProjects($build, $project);
    $proBuild->saveChanges();

    $command              = new BuildCommand(1);
    $command->command     = 'find . -name *.php -exec php-win -l {} ";"';
    $command->description = "PHP Lint Check Directory";
    $command->saveChanges();

    $commandTwo              = new BuildCommand(2);
    $commandTwo->command     = 'phpunit';
    $commandTwo->description = "Run PHPUnit Tests";
    $commandTwo->saveChanges();

    $bc = new BuildsCommands($build, $command);
    $bc->saveChanges();

    $bc               = new BuildsCommands($build, $commandTwo);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $source                 = new BuildSource(1);
    $source->fetchUrl       = "https://github.com/qbex/Cubex.git";
    $source->repositoryType = RepositoryProvider::GIT;
    $source->saveChanges();
  }
}
