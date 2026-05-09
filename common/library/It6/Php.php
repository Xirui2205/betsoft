<?php

class It6_Php {

public static function getRemoteAddr() {
	if (!empty($_SERVER['HTTP_X_REAL_IP']))
		return $_SERVER['HTTP_X_REAL_IP'];
	else if (!empty($_SERVER['REMOTE_ADDR']))
		return $_SERVER['REMOTE_ADDR'];
	else return '';
}

/**
 * Retrieves class constants from given class and given filter for constants' names
 * @param string $class Class from which should be constants retrieved
 * @param string $filter Prefix (case-sensitive) or REGEXP 
 * @param boolean $isRegexp TRUE if passed $filter is REGEXP, FALSE is default.
 * @param string $outputType
 *    <ul>
 *     <li>'map' ... name => value (default)</li>
 *     <li>'names' ... list of constants' names</li>
 *     <li>'values' ... list of constants' values</li>
 *     <li>'value-set' ... value => countOfValues</li>
 *    </ul>
 * @return array (constantName => constantValue)
 */
public static function getFilteredClassConstants($class, $filter, $isRegexp = false, $outputType = 'map') {
	if ($isRegexp)
		$fnTest = function($name) use ($filter) { return preg_match($filter, $name); };
	else {
		$len = strlen($filter);
		$fnTest = function($name) use ($filter, $len) { return (substr($name, 0, $len) == $filter); };
	}
	switch (strtolower($outputType)) {
	case 'names':
		$fnAdd = function(&$consts, $name, $value) { $consts[] = $name; };
		break;
	case 'values':
		$fnAdd = function(&$consts, $name, $value) { $consts[] = $value; };
		break;
	case 'value-set':
		$fnAdd = function(&$consts, $name, $value) {
			if (isset($consts[$value]))
				++$consts[$value];
			else
				$consts[$value] = 1;
		};
		break;
	case 'map':
	default:
		$fnAdd = function(&$consts, $name, $value) { $consts[$name] = $value; };
	}
	$refl = new ReflectionClass($class);
	$consts = array();
	foreach ($refl->getConstants() as $name => $value) {
		if ($fnTest($name))
			$fnAdd($consts, $name, $value);
	}
	return $consts;
}

} // class
