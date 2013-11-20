<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Api\Controllers;

use Cubex\Core\Controllers\BaseController;
use Cubex\Core\Http\Request;
use Cubex\Core\Http\Response;

class ApiController extends BaseController
{
  public function dispatch(Request $request, Response $response)
  {
    try
    {
      return parent::dispatch($request, $response);
    }
    catch(\Exception $e)
    {
      $return = [
        'error' => [
          'code'    => $e->getCode(),
          'message' => $e->getMessage()
        ]
      ];

      return $response->fromJson($return);
    }
  }
}
