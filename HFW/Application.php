<?php

namespace hfw;

use hfw\contracts\LoggerInterface;
use hfw\logging\TextLogger;
use hfw\middlewares\Authentication;
use hfw\middlewares\Authorization;
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
   * Register a middleware with the application. Will execute as LIFO
   *
   * @param BaseMiddleware $middleware
   */
  public function registerMiddleware(BaseMiddleware $middleware) {
    if (in_array($middleware, $this->_middleware)) {
      $className = get_class($middleware);
      throw new \RuntimeException("Cyclic middleware stack detected: tried to queue {$className}");
    } else {
      count($this->_middleware) > 0 and $middleware->setNext($this->_middleware[0]);
      array_unshift($this->_middleware, $middleware);
    }
  }

  /**
   * Run application with current settings
   */
  public function run() {
    $request = Request::createFromGlobals();

    $this->registerMiddleware(new Authorization($this));
    $this->registerMiddleware(new Authentication($this));
    $this->registerMiddleware(new Router($this));

    if ($this->config('debug')) {
      $this->registerMiddleware(new PrettyExceptions($this));
    }

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