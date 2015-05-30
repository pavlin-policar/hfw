<?php

namespace hfw;
use hfw\helpers\NestedArray;


/**
 * Class Config
 *
 * @package hfw
 */
class Config extends NestedArray {
  /**
   * Set config options from array
   *
   * @param array $options
   */
  protected function setOptions(array $options) {
    $this->_array = $options;
  }

  /**
   * Parse config file and return config object
   *
   * @param string $fileName
   * @return Config
   */
  public static function parseConfigFile($fileName = 'config/environment.cfg') {
    $cfg = new Config();
    $cfg->setOptions(parse_ini_file($fileName, true));
    return $cfg;
  }
}