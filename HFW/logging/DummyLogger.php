<?php

namespace hfw\logging;

use hfw\contracts\LoggerInterface;

/**
 * Class DummyLogger
 *
 * @package hfw\logging
 */
class DummyLogger implements LoggerInterface {

  /**
   * Enable or disable logging
   */
  public function setEnabled() {
  }

  /**
   * Log fatal message
   *
   * @param $message
   */
  public function fatal($message) {
  }

  /**
   * Log critical message
   *
   * @param $message
   */
  public function critical($message) {
  }

  /**
   * Log error message
   *
   * @param $message
   */
  public function error($message) {
  }

  /**
   * Log warning message
   *
   * @param $message
   */
  public function warning($message) {
  }

  /**
   * Log notice
   *
   * @param $message
   */
  public function notice($message) {
  }

  /**
   * Log info
   *
   * @param $message
   */
  public function info($message) {
  }

  /**
   * Log debug message
   *
   * @param $message
   */
  public function debug($message) {
  }

  /**
   * Log message with given log level
   *
   * @param $logLevel
   * @param $message
   */
  public function log($logLevel, $message) {
  }
}