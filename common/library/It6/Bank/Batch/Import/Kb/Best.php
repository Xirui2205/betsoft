<?php

class It6_Bank_Batch_Import_Kb_Best extends It6_Bank_Batch_Import {

protected static $fieldDefs = array(
	'globalId' => array('offset' => 86, 'length' => 31, 'format' => 'trim'),
	'importId' => array('offset' => 2, 'length' => 5, 'format' => '0int'),
	'exportId_1' => array('offset' => 201, 'length' => 3),
	'exportId_2' => array('offset' => 469, 'length' => 2),
	'type' => array('offset' => 46, 'length' => 1, 'format' => '0int'),
	'isDebet' => array('offset' => 204, 'length' => 1, 'format' => 'boolean'),
	'amount' => array('offset' => 50, 'length' => 15, 'format' => '0fixedPoint', 'formatParams' => 2),
	'currencyIso' => array('offset' => 47, 'length' => 3, 'format' => 'trim'),
	'variableSymbol' => array('offset' => 117, 'length' => 10, 'format' => '0long'),
	'constantSymbol' => array('offset' => 137, 'length' => 10, 'format' => '0long'),
	'specificSymbol' => array('offset' => 147, 'length' => 10, 'format' => '0long'),
	'dateOfAccounting' => array('offset' => 175, 'length' => 8, 'format' => 'date', 'formatParams' => '%Y%m%d'),
	'ourAccount' => array('offset' => 7, 'length' => 16, 'format' => '0long'),
	//'ourBankCode' => array('offset' => 0, 'length' => 7),
	'theirsAccount' => array('offset' => 23, 'length' => 16, 'format' => '0long'),
	'theirsBankCode' => array('offset' => 39, 'length' => 7, 'format' => '0int4'),
	'theirsName' => array('offset' => 439, 'length' => 30, 'format' => 'trim'),
);

protected function formatField($name, $value) {
	if (array_key_exists($name, static::$fieldDefs) && !empty(static::$fieldDefs[$name]['format'])) {
		switch (static::$fieldDefs[$name]['format']) {
		case '0int':
			return intval($value);
		case '0int4':
			return str_pad(intval($value), 4, '0', STR_PAD_LEFT);
		case '0long':
			return ltrim(ltrim($value), '0');
		case '0fixedPoint':
			return floatval($value) / pow(10, static::$fieldDefs[$name]['formatParams']);
		case 'boolean':
			return !empty($value);
		case 'booleanInv':
			return empty($value);
		case 'date':
			return strptime($value, static::$fieldDefs[$name]['formatParams']);
		case 'trim':
			return trim($value);
		default:
			break;
		}
	}
	return $value;
}

/**
 * @see It6_Bank_Batch_Import::parseLine()
 */
public function parseLine($line, $lineNumber) {
	$opType = substr($line, 0, 2);
	if ('52' != $opType)
		return null;
	$fields = It6_Bank_Batch_Import::parseFixedLengthFields($line, static::$fieldDefs);
	$fields['exportId'] = trim($fields['exportId_1'] . $fields['exportId_2']);
	foreach ($fields as $name => &$value)
		$value = $this->formatField($name, $value);
	return new It6_Bank_Batch_Import_Line($fields);
}

/**
 * @see It6_Bank_Batch_Import::logTransactionMade()
 */
public function logTransactionMade($transactionId, It6_Bank_Batch_Import_Line $line, array $batch, &$db = null) {
	if (!isset($db))
		$db = Zend_Registry::get('db');
	$start = (is_int($batch['start']) ? It6_Date::timestampToDb($batch['start']) : $batch['start']);
	$db->insert(
		'import_log_kb_best',
		array(
			'kbi_id' => $line->globalId,
			'transaction_id' => $transactionId,
			'imported_at' => $start,
			'admin_id' => $batch['adminId'],
			'batch_name' => $batch['name']
		)
	);
}

/**
 * @see It6_Bank_Batch_Import::wasLineImported()
 */
public function wasLineImported(It6_Bank_Batch_Import_Line $line, &$db = null) {
	if (!isset($db))
		$db = Zend_Registry::get('db');
	$logs = $db->select()
		->from('import_log_kb_best', array(
			'globalId' => 'kbi_id',
			'transactionId' => 'transaction_id',
			'importTime' => 'imported_at',
			'adminId' => 'admin_id',
			'batchName' => 'batch_name',
		))
		->where('kbi_id=?', $line->globalId)
		->query()
		->fetchAll();
	return (empty($logs) ? false : $logs[0]);
}

} // class
