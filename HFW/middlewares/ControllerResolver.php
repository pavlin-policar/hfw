<?php

namespace hfw\middlewares;

use hfw\Application;
use hfw\exceptions\NotImplementedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerResolver extends BaseMiddleware {

  /**
   * @var string
   */
  protected $_controllerNamespace;

  /**
   * @param Application $app
   */
  function __construct(Application $app) {
    parent::__construct($app);
    $controllerNamespace = $app->config('app.namespace');
    $controllerNamespace = rtrim($controllerNamespace, '\\') . '\\' . 'controllers' . '\\';
    $this->setControllerNamespace($controllerNamespace);
  }

  /**
   * @return string
   */
  public function getControllerNamespace() {
    return $this->_controllerNamespace;
  }

  /**
   * @param string $controllerNamespace
   */
  public function setControllerNamespace($controllerNamespace) {
    $this->_controllerNamespace = $controllerNamespace;
  }

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
    $parts = $request->attributes->get('invocation');
    $parts = explode('@', $parts);
    $controller = $this->_controllerNamespace . $parts[0];
    $method = $parts[1];
    $controllerObj = new $controller($this->_app);
    if (method_exists($controllerObj, $method)) {
      return $controllerObj->{$method}($request);
    } else {
      throw new NotImplementedException("The method $method in $controller has not been implemented.");
    }
  }
}