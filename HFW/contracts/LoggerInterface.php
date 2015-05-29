<?php

namespace hfw\contracts;

/**
 * Interface LoggerInterface
 *
 * @package hfw\contracts
 */
interface LoggerInterface {
  const FATAL     = 7;
  const CRITICAL  = 6;
  const ERROR     = 5;
  const WARNING   = 4;
  const NOTICE    = 3;
  const INFO      = 2;
  const DEBUG     = 1;

  /**
   * Enable or disable logging
   *
   * @param $enable bool
   */
  public function setEnabled($enable);

  /**
   * Log fatal message
   *
   * @param $message string
   */
  public function fatal($message);

  /**
   * Log critical message
   *
   * @param $message string
   */
  public function critical($message);

  /**
   * Log error message
   *
   * @param $message string
   */
  public function error($message);

  /**
   * Log warning message
   *
   * @param $message string
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
   * @param $message string
   */
  public function info($message);

  /**
   * Log debug message
   *
   * @param $message string
   */
  public function debug($message);

  /**
   * Log message with given log level
   *
   * @param $logLevel
   * @param $message string
   */
  public function log($logLevel, $message);
}