<?php

class It6_Uri_Http {

/**
 * Builds HTTP query string
 * @param array $params Associative array of query parameters
 * @param boolean $html TRUE for HTML entities encoding 
 * @param boolean|null $start TRUE to prepend '?', NULL to prepend '?' if $params is not empty array, otherwise to not prepend anything
 * @return string
 */
public static function buildQuery(array $params, $html = false, $start = null) {
	$query = (true === $start || (!isset($start) && !empty($params)) ? '?' : '');
	$first = true;
	foreach ($params as $name => $value) {
		if ($first)
			$first = false;
		else
			$query .= '&';
		$query .= urlencode($name) . '=' . urlencode($value);
	}
	return ($html ? htmlspecialchars($query) : $query);
}

/**
 * Remove diacritics, special characters, characters to lower, words separated by "-"
 * @param string $text Input text to be processed (for example article title)
 * @return string $text Url text
 */
public static function friendlyUrl($text) {
	$text = trim(mb_strtolower(strip_tags($text), 'UTF-8'));
	$text = preg_replace("/\s{2,}/"," ", $text);
	$search		= array(' ', 'á', 'č', 'ď', 'é', 'ě', 'í', 'ň', 'ó', 'ř', 'š', 'ť', 'ú', 'ů', 'ý', 'ž', 'ä', 'ľ', 'ô', 'ŕ');
	$replace	= array('-', 'a', 'c', 'd', 'e', 'e', 'i', 'n', 'o', 'r', 's', 't', 'u', 'u', 'y', 'z', 'a', 'l', 'o', 'r');
	$text = str_replace($search, $replace, $text);
	$text = preg_replace("/[^a-z0-9-]/", "", $text);
	$text = preg_replace("/-{2,}/","-", $text);
	return $text;
}

} // class
