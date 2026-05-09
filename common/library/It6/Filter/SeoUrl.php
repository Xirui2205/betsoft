<?php

class It6_Filter_SeoUrl implements Zend_Filter_Interface {

/**
 * Translates non-ASCII character into ASCII, make value lowecase,
 * ensures that values start and ends with just one '/' character.
 * @param mixed $value
 */
public static function filterStatic($value) {
	$value = trim($value, '/');
	$value = It6_Text::safeString($value, true);
	return "/$value/"; 
}

/**
 * Delegates to filterStatic().
 * @see It6_Filter_SeoUrl::filterStatic()
 * @see Zend_Filter_Interface::filter()
 */
public function filter($value) {
	return static::filterStatic($value);
}

} // class
