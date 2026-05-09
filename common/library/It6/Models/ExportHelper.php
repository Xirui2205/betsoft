<?php

class It6_Models_ExportHelper {

public static function assocArrayToCsv($array) {
	$array = It6_ArrayWrapper::toNativeArray($array);
	if ( empty($array) ) return "";
	$header = '"'.implode(array_keys($array[0]),'","').'"';
	$ret = array();
	foreach ($array as $line) {
		$ret[] = '"'.implode($line, '","').'"';
	}
	return $header . "\n" . implode($ret,"\n");
}
}
