<?php

namespace hfw\routing;


/**
 * Class Route
 *
 * @package hfw\routing
 */
class Route {
  const HTTP_METHOD_GET     = 1;
  const HTTP_METHOD_POST    = 2;
  const HTTP_METHOD_PUT     = 3;
  const HTTP_METHOD_PATCH   = 4;
  const HTTP_METHOD_DELETE  = 5;

  /**
   * @var array Http methods
   */
  public static $httpMethods = [
      self::HTTP_METHOD_GET    => 'GET',
      self::HTTP_METHOD_POST   => 'POST',
      self::HTTP_METHOD_PUT    => 'PUT',
      self::HTTP_METHOD_PATCH  => 'PATCH',
      self::HTTP_METHOD_DELETE => 'DELETE'
  ];

  /**
   * @var string For named paths
   */
  protected $_name;

  /**
   * @var string Pattern to match
   */
  protected $_uri;

  /**
   * @var int HTTP method to accept from defined constants
   */
  protected $_method;

  /**
   * @var string Target object which to invoke on method call
   */
  protected $_target;

  /**
   * @var string[] In ascending order
   */
  protected $_requiredMiddlewares = ['ControllerResolver'];

  /**
   * @var string[]
   */
  protected $_middlewares = [];

  /**
   * @param $method
   * @param $pattern
   * @param $target
   */
  function __construct($method, $pattern, $target) {
    $this->setMethod($method);
    $this->setUri($pattern);
    $this->setTarget($target);
    $this->_middlewares = $this->_requiredMiddlewares;
    return $this;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->_name;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->_name = $name;
  }

  /**
   * @return string
   */
  public function getUri() {
    return $this->_uri;
  }

  /**
   * @param string $pattern
   */
  public function setUri($pattern) {
    $pattern = strtolower($pattern);

    // don't trim home directory uri
    if (strlen($pattern) !== 1) {
      $pattern = strtolower(rtrim($pattern, '/'));
    }
    $this->_uri = $pattern;
  }

  /**
   * @return int
   */
  public function getMethod() {
    return $this->_method;
  }

  /**
   * @param int $method
   */
  public function setMethod($method) {
    if (!isset(static::$httpMethods[$method])) {
      throw new \LogicException("HTTP method given does not exist");
    }
    $this->_method = $method;
  }

  /**
   * @return string
   */
  public function getTarget() {
    return $this->_target;
  }

  /**
   * @param string $target
   */
  public function setTarget($target) {
    $this->_target = $target;
  }

  /**
   * @return string[]
   */
  public function getMiddlewares() {
    return $this->_middlewares;
  }

  /**
   * Add list of required middlewares that need to run for each route, in ascending order
   *
   * @param string[] $middlewares
   * @return Route $this
   */
  public function addRequiredMiddleware(array $middlewares) {
    while (!empty($middlewares)) {
      $middleware = array_pop($middlewares);
      array_push($this->_middlewares, $middleware);
    }
    return $this;
  }

  /**
   * Remove list of middlewares from current list
   *
   * @param string[] $middlewares
   * @return Route $this
   */
  public function removeRequiredMiddleware(array $middlewares) {
    $this->_middlewares = array_diff($this->_middlewares, $middlewares);
    return $this;
  }

  /**
   * Override current list of middlewares with given list
   *
   * @param string[] $middlewares
   * @return Route $this
   */
  public function setRequiredMiddleware(array $middlewares) {
    $this->_middlewares = $this->_requiredMiddlewares;
    while (!empty($middlewares)) {
      $middleware = array_pop($middlewares);
      array_push($this->_middlewares, $middleware);
    }
    return $this;
  }
}