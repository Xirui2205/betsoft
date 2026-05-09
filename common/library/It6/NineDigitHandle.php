<?php

/**
 * Class that wraps 9-digits handles (originaly for ticket) encoding and decoding.
 */

class It6_NineDigitHandle {

protected static $_prngTable = 'prng_9';

public static function prng_3($seedA, $seedB, $seedC, &$db = null) {
	if (!isset($db))
		$db = Zend_Registry::get('db');
	$rows = $db->select()
		->from(static::$_prngTable, array('s' => 'seed', 'a' => 'prn_a', 'b' => 'prn_b', 'c' => 'prn_c'))
		->where('seed IN (?)', array($seedA, $seedB, $seedC))
		->query()
		->fetchAll();
	foreach ($rows as $row) {
		$seed = $row['s'];
		if ($seed == $seedA)
			$a = $row['a'];
		if ($seed == $seedB)
			$b = $row['b'];
		if ($seed == $seedC)
			$c = $row['c'];
	}
	return array($a, $b, $c);
}

public static function prng_3_inv($prnA, $prnB, $prnC, &$db = null) {
	if (!isset($db))
		$db = Zend_Registry::get('db');
	$rows = $db->select()
		->from(static::$_prngTable, array('s' => 'seed', 'a' => 'prn_a', 'b' => 'prn_b', 'c' => 'prn_c'))
		->where('prn_a=?', $prnA)
		->orWhere('prn_b=?', $prnB)
		->orWhere('prn_c=?', $prnC)
		->query()
		->fetchAll();
	foreach ($rows as $row) {
		$seed = $row['s'];
		if ($row['a'] == $prnA)
			$seedA = $row['s'];
		if ($row['b'] == $prnB)
			$seedB = $row['s'];
		if ($row['c'] == $prnC)
			$seedC = $row['s'];
	}
	return array($seedA, $seedB, $seedC);
}

public static function makeHandle($number, &$db = null) {
	$number = str_pad($number, 9, '0', STR_PAD_LEFT);
	$part1 = intval($number[0] . $number[3] . $number[6]);
	$part2 = intval($number[1] . $number[4] . $number[7]);
	$part3 = intval($number[2] . $number[5] . $number[8]);
	$prns = self::prng_3($part1, $part2, $part3, $db);
	foreach ($prns as &$prn)
		$prn = str_pad($prn, 3, '0', STR_PAD_LEFT);
	list($a, $b, $c) = $prns;
	$part1 = intval($a[0] . $b[0] . $c[0]);
	$part2 = intval($a[1] . $b[1] . $c[1]);
	$part3 = intval($a[2] . $b[2] . $c[2]);
	$prns = self::prng_3($part1, $part2, $part3, $db);
	foreach ($prns as &$prn)
		$prn = str_pad($prn, 3, '0', STR_PAD_LEFT);
	list($a, $b, $c) = $prns;
	return $a . $b . $c;
}

public static function decodeHandle($handle, &$db = null) {
	$a = intval(substr($handle, 0, 3));
	$b = intval(substr($handle, 3, 3));
	$c = intval(substr($handle, 6, 3));
	$parts = self::prng_3_inv($a, $b, $c, $db);
	foreach ($parts as &$prn)
		$prn = str_pad($prn, 3, '0', STR_PAD_LEFT);
	list($part1, $part2, $part3) = $parts;
	$a = intval($part1[0] . $part2[0] . $part3[0]);
	$b = intval($part1[1] . $part2[1] . $part3[1]);
	$c = intval($part1[2] . $part2[2] . $part3[2]);
	list($part1, $part2, $part3) = self::prng_3_inv($a, $b, $c, $db);
	$part1 = str_pad($part1, 3, '0', STR_PAD_LEFT);
	$part2 = str_pad($part2, 3, '0', STR_PAD_LEFT);
	$part3 = str_pad($part3, 3, '0', STR_PAD_LEFT);
	return intval($part1[0] . $part2[0] . $part3[0] . $part1[1] . $part2[1] . $part3[1] . $part1[2] . $part2[2] . $part3[2]);
}

public static function fixHandle(&$handle) {
	if (It6_ArrayWrapper::isArray($handle)) {
		foreach(array_keys($handle) as $k)
			$handle[$k] = str_pad($handle[$k], 9, '0', STR_PAD_LEFT);;
	}
	else
		$handle = str_pad($handle, 9, '0', STR_PAD_LEFT);
}

} // class It6_NineDigitHandle
