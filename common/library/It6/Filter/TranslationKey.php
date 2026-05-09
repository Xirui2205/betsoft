<?php

/**
 * This filter makes input for translation tags more secure.
 * @author Petr Stastny
 */
class It6_Filter_TranslationKey implements Zend_Filter_Interface {

/**
 * Translates non-ASCII charaters to ASCII characters, makes value lowercase.
 * @param mixed $value
 */
public function filterStatic($value) {
	return It6_Text::safeString($value, null);
}

/**
 * Delegates to filterStatic().
 * @see It6_Filter_TranslationKey::filterStatic()
 * @see Zend_Filter_Interface::filter()
 */
public function filter($value) {
	return static::filterStatic($value);
}
	
} // class
