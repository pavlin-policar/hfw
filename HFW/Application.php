<?php

namespace hfw;

use hfw\contracts\LoggerInterface;
use hfw\logging\TextLogger;
use hfw\middlewares\BaseMiddleware;
use hfw\middlewares\PrettyExceptions;
use hfw\routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class Application
 *
 * @package hfw
 */
class Application {

  const VERSION = '0.0.1';

  /**
   * @var HttpKernelInterface[]
   */
  protected $_middleware = [];

  /**
   * @var Config
   */
  protected $_config;

  /**
   * @var LoggerInterface
   */
  protected $_logger;

  public function __construct() {
    $this->_config = Config::parseConfigFile();
    $this->_logger = $this->getLoggerInstance();
  }

  /**
   * Get config option by key if it is defined
   *
   * @param $key
   * @return mixed|null
   */
  public function config($key) {
    return isset($this->_config[$key]) ? $this->_config[$key] : null;
  }

  /**
   * Set config option
   *
   * @param $key
   * @param $value
   */
  public function setConfig($key, $value) {
    $this->_config[$key] = $value;
  }

  /**
   * Register a middleware with the application. Will execute as LIFO
   *
   * @param BaseMiddleware $middleware
   */
  public function registerMiddleware(BaseMiddleware $middleware) {
    if (in_array($middleware, $this->_middleware)) {
      $className = get_class($middleware);
      throw new \RuntimeException("Cyclic middleware stack detected: tried to queue {$className}");
    }
    foreach ($this->_middleware as $middlewareIterator) {
      if (get_class($middleware) == get_class($middlewareIterator)) {
        $className = get_class($middlewareIterator);
        throw new \RuntimeException("Duplicate middleware detected: tried to queue {$className}");
      }
    }
    count($this->_middleware) > 0 and $this->_middleware[count($this->_middleware) - 1]->setNext($middleware);
    array_push($this->_middleware, $middleware);
  }

  /**
   * Run application with current settings
   */
  public function run() {
    $request = Request::createFromGlobals();

    if ($this->config('debug')) {
      $this->registerMiddleware(new PrettyExceptions($this));
    }
    $this->registerMiddleware(new Router($this));

    // invoke middleware stack
    $response = $this->_middleware[0]->handle($request);
    $response->send();
  }

  /**
   * @return LoggerInterface
   */
  public function getLogger() {
    return $this->_logger;
  }

  /**
   * @return LoggerInterface
   */
  protected function getLoggerInstance() {
    switch ($this->config('logging.type')) {
      case 'text':
        $logger = new TextLogger($this->config('logging.directory'), $this->config('logging.file'),
            $this->config('logging.enabled'));
        if ($this->config('debug')) {
          $logger->setMinLevel(LoggerInterface::DEBUG);
        } else {
          $logger->setMinLevel(LoggerInterface::ERROR);
        }
        return $logger;
      default:
        throw new \LogicException('Logger type not implemented');
    }
  }
}