<?php

namespace journal\controllers;


use hfw\Controller;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller {

  public function notFound() {
    return new Response("404 Page, not found");
  }
}