<?php

if (!empty($router) && $router instanceof \hfw\routing\Router) {
  $router->get('/', 'IndexController@index');

  $router->with('/users', function () use ($router) {
    $router->get('/', 'UserController@index');
    $router->get('/[i:id]', 'UserController@viewAction');
    $router->get('/create', 'UserController@create');
    $router->post('/', 'UserController@createAction');
    $router->put('/[i:id]', 'UserController@modifyAction');
    $router->delete('/[i:id]', 'UserController@deleteAction');
  }, ['Authentication', 'Authorization']);

  $router->with('/journal', function () use ($router) {
    $router->post('/', 'JournalController@createAction');
    $router->get('/create', 'JournalController@create');
    $router->put('/[i:id]', 'JournalController@modifyAction');
    $router->delete('/[i:id]', 'JournalController@deleteAction');
  }, ['Authentication', 'Authorization'])
      ->with('/journal', function () use ($router) {
        $router->get('/', 'JournalController@index');
        $router->get('/[i:id]', 'JournalController@viewAction');
      });

} else {
  throw new LogicException('Route file must be given ');
}
