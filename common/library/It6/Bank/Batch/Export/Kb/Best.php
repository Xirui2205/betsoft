<?php

class It6_Bank_Batch_Export_Kb_Best extends It6_Bank_Batch_Export {

const EOL = "\x0d\x0a";

private $id;
private $exportDate;
private $exportDateLong;
private $payDateLong;
private $currencyIso;

private $count;
private $sum;
private $seqNum;

private $bankAccountMap = array(  // Webservice_TransactionType::ACCOUNT_TYPE_* => parameterName
	Webservice_TransactionType::ACCOUNT_TYPE_USER => It6_Models_Parameter::NAME_BANK_EXPORT_ACCOUNT_KB_USER,
	Webservice_TransactionType::ACCOUNT_TYPE_HOST => It6_Models_Parameter::NAME_BANK_EXPORT_ACCOUNT_KB_HOST,
);
private $bankAccounts = array(); // Webservice_TransactionType::ACCOUNT_TYPE_* => array(accountNumber, bankCode)

public function __construct($db, $exportId = null, $currencyIso = null) {
	$this->id = $exportId;
	$this->currencyIso = (empty($currencyIso) ? 'CZK' : $currencyIso);
	$params = It6_Models_Parameter::getDataByName(array_values($this->bankAccountMap), $db);
	foreach ($this->bankAccountMap as $type => $paramName) {
		if (empty($params[$paramName]['value']))
			throw new Exception("Required parameter not found. name=$name");
		else {
			$parsed = $this->parseBankAccount($params[$paramName]['value']);
			if (empty($parsed))
				throw new Exception("Invalid account number format. $paramName={$params[$paramName]['value']}");
			else
				$this->bankAccounts[$type] = $parsed;
		}
	}
}

/**
 * Parses standard account number formats (bank code after slash, can have dash separator for account number prefix)
 * @param string $account Account number with bank code after slash, with optional dash
 * @return array|NULL (accountNumber, bankCode) If accountNumber have prefix, main part of number is padded by zeros from left to make it 10 digits long.
 *                                              NULL if not parsed.
 */
private function parseBankAccount($account) {
	if (1 != preg_match('!((\\d+)-)?(\\d+)/(\\d+)!', $account, $matches))
		return null;
	$number = $matches[3];
	$bank = $matches[4];
	if (empty($matches[2]))
		$prefix = '';
	else {
		$prefix = $matches[2];
		$number = str_pad($number, 10, '0', STR_PAD_LEFT);
	}
	return array($prefix . $number, $bank);
}

private function padInt($i, $length) {
	return str_pad($i, $length, '0', STR_PAD_LEFT);
}

private function padString($s, $length) {
	return str_pad($s, $length, ' ', STR_PAD_RIGHT);
}

/**
 * @param float $f
 * @param integer $length1 Length before virtual decimal point
 * @param integer $length2 Length after virtual decimal point
 * @return string
 */
private function padFixedPoint($f, $length1, $length2) {
	$i = floor($f * pow(10, $length2));
	return $this->padInt($i, $length1 + $length2);
}

private function filler($length) {
	return str_repeat(' ', $length);
}

/**
 * @see It6_Bank_Batch_Export
 */
public function preExport(array $transactions) {
	$this->count = 0;
	$this->sum = '0';
	$t = time();
	$this->exportDate = strftime('%y%m%d', $t);
	$this->exportDateLong = strftime('%Y%m%d', $t);
	$this->payDateLong = $this->exportDateLong; //TODO: really?
	$this->seqNum = DAILY_SEQUENCE_OFFSET_BANK_EXPORT + It6_Models_DailySequence::reserveNext(It6_Models_DailySequence::SEQID_BANK_EXPORT, count($transactions));
	if (empty($this->id))
		$this->id = $this->exportDate . $this->padInt($this->seqNum, 5);
	return 'HI'
		. $this->filler(9)
		. $this->exportDate
		. $this->padString($this->id, 14)
		. $this->filler(35 + 3 + 282)
		. self::EOL;
}

/**
 * @see It6_Bank_Batch_Export
 */
public function postExport(array $transactions) {
	$this->exportDate = strftime('%y%m%d');
	return 'TI'
		. $this->filler(9)
		. $this->exportDate
		. $this->padInt($this->count, 6)
		. $this->padInt($this->sum, 16 + 2)
		. $this->filler(310)
		. self::EOL;
}

/**
 * Export single transactions
 * @param array $transaction (@see Entity_Transaction)
 * @param integer Number of the transaction in this export
 * @return string
 */
public function exportTransaction($transaction, $number) {
	++$this->count;
	$amount = bcmul($transaction['value'], 100);
	$amount = str_replace('-', '', $amount); // bigint absolute value
	$bankAccount = (empty($transaction['bankAccountPrefix']) ? '' : $transaction['bankAccountPrefix']);
	$bankAccount .= $this->padInt($transaction['bankAccountNumber'], 10);
	$this->sum = bcadd($this->sum, $amount);
	$seqNum = $this->seqNum++;
	$trAccountType = $transaction['accountType'];
	$vsField = ('user' == $trAccountType ? 'userHandle' : 'branchHandle');
	if (empty($this->bankAccounts[$trAccountType]))
		throw new Exception("Unknown bank account for account type. accountType=$trAccountType");
	else
		$ourBankAccount = $this->bankAccounts[$trAccountType];
	return '01'
		. $this->padInt($seqNum, 5)
		. $this->exportDateLong
		. $this->payDateLong
		. $this->currencyIso
		. $this->padInt($amount, 15)
		. '0' // 0=uhrada 1=inkaso
		. $this->filler(3 + 1) // mena, konverze
		. $this->padInt(0, 10) // konst.symbol
		. $this->padString(' ', 140) // zprava pro partnera
		. $this->filler(3)
		. $this->padInt($ourBankAccount[1], 4) // kod banky prikazce
		. $this->padInt($ourBankAccount[0], 16) // ucet prikazce
		. $this->padInt('0', 10) // VS prikazce (prepsano VS partnera)
		. $this->padInt('0', 10) // SS prikazce (prepsano SS partnera)
		. $this->padString(' ', 30) // poznamka prikazce (Priorita X/$transaction->notes?)
		. $this->filler(3)
		. $this->padInt($transaction['bankAccountBankCode'], 4) // kod banky prijemce
		. $this->padInt($bankAccount, 16) // ucet prijemce
		. $this->padInt($transaction[$vsField], 10) // VS partnera
		. $this->padInt(0, 10) // SS partnera
		. $this->padString(' ', 30) // poznamka partnera (Priorita X/$transaction->notes?)
		. ' ' // E=express, A=SWIFT express, jinak standard
		. ' ' // Forex: Y=pro domluveny kurz, jinak dle kurz.listku
		. $this->filler(7)
		. self::EOL;
}

public function getTypeName() {
	return 'kb';
}

public function getFileExtension() {
	return 'IKM';
}

} // class
