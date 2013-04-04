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
    $command->command     = 'find';
    $command->args        = ['. -name *.php', '-exec php -l {} ";"'];
    $command->name        = 'PHP Lint';
    $command->description = "PHP Lint Check Directory";
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [3];
    $bc->saveChanges();

    $command              = new BuildCommand(2);
    $command->name        = 'PHP Unit';
    $command->command     = 'phpunit';
    $command->args        = [
      '--coverage-clover ../build/clover.sml',
      '--log-junit ../build/junit.xml'
    ];
    $command->description = "Run PHPUnit Tests";
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command              = new BuildCommand(3);
    $command->name        = 'Make Build Directory';
    $command->command     = 'mkdir';
    $command->args        = [
      '../build',
      '-p'
    ];
    $command->description = "Make Build Directory";
    $command->saveChanges();

    $bc = new BuildsCommands($build, $command);
    $bc->saveChanges();

    $command              = new BuildCommand(4);
    $command->command     = 'phploc';
    $command->args        = [
      '--log-csv ../build/phploc.csv',
      './'
    ];
    $command->name        = 'PHPLoc';
    $command->description = "Generate PHP Information (csv out)";
    $command->saveChanges();

    $bc = new BuildsCommands($build, $command);
    $bc->saveChanges();

    $command              = new BuildCommand(5);
    $command->name        = 'PHP MD';
    $command->command     = 'phpmd';
    $command->args        = [
      './',
      'xml',
      './phpmd.xml',
      '--reportfile ../build/pmd.report.xml'
    ];
    $command->description = "Generate PHP Mess Detection";
    $command->saveChanges();

    $bc = new BuildsCommands($build, $command);
    $bc->saveChanges();

    $command              = new BuildCommand(6);
    $command->name        = 'PHPCS';
    $command->command     = 'phpcs';
    $command->args        = [
      '--report=checkstyle',
      '--report-file=../build/checkstyle.xml',
      '--standard=phpcs.xml',
      './src'
    ];
    $command->description = "Check Code Standards";
    $command->saveChanges();

    $bc = new BuildsCommands($build, $command);
    $bc->saveChanges();

    $command              = new BuildCommand(7);
    $command->name        = 'PHPCPD';
    $command->command     = 'phpcpd';
    $command->args        = [
      '--log-pmd ../build/pmd-cpd.xml',
      './src'
    ];
    $command->description = "Check Code Duplication";
    $command->saveChanges();

    $bc = new BuildsCommands($build, $command);
    $bc->saveChanges();

    $command              = new BuildCommand(8);
    $command->name        = 'PDepend';
    $command->command     = 'pdepend';
    $command->args        = [
      '--jdepend-xml=../build/depend.xml',
      '--summary-xml=../build/depend-summary.xml',
      '--jdepend-chart=../build/depend.svg',
      '--overview-pyramid=../build/depend-pyramid.svg',
      './'
    ];
    $command->description = "Generate PHP Dependancy information";
    $command->saveChanges();

    $bc = new BuildsCommands($build, $command);
    $bc->saveChanges();

    $source                 = new BuildSource(1);
    $source->fetchUrl       = "https://github.com/qbex/Cubex.git";
    $source->repositoryType = RepositoryProvider::GIT;
    $source->saveChanges();
  }
}
