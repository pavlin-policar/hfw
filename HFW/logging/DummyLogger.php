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
    // TODO: Implement setEnabled() method.
  }

  /**
   * Log fatal message
   *
   * @param $message string
   */
  public function fatal($message) {
    // TODO: Implement fatal() method.
  }

  /**
   * Log critical message
   *
   * @param $message string
   */
  public function critical($message) {
    // TODO: Implement critical() method.
  }

  /**
   * Log error message
   *
   * @param $message string
   */
  public function error($message) {
    // TODO: Implement error() method.
  }

  /**
   * Log warning message
   *
   * @param $message string
   */
  public function warning($message) {
    // TODO: Implement warning() method.
  }

  /**
   * Log notice
   *
   * @param $message
   */
  public function notice($message) {
    // TODO: Implement notice() method.
  }

  /**
   * Log info
   *
   * @param $message string
   */
  public function info($message) {
    // TODO: Implement info() method.
  }

  /**
   * Log debug message
   *
   * @param $message string
   */
  public function debug($message) {
    // TODO: Implement debug() method.
  }

  /**
   * Log message with given log level
   *
   * @param $logLevel
   * @param $message string
   */
  public function log($logLevel, $message) {
    // TODO: Implement log() method.
  }
}