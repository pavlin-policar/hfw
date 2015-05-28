<?php

namespace hfw\helpers;

/**
 * Class Stack
 *
 * @package hfw\helpers
 */
class Stack implements \Countable {
  /**
   * @var array
   */
  protected $_stack = [];

  /**
   * @return bool
   */
  public function isEmpty() {
    return count($this->_stack) == 0;
  }

  /**
   * Push element to top of stack
   *
   * @param $element
   */
  public function push($element) {
    array_unshift($this->_stack, $element);
  }

  /**
   * Take element off the top of the stack
   *
   * @return mixed
   */
  public function pop() {
    return array_shift($this->_stack);
  }

  /**
   * Look at element on top of stack
   *
   * @return mixed
   */
  public function peek() {
    return $this->_stack[0];
  }

  /**
   * @return int
   */
  public function size() {
    return count($this->_stack);
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Count elements of an object
   *
   * @link http://php.net/manual/en/countable.count.php
   * @return int The custom count as an integer.
   *       </p>
   *       <p>
   *       The return value is cast to an integer.
   */
  public function count() {
    return $this->size();
  }
}