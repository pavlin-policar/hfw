<?php

namespace hfw\helpers;

/**
 * Class NestedArray
 *
 * Supports nested array accessed by a single delimited string $array['foo.bar.baz']
 *
 * @package hfw\helpers
 */
class NestedArray implements \ArrayAccess {
  /**
   * @var array
   */
  protected $_array = [];

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
    $result = $this->findInArray($this->getKeysFromOffset($offset), $this->_array);
    return $result !== false;
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
    return $this->offsetExists($offset) ? $this->findInArray($this->getKeysFromOffset($offset), $this->_array) : null;
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
    $this->setInArray($this->getKeysFromOffset($offset), $this->_array, $value);
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
    $this->unsetInArray($this->getKeysFromOffset($offset), $this->_array);
  }

  /**
   * Extract key array from given offset, delimited by the '.' character
   * e.g. "foo.bar.baz" => ["foo", "bar", "baz"]
   *
   * @param $offset
   * @return array
   */
  protected function getKeysFromOffset($offset) {
    preg_match_all('/(\w+)(?:\.)?/i', $offset, $matches, PREG_SET_ORDER);
    $keys = [];
    foreach ($matches as $match) {
      $keys[] = $match[1];
    }
    return $keys;
  }

  /**
   * Unset given key in array from given array of keys
   * e.g. ["foo", "bar", "baz"] <=> $array["foo"]["bar"]["baz"]
   *
   * @param $keys
   * @param $array
   */
  protected function unsetInArray($keys, &$array) {
    if (count($keys) === 1) {
      if (isset($array[$keys[0]])) {
        unset($array[$keys[0]]);
      }
    } else {
      $key = array_shift($keys);
      $this->unsetInArray($keys, $array[$key]);
    }
  }

  /**
   * Set key in array from given array of keys
   * e.g. ["foo", "bar", "baz"] <=> $array["foo"]["bar"]["baz"]
   *
   * @param $keys
   * @param $array
   * @param $value
   */
  protected function setInArray($keys, &$array, $value) {
    if (count($keys) === 1) {
      $array[$keys[0]] = $value;
    } else {
      $key = array_shift($keys);
      if (!isset($array[$key]) || !is_array($array[$key])) {
        $array[$key] = [];
      }
      $this->setInArray($keys, $array[$key], $value);
    }
  }

  /**
   * Find key in array from given array of keys
   * e.g. ["foo", "bar", "baz"] <=> $array["foo"]["bar"]["baz"]
   *
   * @param $keys
   * @param $array
   * @return bool
   */
  protected function findInArray($keys, &$array) {
    if (count($keys) > 0) {
      $key = array_shift($keys);
      if (!isset($array[$key])) {
        return false;
      }
      if (count($keys) === 0) {
        return isset($array[$key]) ? $array[$key] : false;
      } else {
        return $this->findInArray($keys, $array[$key]);
      }
    }
    return false;
  }
}