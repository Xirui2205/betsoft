<?php

/**
 * Bet dao.
 * @author Pavel Klinger
 * @see Webservice_Bet
 * 
 */
abstract class Entities_AbstractEntity implements ArrayAccess {
	
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			throw new Exception ("Cannot add new value to object");
		} else {
			$this->$offset = $value;
		}
	}
        
	public function offsetExists($offset) {
		return isset($this->$offset);
	}
        
	public function offsetUnset($offset) {
		unset($this->$offset);
	}
        
	public function offsetGet($offset) {
		return isset($this->$offset) ? $this->$offset : null;
	}
	
}
