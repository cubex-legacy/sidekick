<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;
use Sidekick\Components\Fortify\Enums\BuildLevel;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\Command;
use Sidekick\Components\Fortify\Mappers\BuildsCommands;
use Sidekick\Components\Fortify\Mappers\BuildsProjects;
use Sidekick\Components\Fortify\Mappers\Patch;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Enums\RepositoryProvider;
use Sidekick\Components\Repository\Mappers\Source;

class SampleData extends CliCommand
{
  public function execute()
  {
    $build                  = new Build(1);
    $build->buildLevel      = BuildLevel::MINOR;
    $build->name            = "Minor Build";
    $build->sourceDirectory = 'sourcecode/';
    $build->saveChanges();

    $command                   = new Command(1);
    $command->command          = 'php';
    $command->args             = ['-l'];
    $command->name             = 'PHP Lint';
    $command->description      = "PHP Lint Check Directory";
    $command->runOnFileSet     = true;
    $command->filePattern      = '.*\.php$';
    $command->fileSetDirectory = '{sourcedirectory}src';
    $command->saveChanges();

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [3, 9];
    $bc->saveChanges();

    $command              = new Command(2);
    $command->name        = 'PHP Unit';
    $command->command     = 'phpunit';
    $command->args        = [
      '--coverage-clover logs/clover.sml',
      '--log-junit logs/junit.xml',
      ' -c {sourcedirectory}phpunit.xml.dist',
    ];
    $command->description = "Run PHPUnit Tests";
    $command->saveChanges();

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command              = new Command(3);
    $command->name        = 'Make Build Directory';
    $command->command     = 'mkdir';
    $command->args        = [
      'logs',
      '-p',
    ];
    $command->description = "Make Build Directory";
    $command->saveChanges();

    $bc = new BuildsCommands([$build, $command]);
    $bc->saveChanges();

    $command              = new Command(4);
    $command->command     = 'phploc';
    $command->args        = [
      '--log-csv logs/phploc.csv',
      '{sourcedirectory}src',
    ];
    $command->name        = 'PHPLoc';
    $command->description = "Generate PHP Information (csv out)";
    $command->saveChanges();

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command                    = new Command(5);
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

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [7];
    $bc->saveChanges();

    $command              = new Command(6);
    $command->name        = 'PHPCS';
    $command->command     = 'phpcs';
    $command->args        = [
      '--extensions=php',
      '--report=checkstyle',
      '--warning-severity=0',
      '--report-file=logs/checkstyle.xml',
      '--standard={sourcedirectory}phpcs.xml',
      '{sourcedirectory}src',
    ];
    $command->description = "Check Code Standards";
    $command->saveChanges();

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command              = new Command(7);
    $command->name        = 'PHPCPD';
    $command->command     = 'phpcpd';
    $command->args        = [
      '--log-pmd logs/pmd-cpd.xml',
      '{sourcedirectory}src',
    ];
    $command->description = "Check Code Duplication";
    $command->saveChanges();

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command              = new Command(8);
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

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [1];
    $bc->saveChanges();

    $command              = new Command(9);
    $command->name        = 'Composer Install';
    $command->command     = 'composer';
    $command->args        = [
      'install',
      '-o',
      '--working-dir {sourcedirectory}',
    ];
    $command->description = "Install Project Dependencies";
    $command->saveChanges();

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [3];
    $bc->saveChanges();

    $command              = new Command(10);
    $command->name        = 'Phuse';
    $command->command     = 'php {CUBEX_BIN} Phuse.CreateBuild';
    $command->args        = [
      '--path={sourcedirectory}',
      '--version=dev-{branch}'
    ];
    $command->description = "Phuse Build";
    $command->saveChanges();

    $bc               = new BuildsCommands([$build, $command]);
    $bc->dependencies = [8];
    $bc->saveChanges();

    /***
     * [build_projects]
     * cubex[name] = Cubex
     * cubex[description] = Cubex Framework
     * cubex[repository] = https://github.com/qbex/Cubex.git
     * cubex[repo_type] = git
     */

    $i      = 0;
    $builds = $this->config('build_projects');
    foreach($builds as $buildInfo)
    {
      echo "Adding " . $buildInfo['name'] . "\n";
      $i++;
      $project              = new Project($i);
      $project->name        = $buildInfo['name'];
      $project->description = $buildInfo['description'];
      $project->saveChanges();

      $source                 = new Source($i);
      $source->name           = $project->name;
      $source->description    = $project->description;
      $source->fetchUrl       = $buildInfo['repository'];
      $source->repositoryType = RepositoryProvider::fromValue(
        $buildInfo['repo_type']
      );
      $source->localpath      = $buildInfo['localpath'];
      $source->saveChanges();

      $proBuild                = new BuildsProjects([$build, $project]);
      $proBuild->buildSourceId = $source->id();
      $proBuild->saveChanges();
    }

    $patch                 = new Patch(1);
    $patch->author         = 1;
    $patch->patch          = file_get_contents(
      'C:\Websites\qbex\Cubex\Break_Loader.patch'
    );
    $patch->name           = "Break_Loader";
    $patch->filename       = "Break_Loader.patch";
    $patch->leadingSlashes = 0;
    $patch->saveChanges();

    echo "Sample Data Generated\n";
  }
}
