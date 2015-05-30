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
   *
   * @param $enable bool
   */
  public function setEnabled($enable) {
  }

  /**
   * Log fatal message
   *
   * @param $message string
   */
  public function fatal($message) {
  }

  /**
   * Log critical message
   *
   * @param $message string
   */
  public function critical($message) {
  }

  /**
   * Log error message
   *
   * @param $message string
   */
  public function error($message) {
  }

  /**
   * Log warning message
   *
   * @param $message string
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
   * @param $message string
   */
  public function info($message) {
  }

  /**
   * Log debug message
   *
   * @param $message string
   */
  public function debug($message) {
  }

  /**
   * Log message with given log level
   *
   * @param $logLevel
   * @param $message string
   */
  public function log($logLevel, $message) {
  }
}