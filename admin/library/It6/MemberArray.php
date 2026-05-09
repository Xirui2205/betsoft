<?php

/**
 * Class wrapping array that items can be accessed as member variables (in safe manner).
 */

class It6_MemberArray {

protected $_items = array();

public function __get($name) {
	return ($this->has($name) ? $this->_items[$name] : null);
}

public function __set($name, $value) {
	$this->_items[$name] = $value;
}

public function has($name) {
	return array_key_exists($name, $this->_items);
}

} // class It6_MemberArray