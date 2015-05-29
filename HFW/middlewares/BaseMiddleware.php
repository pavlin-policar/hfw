<?php

namespace hfw\middlewares;

use hfw\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class BaseMiddleware
 *
 * @package hfw\middlewares
 */
abstract class BaseMiddleware implements HttpKernelInterface {
  /**
   * @var HttpKernelInterface
   */
  protected $_next;

  /**
   * @var Application
   */
  protected $_app;

  /**
   * @param $app
   */
  function __construct($app) {
    $this->_app = $app;
  }


  /**
   * @param HttpKernelInterface $next
   */
  public function setNext(HttpKernelInterface $next) {
    $this->_next = $next;
  }
}