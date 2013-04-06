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

    $build                  = new Build(1);
    $build->buildLevel      = BuildLevel::MINOR;
    $build->name            = "Minor Build";
    $build->sourceDirectory = 'sourcecode/';
    $build->saveChanges();

    $proBuild                = new BuildsProjects($build, $project);
    $proBuild->buildSourceId = 1;
    $proBuild->saveChanges();

    $command                   = new BuildCommand(1);
    $command->command          = 'php';
    $command->args             = ['-l'];
    $command->name             = 'PHP Lint';
    $command->description      = "PHP Lint Check Directory";
    $command->runOnFileSet     = true;
    $command->filePattern      = '.*\.php$';
    $command->fileSetDirectory = '{sourcedirectory}src';
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [3, 9];
    $bc->saveChanges();

    $command              = new BuildCommand(2);
    $command->name        = 'PHP Unit';
    $command->command     = 'phpunit';
    $command->args        = [
      '--coverage-clover logs/clover.sml',
      '--log-junit logs/junit.xml',
      ' -c {sourcedirectory}phpunit.xml.dist',
      '{sourcedirectory}',
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
      'logs',
      '-p',
    ];
    $command->description = "Make Build Directory";
    $command->saveChanges();

    $bc = new BuildsCommands($build, $command);
    $bc->saveChanges();

    $command              = new BuildCommand(4);
    $command->command     = 'phploc';
    $command->args        = [
      '--log-csv logs/phploc.csv',
      '{sourcedirectory}src',
    ];
    $command->name        = 'PHPLoc';
    $command->description = "Generate PHP Information (csv out)";
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command                    = new BuildCommand(5);
    $command->name              = 'PHP MD';
    $command->causeBuildFailure = false;
    $command->command           = 'phpmd';
    $command->args              = [
      '{sourcedirectory}src',
      'xml',
      '{sourcedirectory}phpmd.xml',
      '--reportfile logs/pmd.report.xml',
    ];
    $command->description       = "Generate PHP Mess Detection";
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [7];
    $bc->saveChanges();

    $command              = new BuildCommand(6);
    $command->name        = 'PHPCS';
    $command->command     = 'phpcs';
    $command->args        = [
      '--report=checkstyle',
      '--warning-severity=0',
      '--report-file=logs/checkstyle.xml',
      '--standard={sourcedirectory}phpcs.xml',
      '{sourcedirectory}src',
    ];
    $command->description = "Check Code Standards";
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command              = new BuildCommand(7);
    $command->name        = 'PHPCPD';
    $command->command     = 'phpcpd';
    $command->args        = [
      '--log-pmd logs/pmd-cpd.xml',
      '{sourcedirectory}src',
    ];
    $command->description = "Check Code Duplication";
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command              = new BuildCommand(8);
    $command->name        = 'PDepend';
    $command->command     = 'pdepend';
    $command->args        = [
      '--jdepend-xml=logs/depend.xml',
      '--summary-xml=logs/depend-summary.xml',
      '--jdepend-chart=logs/depend.svg',
      '--overview-pyramid=logs/depend-pyramid.svg',
      '{sourcedirectory}src',
    ];
    $command->description = "Generate PHP Dependancy information";
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command              = new BuildCommand(9);
    $command->name        = 'Composer Install';
    $command->command     = 'composer';
    $command->args        = [
      'install',
      '-o',
      '--working-dir {sourcedirectory}',
    ];
    $command->description = "Install Project Dependencies";
    $command->saveChanges();

    $bc               = new BuildsCommands($build, $command);
    $bc->dependencies = [3];
    $bc->saveChanges();

    $source                 = new BuildSource(1);
    $source->fetchUrl       = "https://github.com/qbex/Cubex.git";
    $source->repositoryType = RepositoryProvider::GIT;
    $source->saveChanges();
  }
}
