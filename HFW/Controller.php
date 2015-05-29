<?php

namespace hfw;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class Controller
 *
 * @package hfw
 */
abstract class Controller {

  /**
   * Redirect to given url
   *
   * @param       $destination
   * @param int   $status
   * @param array $headers
   */
  public function redirect($destination, $status = 302, $headers = []) {
    $response = new Response();
    $response->setStatusCode($status);
    $headers['Location'] = $destination;
    $response->headers->add($headers);
    $response->send();
  }
}