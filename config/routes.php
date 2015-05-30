<?php

if (!empty($router) && $router instanceof \hfw\routing\Router) {
  $router->get('/', 'IndexController@index');

  $router->with('/users', function () use ($router) {
    $router->get('/', 'UserController@index');
    $router->post('/', 'UserController@createAction');
    $router->get('/create', 'UserController@create');
    $router->get('/:id', 'UserController@viewAction');
    $router->put('/:id', 'UserController@modifyAction');
    $router->delete('/:id', 'UserController@deleteAction');
  }, ['Authentication', 'Authorization', 'Authentication']);

  $router->with('/journal', function () use ($router) {
    $router->post('/', 'JournalController@createAction');
    $router->get('/create', 'JournalController@create');
    $router->put('/:id', 'JournalController@modifyAction');
    $router->delete('/:id', 'JournalController@deleteAction');
  }, ['Authentication', 'Authorization'])
      ->with('/journal', function () use ($router) {
        $router->get('/', 'JournalController@index');
        $router->get('/:id', 'JournalController@viewAction');
      });

} else {
  throw new LogicException('Route file must be given ');
}
