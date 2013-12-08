<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;

class CommitBuild extends CliCommand
{
  public function execute()
  {
    //Create build path
    //mkdir build/path

    //Rsync git repo into build directory, using hard links
    //rsync -lrtH repo/path build/path

    //Checkout the build path to the desired commit hash
    //git checkout commithash

    //Load all static analysis classes

    //Loop over all changed files and pass file path into each analysis class

    //Loop over all analysis classes which run on entire directory

    //Once analysis complete, start build processes e.g. composer

    //Copy vendor directory in from vendor/repos/branch/latest
    //Composer update
    //Push vendor dir back to latest vendor/repos/branch/latest

    //Run build commands
  }
}
