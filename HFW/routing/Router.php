<?php

namespace hfw\routing;

use Closure;
use hfw\exceptions\FileNotFoundException;
use hfw\exceptions\InvalidRouteParameterException;
use hfw\exceptions\NotImplementedException;
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
   * @var RouteFactory
   */
  protected $_routeFactory = null;

  /**
   * @var string[] Supported match types with their respective regex
   */
  protected $_matchTypes = [
      'i' => '\d+',
      'w' => '[a-zA-z]+',
      'a' => '.+'
  ];

  /**
   * Register GET route
   *
   * @param string $path
   * @param string $invocationString
   * @return Route
   */
  public function get($path, $invocationString) {
    $route = $this->createRoute(Route::HTTP_METHOD_GET, $path, $invocationString);
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
    $route = $this->createRoute(Route::HTTP_METHOD_POST, $path, $invocationString);
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
    $route = $this->createRoute(Route::HTTP_METHOD_PUT, $path, $invocationString);
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
    $route = $this->createRoute(Route::HTTP_METHOD_DELETE, $path, $invocationString);
    $this->_routes[] = $route;
    return $route;
  }

  /**
   * Enable grouping of objects, useful when applying
   *
   * @param string   $namespace
   * @param callable $routes
   * @param string[] $middlewares
   * @return Router $this
   */
  public function with($namespace, Closure $routes, $middlewares = []) {
    $previousNamespace = $this->_routeFactory->getNamespace();
    $this->_routeFactory->appendNamespace($namespace);
    $this->_routeFactory->setMiddlewares($middlewares);
    $routes($this);
    $this->_routeFactory->resetMiddlewares();
    $this->_routeFactory->setNamespace($previousNamespace);
    return $this;
  }

  /**
   * Create route factory if not yet exists and have that create us a route
   *
   * @param int    $method
   * @param string $route
   * @param string $target Invocation string in format controller@method
   * @return Route
   */
  protected function createRoute($method, $route, $target) {
    if ($this->_routeFactory === null) {
      // TODO Implement IOC container to resolve this variable
      $this->_routeFactory = new RouteFactory();
    }
    return $this->_routeFactory->build($method, $route, $target);
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
      // get all parameter names from route uri match
      preg_match_all('`(?:/\[[a-z]:([^/]+)\])`', $regex, $paramNames);
      $paramNames = $paramNames[1];

      $regex = $this->compileRegex($route->getUri());
      preg_match($regex, $uri, $matches);

      if (count($matches) > 0) {
        // remove first entry, as it matches entire uri string
        array_shift($matches);
        $params = [];
        foreach ($paramNames as $index => $paramName) {
          $params[$paramName] = $matches[$index];
        }
        // save uri parameters to request attributes
        $request->attributes->add($params);
        // add invocation method to request attributes
        $request->attributes->add(['invocation' => $route->getTarget()]);
        // register route required middlewares with application
        $this->registerMiddlewaresWithApp($route->getMiddlewares());
        break;
      }
    }
    if (!$request->attributes->has('invocation')) {
      $this->routeNotFoundHandler($request);
    }
  }

  /**
   * Sets default action for what happens if route cannot be matched, adds invocation method to request and registers
   * required middlewares
   *
   * @param Request $request
   * @throws NotImplementedException
   */
  protected function routeNotFoundHandler(Request $request) {
    $request->attributes->set('invocation', 'ErrorController@notFound');
    $this->registerMiddlewaresWithApp(Route::$requiredMiddlewares);
  }

  /**
   * Compile route match string in its format to valid regex
   *
   * @param $regex
   * @return string
   * @throws InvalidRouteParameterException
   */
  protected function compileRegex($regex) {
    $matchTypes = $this->_matchTypes;
    $regex = preg_replace_callback('`\/\[(\w):(\w+)\]`', function ($match) use ($matchTypes) {
      list(, $type,) = $match;
      if (isset($matchTypes[$type])) {
        return '/(' . $matchTypes[$type] . ')';
      } else {
        throw new InvalidRouteParameterException(
            "Tried to match parameter of type '{$type}' which is not a valid parameter. See list of valid parameters.");
      }
    }, $regex);
    $regex = '`^' . $regex . '\/?$`';
    return $regex;
  }

  /**
   * Register route required middlewares with application
   *
   * @param string[] $middlewares
   * @throws NotImplementedException
   */
  protected function registerMiddlewaresWithApp(array $middlewares) {
    $middlewareNamespace = $this->_app->config('app.namespace');
    $middlewareNamespace = rtrim($middlewareNamespace, '\\') . '\\' . 'middlewares' . '\\';

    // before adding middlewares to stack we don't know which our 'next' is, so we need to figure that out.
    $next = null;

    while (!empty($middlewares)) {
      $middleware = array_pop($middlewares);

      // first try application middlewares
      $middlewareName = $middlewareNamespace . $middleware;
      if (class_exists($middlewareName)) {
        $middlewareObj = new $middlewareName($this->_app);
        if ($next === null) {
          $next = $middlewareObj;
        }
        $this->_app->registerMiddleware($middlewareObj);
        // middleware has been found, no need to look at framework middlewares
        continue;
      }

      // try framework middlewares if not in application
      $middlewareName = '\\hfw\\middlewares\\' . $middleware;
      if (class_exists($middlewareName)) {
        $middlewareObj = new $middlewareName($this->_app);
        if ($next === null) {
          $next = $middlewareObj;
        }
        $this->_app->registerMiddleware($middlewareObj);
      } else {
        throw new NotImplementedException("Trying to register middleware '{$middlewareName}', but class not found.");
      }
    }
    $this->_next = $next;
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
    return $this->_next->handle($request, $type, $catch);
  }
}