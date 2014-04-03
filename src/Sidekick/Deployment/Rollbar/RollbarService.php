<?php
namespace Sidekick\Deployment\Rollbar;

use Cubex\Data\Handler\DataHandler;
use Cubex\Log\Log;
use Sidekick\Deployment\BaseDeploymentService;

class RollbarService extends BaseDeploymentService
{
  /**
   * @return void
   */
  public function deploy()
  {
    $cfg = (new DataHandler())->hydrate($this->_stage->configuration);
    \Requests::post(
      'https://api.rollbar.com/api/1/deploy/',
      [],
      [
        'access_token' => $cfg->getStr("command", null),
        'environment'  => $cfg->getStr("environment", 'production'),
        'revision'     => $this->_version->format(),
        'comment'      => $this->_version->changeLog
      ]
    );

    Log::info(
      'https://api.rollbar.com/api/1/deploy/' . $this->_version->format(
      ) . '/' . $this->_stage->platform()->name
    );
  }

  public static function getConfigurationItems()
  {
    return [
      'access_token' => 'Your project access token. Required.',
      'environment' => 'Environment you will be deploying to',
    ];
  }
}

