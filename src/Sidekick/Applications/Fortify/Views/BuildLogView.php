<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 10/06/13
 * Time: 10:44
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\Command;

class BuildLogView extends TemplatedViewModel
{
  protected $_commandsRun = [];
  private $_outputLine = false;
  private $_buildRunId;

  public function __construct($buildRunId)
  {
    $this->_buildRunId = $buildRunId;
  }

  public function addCommand(
    Command $command, $commandRun, $passed = false, $commandOutput = null
  )
  {
    if($command->command !== null)
    {
      $obj                = new \stdClass();
      $obj->command       = $command;
      $obj->passed        = $passed;
      $obj->exitCode      = idx($commandRun, 'exit_code');
      $obj->startTime     = idx($commandRun, 'start_time');
      $obj->commandOutput = $commandOutput;
      $obj->argsLine      = '';
      if(is_array($command->args))
      {
        $obj->args = implode(' ', $command->args);
      }

      $this->_commandsRun[] = $obj;
    }

    return $this;
  }

  public function getCommandsRun()
  {
    return $this->_commandsRun;
  }

  public function getOutputLine($key, $lineValue)
  {
    $keyParts = explode(':', $key);
    if($keyParts[0] == 'output')
    {
      $this->_outputLine           = new \stdClass();
      $this->_outputLine->lineTime = date('H:i:s', $keyParts[1]);
      $this->_outputLine->lines    = [];

      if($lineValue)
      {
        $lines = explode("\n", $lineValue);
        foreach($lines as $line)
        {
          //we don't want empty lines
          if(empty($line))
          {
            continue;
          }
          $this->_outputLine->lines[] = $line;
        }
      }
    }

    return $this->_outputLine;
  }

  public function getTextClass($passed, $exitCode)
  {
    $txtClass = '';
    if(!$passed)
    {
      $txtClass = 'error-text';
      if($exitCode == -1)
      {
        $txtClass = 'running-text';
      }
    }

    return $txtClass;
  }

  public function linkify($line)
  {
    //support only lines containing paths to .mo & .po
    if(strpos($line, '.mo:') !== false && strpos($line, '.po:') !== false)
    {
      $lastColon = strrpos($line, ':');
      $target    = substr($line, 0, $lastColon);
      list($path, $startLine) = explode(':', $target, 2);
      $link = "/sourcecode/build/" . $this->_buildRunId . '/';
      $link .= substr($path, strpos($path, 'src'));
      $link .= ';' . $startLine;

      $linkedPath = '<a href="' . $link . '">' . $target . '</a>';

      $line = str_replace($path, $linkedPath, $line);
    }
    return $line;
  }
}
