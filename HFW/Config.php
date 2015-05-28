<?php

namespace hfw;


/**
 * Class Config
 *
 * @package hfw
 */
class Config implements \ArrayAccess {

  protected $_options = [];

  /**
   * Set config options from array
   *
   * @param array $options
   */
  protected function setOptions(array $options) {
    $this->_options = $options;
  }

  /**
   * Parse config file and return config object
   *
   * @param string $fileName
   * @return Config
   */
  public static function parseConfigFile($fileName = 'environment.cfg') {
    $cfg = new Config();
    $cfg->setOptions(parse_ini_file($fileName, true));
    return $cfg;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Whether a offset exists
   *
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset <p>
   *                      An offset to check for.
   *                      </p>
   * @return boolean true on success or false on failure.
   *                      </p>
   *                      <p>
   *                      The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset) {
    return isset($this->_options[$offset]);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to retrieve
   *
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset <p>
   *                      The offset to retrieve.
   *                      </p>
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset) {
    return isset($this->_options[$offset]) ? $this->_options[$offset] : null;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to set
   *
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset <p>
   *                      The offset to assign the value to.
   *                      </p>
   * @param mixed $value  <p>
   *                      The value to set.
   *                      </p>
   * @return void
   */
  public function offsetSet($offset, $value) {
    $this->_options[$offset] = $value;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to unset
   *
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset <p>
   *                      The offset to unset.
   *                      </p>
   * @return void
   */
  public function offsetUnset($offset) {
    unset($this->_options[$offset]);
  }
}