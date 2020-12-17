<?php

namespace edustef\mvcFrame;

use edustef\mvcFrame\exceptions\NotFoundException;

class Router
{
  /**
   * This stores an array of arrays with the format 
   */
  protected array $routes = [];
  public Request $request;
  public Response $response;

  public function __construct(Request $request, Response $response)
  {
    $this->response = $response;
    $this->request = $request;
  }

  /**
   * Adds the path and callback to the GET router
   * The callback is called when resolving the path 
   */
  public function get($path, $callback)
  {
    $this->routes['get'][$path] = $callback;
  }

  /**
   * Adds the path and callback to the POST router. 
   * The callback is called when resolving the path 
   */
  public function post($path, $callback)
  {
    $this->routes['post'][$path] = $callback;
  }

  /**
   * Will resolve the method and path of the REQUEST
   * and will create the controller and run it's method referenced by the callback.
   * @throws NotFoundException; 
   * @throws ForbiddenException;
   */
  public function resolve(): string
  {
    $path = $this->request->getPath();
    $method = $this->request->method();

    $callback = $this->routes[$method][$path] ?? false;

    if ($callback === false) {
      throw new NotFoundException();
    }

    if (is_string($callback)) {
      return Application::$app->view->renderView($callback);
    }

    //create instance of controller
    if (is_array($callback)) {
      $controller = new $callback[0]();
      Application::$app->controller = $controller;
      $controller->action = $callback[1];
      $callback[0] = $controller;

      foreach ($controller->middlewares as $middleware) {
        $middleware[0]->execute($middleware[1]);
      }
    }

    return call_user_func($callback, $this->request, $this->response);
  }
}
