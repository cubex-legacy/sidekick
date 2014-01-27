<?php
namespace Sidekick\Components\Fortify\Analysers\PhpSuperGlobals;

use Sidekick\Components\Fortify\Analysers\AbstractFileGrepAnalyser;

class PhpSuperGlobals extends AbstractFileGrepAnalyser
{
  protected $_matchOn = [
    '$GLOBALS',
    '$_SERVER',
    '$_GET',
    '$_POST',
    '$_FILES',
    '$_COOKIE',
    '$_SEESSION',
    '$_REQUEST',
    '$_ENV',
  ];
}
