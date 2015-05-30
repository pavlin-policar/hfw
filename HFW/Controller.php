<?php

namespace hfw;

/**
 * Class Controller
 *
 * @package hfw
 */
abstract class Controller {

  /**
   * @var Application
   */
  protected $_app;

  /**
   * @param Application $app
   */
  function __construct(Application $app) {
    $this->_app = $app;
  }
}