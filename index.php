<?php

error_reporting(E_ALL);

session_start();
date_default_timezone_set('Europe/Ljubljana');
require_once('vendor/autoload.php');

$base = dirname($_SERVER['PHP_SELF']);
if (ltrim($base, '/')) {
  $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
}

$app = new \hfw\Application();
$app->setConfig('app.namespace', '\\journal');
$app->run();
