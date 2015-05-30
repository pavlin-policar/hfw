<?php

namespace hfw\routing;


use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class RouteFactory
 *
 * @package hfw\routing
 */
class RouteFactory {

  /**
   * @var string
   */
  protected $_namespace = '';

  /**
   * @var HttpKernelInterface[]
   */
  protected $_middlewares = [];

  /**
   * @param string $namespace
   */
  public function setNamespace($namespace) {
    $this->_namespace = $namespace;
  }

  /**
   * Append namespace to current namespace, useful when grouping routes together
   *
   * @param string $namespace
   */
  public function appendNamespace($namespace) {
    $namespace = '/' . ltrim($namespace, '/');
    $this->_namespace .= $namespace;
  }

  /**
   * @return string
   */
  public function getNamespace() {
    return $this->_namespace;
  }

  /**
   * @return HttpKernelInterface[]
   */
  public function getMiddlewares() {
    return $this->_middlewares;
  }

  /**
   * @param HttpKernelInterface[] $middlewares
   */
  public function setMiddlewares($middlewares) {
    $this->_middlewares = $middlewares;
  }

  /**
   * Remove all middlewares for routes
   */
  public function resetMiddlewares() {
    $this->_middlewares = [];
  }

  /**
   * @param $method
   * @param $uri
   * @param $target
   * @return Route
   */
  public function build($method, $uri, $target) {
    $route = new Route($method, $this->_namespace . $uri, $target);
    $route->addRequiredMiddleware($this->_middlewares);
    return $route;
  }
}