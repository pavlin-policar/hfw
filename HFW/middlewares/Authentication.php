<?php

namespace hfw\middlewares;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Authentication
 *
 * @package hfw\middlewares
 */
class Authentication extends BaseMiddleware {

  /**
   * Handles a Request to convert it to a Response.
   *
   * When $catch is true, the implementation must catch all exceptions
   * and do its best to convert them to a Response instance.
   *
   * @param Request $request A Request instance
   * @param int     $type    The type of the request
   *                         (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
   * @param bool    $catch   Whether to catch exceptions or not
   *
   * @return Response A Response instance
   *
   * @throws \Exception When an Exception occurs during processing
   *
   * @api
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {
    if (false) {
      throw new \Exception("Not authenticated");
    } else {
      $response = $this->_next->handle($request, $type, $catch);
      $response->setContent('Authenticated!<br>' . $response->getContent());
      return $response;
    }
  }
}