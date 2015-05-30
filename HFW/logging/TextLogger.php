<?php

namespace hfw\logging;


use hfw\contracts\LoggerInterface;

/**
 * Class TextLogger
 *
 * @package hfw\logging
 */
class TextLogger implements LoggerInterface {

  /**
   * @var resource
   */
  protected $_fileHandler = null;

  /**
   * @var string
   */
  protected $_directory;

  /**
   * @var string
   */
  protected $_fileName;

  /**
   * @var bool
   */
  protected $_enabled = true;

  /**
   * Minimum level to log
   *
   * @var
   */
  protected $_level;

  /**
   * @var array
   */
  protected $_levels = [
      self::FATAL    => 'FATAL',
      self::CRITICAL => 'CRITICAL',
      self::ERROR    => 'ERROR',
      self::WARNING  => 'WARNING',
      self::NOTICE   => 'NOTICE',
      self::INFO     => 'INFO',
      self::DEBUG    => 'DEBUG'
  ];

  /**
   * @param     $directory
   * @param     $fileName
   * @param     $enabled
   * @param int $minLevel Minimum log level at which to write logs, disable for lower levels in production
   */
  function __construct($directory, $fileName, $enabled = true, $minLevel = LoggerInterface::DEBUG) {
    $this->_directory = $directory;
    $this->_fileName = $fileName;
    $this->_enabled = $enabled;
    $this->_level = $minLevel;
  }

  /**
   * When application execution is finished, make sure to properly close file handler
   */
  public function __destruct() {
    if ($this->_fileHandler !== null) {
      fclose($this->_fileHandler);
    }
  }

  /**
   * Enable or disable logging
   *
   * @param $enable bool
   */
  public function setEnabled($enable) {
    $this->_enabled = $enable;
  }

  /**
   * Set minimum log level, which to log
   *
   * @param $minLevel
   */
  public function setMinLevel($minLevel) {
    if (array_key_exists($minLevel, $this->_levels)) {
      $this->_level = $minLevel;
    } else {
      throw new \LogicException('Tried to set minimum log level to invalid level');
    }
  }

  /**
   * Log fatal message
   *
   * @param $message string
   */
  public function fatal($message) {
    $this->log(static::FATAL, $message);
  }

  /**
   * Log critical message
   *
   * @param $message string
   */
  public function critical($message) {
    $this->log(static::CRITICAL, $message);
  }

  /**
   * Log error message
   *
   * @param $message string
   */
  public function error($message) {
    $this->log(static::ERROR, $message);
  }

  /**
   * Log warning message
   *
   * @param $message string
   */
  public function warning($message) {
    $this->log(static::WARNING, $message);
  }

  /**
   * Log notice
   *
   * @param $message string
   */
  public function notice($message) {
    $this->log(static::NOTICE, $message);
  }

  /**
   * Log info
   *
   * @param $message string
   */
  public function info($message) {
    $this->log(static::INFO, $message);
  }

  /**
   * Log debug message
   *
   * @param $message string
   */
  public function debug($message) {
    $this->log(static::DEBUG, $message);
  }

  /**
   * Log message with given log level
   *
   * @param $logLevel
   * @param $message string
   */
  public function log($logLevel, $message) {
    if ($this->_enabled && $this->_level <= $logLevel) {
      $this->openFile();
      fprintf($this->_fileHandler, "[%s]%10s    %s\n", date("d:m:Y H:i:s"), $this->_levels[$logLevel], $message);
    }
  }

  /**
   * Open file handler if not already open
   */
  protected function openFile() {
    if ($this->_fileHandler === null) {
      if (!file_exists($this->_directory)) {
        mkdir($this->_directory);
      }
      $this->_fileHandler = fopen($this->_directory . $this->_fileName, 'a+');
    }
  }
}