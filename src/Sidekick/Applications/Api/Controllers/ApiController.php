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

      //build response and return key in plain text
      $response = new Response(json_encode($return));
      $response->addHeader('Content-Type', 'text/json');

      return $response;
    }
  }
}
