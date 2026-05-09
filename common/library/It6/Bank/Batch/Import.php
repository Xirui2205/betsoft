<?php

/**
 * Class provides standard interface for importing data from bank into our system
 */
abstract class It6_Bank_Batch_Import extends It6_Bank_Batch {

/**
 * @param string $line Line in specific format to be parsed
 * @param integer $lineNumber Number of line in import batch
 * @return It6_Bank_Batch_Import_Line|NULL|boolean NULL to skip, FALSE for error
 */
abstract public function parseLine($line, $lineNumber);

/**
 * Log made transaction (used e.g. for re-import attempt detection)
 * @param integer $transactionId
 * @param It6_Bank_Batch_Import_Line $line
 * @param array $batch Data of current batch ('name' => batch_name, 'start' => DB_string|integer start of batch timestamp, 'adminId' => admin_id)
 * @param string|integer|NULL String in DB datetime format, integer UNIX timestamp or NULL for current time (best performance has string version)
 * @param string $batchName
 * @param Zend_Db_Adapter $db
 * @throws Exception
 */
abstract public function logTransactionMade($transactionId, It6_Bank_Batch_Import_Line $line, array $batch, &$db = null);

/**
 * Test if line was already imported
 * @param It6_Bank_Batch_Import_Line $line
 * @param Zend_Db_Adapter $db
 * @return boolean|array FALSE if line wasn't imported (transaction made) else data of batch and transaction
 *                       keys: globalId, transactionId, importTime, adminId, batchName
 */
abstract public function wasLineImported(It6_Bank_Batch_Import_Line $line, &$db = null);

public function importFile($fileName, $db = null, &$log = null, $batchName = '') {
//	@unlink('/tmp/import.log');
	$f = fopen($fileName, 'r');
	if (!$f)
		throw new Exception('Cannot open file for reading: ' . $fileName);
	$number = 0;
	$err = null;
	$useDb = isset($db);
	$batch = array(
		'name' => $batchName,
		'start' => It6_Date::dbNow(),
		'adminId' => Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN),
	);
	if ($useDb)
		It6_DbTransaction::begin($db);
	try {
		while (!feof($f)) {
			$s = fgets($f);
			if (false !== $s) {
				$line = $this->parseLine($s, ++$number);
				if (false === $line)
					throw new Exception('Parse error on line: ' . $number);
				else if (!empty($line))
					$this->importLine($line, $db, $log, $batch);
			}
		}
		if ($useDb)
			It6_DbTransaction::commit($db);
	}
	catch (Exception $e) {
		$err = $e;
		if ($useDb)
			It6_DbTransaction::rollback($db);
	}
	fclose($f);
	if (!empty($err))
		throw $err;
	return $number;
}

protected function _usingSpecificSymbol() {
	return defined('SS_USER_DEPOSIT_BANK');
}

protected function importLine(It6_Bank_Batch_Import_Line $line, $db = null, &$log = null, array $batch) {
	static $useSs = null;
	if (!isset($useSs))
		$useSs = $this->_usingSpecificSymbol();

	$batchName = $batch['name'];

	$importedLine = $this->wasLineImported($line, $db);
	if (false !== $importedLine) {
		if (isset($log)) {
			$data = array();
			foreach ($importedLine as $key => $value)
				$data[] = "$key:'$value'";
			$data = implode(', ', $data);
			$log[] = "WARNING: Line was skipped (already imported) {{$data}}";
		} 
		return;
	}

	if ($useSs) {
		$ss = ltrim($line->specificSymbol, '0');
		if (SS_USER_DEPOSIT_BANK != $ss)
			return;
	}

	// currently we import only transactions that are crediting our bank account
	// (we are going to make only Webservice_TransactionType::NAME_USER_DEPOSIT_BANK transactions)
	if (It6_Bank_Batch_Import_Line::TYPE_CREDIT != $line->type)
		return;
	
	$user = It6_Models_User::getDataByHandle(sprintf("%09d", $line->variableSymbol), $db);// some banks strips zeros from the begging of the VS, we need them...
	if (empty($user)) {
		$msg = 'Variable symbol is not user handle: VS="' . $line->variableSymbol . "\" (batch \"$batchName\")";
		if (isset($log))
			$log[] = "ERROR: $msg";
		It6_Log::err($msg, It6_Log::TAG_TRANSACTION_IMPORT);
		return;
	}

	$currency = It6_Models_Currency::getCurrencyByName($line->currencyIso);
	if (empty($currency)) {
		$msg = 'Currency not found: ISO name = "' . $line->currencyIso . "\" (batch \"$batchName\")";
		if (isset($log))
			$log[] = "ERROR: $msg";
		It6_Log::err($msg, It6_Log::TAG_TRANSACTION_IMPORT);
		return;
	}

	$sign = ($line->isDebet ? -1 : 1);
	$amount = $sign * $line->amount;
	$transaction = array(
		'typeName' => Webservice_TransactionType::NAME_USER_DEPOSIT_BANK,
		'currencyId' => $currency['id'],
		'value' => $amount,
		'userId' => $user['id'],
		'hostId' => It6_Models_Host::ID_INTERNET,
	);
	$transactionId = Zend_Registry::get('ws')->Transaction->make($transaction);
	$this->logTransactionMade($transactionId, $line, $batch, $db);
	if (isset($log))
		$log[] = "ID: $transactionId " . ((string)$line);
}

/**
 * Parses line using given fixed length fields description
 * @param string $line Line to be parsed
 * @param array $fieldDefs Line fields definitions: array( 'name_in_import_line' => array('offset' => integer, 'length' => integer), ... )
 * @return boolean|array FALSE on failure or array of parsed field => value pairs
 */
public static function parseFixedLengthFields($line, array $fieldDefs) {
	$result = array();
	$lineLength = strlen($line);
	foreach ($fieldDefs as $field => $def) {
		$offset = $def['offset'];
		$length = $def['length'];
		if ($offset + $length > $lineLength)
			return false;
		$result[$field] = substr($line, $offset, $length);
	}
	return $result;
}

} // class
