<?php

namespace hfw;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class Controller
 *
 * @package hfw
 */
class Controller {

  /**
   * Redirect to given url
   *
   * @param     $destination
   * @param int $status
   */
  public function redirect($destination, $status = 302) {
    $response = new Response();
    $response->setStatusCode($status);
    $response->headers->add(['Location' => $destination]);
    $response->send();
  }
}