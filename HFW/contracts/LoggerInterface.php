<?php

namespace hfw\contracts;

/**
 * Interface LoggerInterface
 *
 * @package hfw\contracts
 */
interface LoggerInterface {
  const FATAL     = 1;
  const CRITICAL  = 2;
  const ERROR     = 3;
  const WARNING   = 4;
  const NOTICE    = 5;
  const INFO      = 6;
  const DEBUG     = 7;

  /**
   * Enable or disable logging
   */
  public function setEnabled();

  /**
   * Log fatal message
   *
   * @param $message
   */
  public function fatal($message);

  /**
   * Log critical message
   *
   * @param $message
   */
  public function critical($message);

  /**
   * Log error message
   *
   * @param $message
   */
  public function error($message);

  /**
   * Log warning message
   *
   * @param $message
   */
  public function warning($message);

  /**
   * Log notice
   *
   * @param $message
   */
  public function notice($message);

  /**
   * Log info
   *
   * @param $message
   */
  public function info($message);

  /**
   * Log debug message
   *
   * @param $message
   */
  public function debug($message);

  /**
   * Log message with given log level
   *
   * @param $logLevel
   * @param $message
   */
  public function log($logLevel, $message);
}