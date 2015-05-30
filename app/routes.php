<?php

if (!empty($router) && $router instanceof \hfw\routing\Router) {
  $router->get('/', 'indexController@index');

  $router->get('/users', 'userController@index');
  $router->post('/users', 'userController@createAction');
  $router->get('/users/create', 'userController@create');
  $router->get('/users/:id', 'userController@viewAction');
  $router->put('/users/:id', 'userController@modifyAction');
  $router->delete('/users/:id', 'userController@deleteAction');
} else {
  throw new LogicException('Route file must be given ');
}
