<?php

/**
 * Doesn't override anything, only name reserved for future use.
 * @see ArrayIterator
 */
class It6_ArrayIterator extends ArrayIterator {
} // class It6_ArrayIterator

class It6_Array implements ArrayAccess, IteratorAggregate, Countable {

const ITEM_CLASS = ''; // if empty string then no class check will be performed
const ITERATOR_CLASS = 'It6_ArrayIterator';

/**
 * Array of It6_Amount
 * @var array
 */
protected $_items = array();

// interface Countable
public function count() {
	return count($this->_items);
}

// class specific functions
/**
 * @param array $items Array of instances of ITEM_CLASS as initial content
 */
public function __construct(array $items = array()) {
	$this->append($items);
}

/**
 * Append one or more items (instances of ITEM_CLASS if restricted)
 * @param array $more
 * @param bool $clone [optional] TRUE if items should be cloned
 * @returns It6_Array reference to this instance
 */
public function append($more, $clone = false) {
	if (!is_array($more))
		$more = array($more);
	$class = ('' == strlen(static::ITEM_CLASS) ? null : static::ITEM_CLASS);
	foreach ($more as &$item) {
		if (isset($class) && !($item instanceof $class))
			throw new Exception('Argument must be array of ' . $class . ' instances only');
		$this->_items[] = ($clone ? clone $item : $item);
	}
	return $this;
}

public static function sizeof($array) {
	if ($array instanceof It6_Array)
		return count($array->_items);
	else
		return count($array);
}

public static function is_array($array) {
	return (is_array($array) || $array instanceof Traversable);
}

// public function sumMember($member, $initial = null) {
// 	return array_reduce(
// 		$this->_items,
// 		function($accu, $amount) use ($member) { return $accu + $amount->$member; },
// 		$initial
// 	);
// }

/**
 * Computes count of $k-element combinations from $n-element set.
 * @param int $n Count of elements in a set
 * @param int $k Count of element in a combinations (1..$n)
 * @returns int Count of combinations
 */
public static function getCombinationCount($n, $k) {
	static $cache = array();
	if ($k < 1 || $k > $n || $n < 1)
		throw new Exception('Invalid parameter');
	if (!array_key_exists($n, $cache))
		$cache[$n] = array();
	if (!array_key_exists($k, $cache[$n])) {
		$nom = 1;
		for ($i = $n; $i > $k; --$i)
			$nom *= $i;
		$denom = 1;
		for ($i = 2; $i <= $n - $k; ++$i)
			$denom *= $i;
		return ($cache[$n][$k] = $nom / $denom);
	}
	else
		$cache[$n][$k];
}

/**
 * @param int $n Count of elements in given set
 * @param int $k Count of elements in combination
 * @param array $A Given set (must have at least $n elements, if it has more elements then only first $n elements are used)
 * @return array Array of arrays. Each array is one of all possible $k-element combinations from given set.
 */
public static function getCombinations($n, $k, array $A) {
	if ($k > $n)
		return array();
	if (0 >= $k)
		return array();
	if ($k == $n)
		return array($A);
	if (1 == $k) {
		$result = array();
		for ($i = 0; $i < $n; ++$i)
			$result[$i] = array( $A[$i] );
		return $result;
	}
	$headA = $A[0];
	$tailA = array_slice($A, 1);
	$c1 = self::getCombinations($n - 1, $k - 1, $tailA);
	$c2 = self::getCombinations($n - 1, $k, $tailA);
	$result = array();
	for ($i = 0; $i < sizeof($c1); ++$i)
		$result[$i] = array_merge(array($headA), $c1[$i]);
	return array_merge($result, $c2);
}

// IteratorAggregate interface

/**
 * @returns ITERATOR_CLASS instance wrapping items in this collection
 * @see IteratorAggregate
 */
public function getIterator() {
	$class = static::ITERATOR_CLASS;
	return new $class($this->_items);
}

// ArrayAccess methods

/**
 * @see ArrayAccess
 */
public function offsetSet($offset, $value) {
	if (is_null($offset))
		throw new Exception ("Cannot add new value to object");
	else
		$this->_items[$offset] = $value;
}

/**
 * @see ArrayAccess
 */
public function offsetExists($offset) {
	return isset($this->_items[$offset]);
}

/**
 * @see ArrayAccess
 */
public function offsetUnset($offset) {
	unset($this->_items[$offset]);
}

/**
 * @see ArrayAccess
 */
public function offsetGet($offset) {
	if (array_key_exists($offset, $this->_items))
		return $this->_items[$offset];
	else
		return null;
}

/*
 * Returns all elements from all arrays as a string
 * @param string $glue String that connects the elements
 * @param array $array The array that is to be imploded
 */
 public static function multi_implode($glue, $array) {
    $ret = '';
    foreach ($array as $item) {
        if (is_array($item)) {
            $ret .= self::multi_implode($glue, $item) . $glue;
        }
		else {
            $ret .= $item . $glue;
        }
    }
    $ret = substr($ret, 0, 0-strlen($glue));
    return $ret;
 }

} // class It6_Array
