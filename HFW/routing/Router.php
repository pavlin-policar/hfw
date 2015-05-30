<?php

namespace hfw\routing;

use hfw\exceptions\FileNotFoundException;
use hfw\middlewares\BaseMiddleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Router
 *
 * RESTFUL router class, support for all HTTP methods, use hidden _method field to specify methods for html forms
 *
 * @package hfw\routing
 */
class Router extends BaseMiddleware {

  /**
   * @var Route[]
   */
  protected $_routes = [];

  /**
   * Register GET route
   *
   * @param string $path
   * @param string $invocationString
   * @return Route
   */
  public function get($path, $invocationString) {
    $route = new Route(Route::HTTP_METHOD_GET, $path, $invocationString);
    $this->_routes[] = $route;
    return $route;
  }

  /**
   * Register POST route
   *
   * @param $path
   * @param $invocationString
   * @return Route
   */
  public function post($path, $invocationString) {
    $route = new Route(Route::HTTP_METHOD_POST, $path, $invocationString);
    $this->_routes[] = $route;
    return $route;
  }

  /**
   * Register PUT route
   *
   * @param $path
   * @param $invocationString
   * @return Route
   */
  public function put($path, $invocationString) {
    $route = new Route(Route::HTTP_METHOD_PUT, $path, $invocationString);
    $this->_routes[] = $route;
    return $route;
  }

  /**
   * Register DELETE route
   *
   * @param $path
   * @param $invocationString
   * @return Route
   */
  public function delete($path, $invocationString) {
    $route = new Route(Route::HTTP_METHOD_DELETE, $path, $invocationString);
    $this->_routes[] = $route;
    return $route;
  }

  /**
   * Match request to a registered route, and add any url parameters given to request attributes. Also specify, in
   * request attributes, the invocation method that the specific route requires.
   *
   * @param Request $request
   */
  public function matchRequest(Request &$request) {
    // enable request method override
    $request->enableHttpMethodParameterOverride();

    $uri = $request->getRequestUri();
    if (($pos = strpos($uri, '?')) !== false) {
      $uri = substr($uri, 0, $pos);
    }

    foreach ($this->_routes as $route) {
      if ($request->getMethod() != Route::$httpMethods[$route->getMethod()]) {
        continue;
      }

      // don't trim home directory uri
      if (strlen($uri) !== 1) {
        $uri = strtolower(rtrim($uri, '/'));
      }

      $regex = $route->getUri();
      preg_match('/:[^\/]+/', $regex, $paramNames);
      // replace any slashes in uri with regex slashes
      $regex = preg_replace('/\//', '\\\/', $regex);
      // replace any parameter in ':param' format with regex expression
      $regex = preg_replace('/:[^\/]+/', '([^\/]*)', $regex);
      $regex = '/^' . $regex . '$/';

      preg_match($regex, $uri, $matches);

      if (count($matches) > 0) {
        // remove first entry, as it matches entire uri string
        array_shift($matches);
        $params = [];
        foreach ($paramNames as $index => $paramName) {
          $params[ltrim($paramName, ':')] = $matches[$index];
        }
        $request->attributes->add($params);
        $request->attributes->add(['invocation' => $route->getTarget()]);
        break;
      }
      if (!$request->attributes->has('invocation')) {
        $request->attributes->set('invocation', 'errorController@notFound');
      }
    }
  }

  /**
   * Read routes from configuration specified route file
   *
   * @throws FileNotFoundException
   * @internal param Router $router
   */
  protected function getRoutes() {
    $routeFile = $this->_app->config('routing.routeFile');
    if (file_exists($routeFile)) {
      // configuration file requires Router variable to be defined
      /** @noinspection PhpUnusedLocalVariableInspection */
      $router = $this;
      /** @noinspection PhpIncludeInspection */
      include($this->_app->config('routing.routeFile'));
    } else {
      throw new FileNotFoundException('Invalid or no route file was specified in configuration.');
    }
  }

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
    $this->getRoutes();
    $this->matchRequest($request);
    echo '<pre>';
    print_r($request);
    return $this->_next->handle($request, $type, $catch);
  }
}