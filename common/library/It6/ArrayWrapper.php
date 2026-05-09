<?php

class It6_ArrayWrapperIterator extends ArrayIterator {
	public function current() {
		$ret = parent::current();
		if ( is_array($ret) ) {
			return new It6_ArrayWrapper($ret);
		} else {
			return $ret;
		}
	}
}

class It6_ArrayWrapper implements ArrayAccess, IteratorAggregate, Countable {
	public $collection;

	public function __construct($collection) {
		if ( $collection instanceof It6_ArrayWrapper )
			$this->collection = $collection->collection;
		else
			$this->collection = $collection;
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->collection[] = $value;
		} else {
			$this->collection[$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		return isset($this->collection[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->collection[$offset]);
	}

	public function offsetGet($offset) {
		if (isset($this->collection[$offset]) )
			if ( is_array($this->collection[$offset]) ) {
				return new It6_ArrayWrapper($this->collection[$offset]);
			}
			else return $this->collection[$offset];
		else {
			return null;
		}
	}

	public function __get($offset) {
		return $this->offsetGet($offset);
	}

	public function __set($offset, $value) {
		return $this->offsetSet($offset, $value);
	}

	public function __isset($offset) {
		return $this->offsetExists($offset);
	}

	public function __unset($offset) {
		return $this->offsetUnset($offset);
	}

	public function getIterator() {
		return new It6_ArrayWrapperIterator($this->collection);
	}

	public function count() {
		return count($this->collection);
	}

	public static function toNativeArray($array){
		if ( is_array($array) || $array instanceof It6_ArrayWrapper ) {
			$retdata = array();
			foreach($array as $key => $element){
				if(is_array($element))
					$retdata[$key] = static::toNativeArray($element);
				else if ( $element instanceof IT6_ArrayWrapper )
					$retdata[$key] = static::toNativeArray($element->collection);
				else
					$retdata[$key] = $element;
			}
			return $retdata;
		}
		else {
			return $array;
		}
	}

	public static function toAssocArray($array, $key, $value, $init = array()) {
		$ret = $init;
		$formatter = new Zend_Log_Formatter_Simple($value);
		foreach ( $array as $item ) {
			$ret[$item[$key]] = $formatter->format($item);
		}
		return $ret;
	}

	public static function toAssocLikeArray($array, $key) {
		$outdata = array();

		foreach($array as $arrElement) {
			$outdata[$arrElement[$key]][] = self::toNativeArray($arrElement);
		}

		return $outdata;
	}

	public static function isArray($array) {
		return (is_array($array) || $array instanceof It6_ArrayWrapper);
	}

	/**
	 * Compatibility method that smoothly checks existence of key/property in native array or in It6_ArrayWrapper or in general object.
	 * @param mixed $key Any possible array key value
	 * @param array|It6_ArrayWrapper|Object $array
	 */
	public static function keyExists($key, $array) {
		if (is_array($array))
			return array_key_exists($key, $array);
		else if ($array instanceof It6_ArrayWrapper)
			return static::keyExists($key, $array->collection);
		else if (is_object($array))
			return property_exists($array, $key);
	}

	public static function toMultiOption($array, $key='id', $value='name') {
		$retdata = array();

		foreach($array as $item) {
			$retdata[$item[$key]] = $item[$value];
		}

		return $retdata;
	}
}
