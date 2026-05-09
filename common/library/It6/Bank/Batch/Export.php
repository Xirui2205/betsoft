<?php

/**
 * Class provides standard interface for export of bank operations to be executed by banking application
 */
abstract class It6_Bank_Batch_Export extends It6_Bank_Batch {

/**
 * Export single transactions
 * @param array $transaction (@see Entity_Transaction)
 * @param integer Number of the transaction in this export
 * return string
 */
abstract public function exportTransaction($transaction, $number);

/**
 * Use this method to preprocess all transactions and initialize export.
 * @param array $transactions Transactions to be exported (@see Entity_Transaction)
 * @return string Returned string will be prepended to final export data
 */
abstract public function preExport(array $transactions);

/**
 * Exports all given transactions one by one
 * @param array $transactions Transactions to be exported (@see Entity_Transaction)
 * @return string Exported data
 */
public function export(array $transactions) {
	$buff = $this->preExport($transactions);
	$number = 0;
	foreach ($transactions as $t) {
		$buff .= $this->exportTransaction($t, ++$number);
	}
	$buff .= $this->postExport($transactions);
	return $buff;
}

/**
 * Use this method to postprocess all transactions and finalize export.
 * @param array $transactions Transactions to be exported (@see Entity_Transaction)
 * @return string Returned string will be appended to final export data
 */
abstract public function postExport(array $transactions);

public static function getExportsDir() {
	return FILES_DIR . 'bank/export/';
}

/**
 * @returns string Short type name of this export (used in file names etc.)
 */
public function getTypeName() {
	return '';
}

/**
 * @return string Default file extension for this type of export (without leading dot)
 */
public function getFileExtension() {
	return 'txt';
}

/**
 * @param integer $exportTimestamp UNIX timestamp of export
 * @return string Complete file name
 */
public function getNewExportFileName($exportTimestamp) {
	$fn = strftime('%Y-%m-%d-%H-%M-%S', $exportTimestamp) . '-export';
	$typeName = static::getTypeName();
	if (!empty($typeName))
		$fn .= "-$typeName";
	$ext = static::getFileExtension();
	if (!empty($ext))
		$fn .= ".$ext";
	return $fn;
}

/**
 * @return file list
 */
public static function getExports() {
	//TODO: paging?
	$dir = static::getExportsDir();
	$files = array();
	if ($dh = opendir($dir)) {
		while( false !== ($f = readdir($dh)) ) {
			if (!is_dir($f) && 1 == preg_match('/^\\d{4}-\\d{2}-\\d{2}-\\d{2}-\\d{2}-\\d{2}-/', $f))
				$files[] = $f;
		}
		closedir($dh);
	}
	rsort($files);
	$files = array_slice($files, 0, 20);
	return $files;
}

} // class
