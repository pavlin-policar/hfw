<?php

namespace hfw\middlewares;

use Symfony\Component\HttpFoundation\Request;
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
   * @param HttpKernelInterface $next
   */
  public function setNext(HttpKernelInterface $next) {
    $this->_next = $next;
  }
}