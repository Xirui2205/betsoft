<?php

class It6_Bank_Batch_Import_Line {

const TYPE_DEBET = 0;
const TYPE_CREDIT = 1;
const TYPE_CANCEL_DEBET = 2;
const TYPE_CANCEL_CREDIT = 3;

public $globalId;
public $importId;
public $exportId;
/**
 * One of TYPE_* constants
 * @var integer
 */
public $type;
public $isDebet; // TRUE means that theirs account is being credited (we are taking from our account)
public $amount;
public $currencyIso;
public $variableSymbol;
public $constantSymbol;
public $specificSymbol;
/**
 * Date time structure as returned from strptime()
 * @var array
 */
public $dateOfAccounting;
public $ourAccount;
public $ourBankCode;
public $theirsAccount;
public $theirsBankCode;
public $theirsName;

private static $fields = array(
	'globalId', 'importId', 'exportId', 'type', 'isDebet', 'amount', 'currencyIso',
	'variableSymbol', 'constantSymbol', 'specificSymbol', 'dateOfAccounting',
	'ourAccount', 'ourBankCode', 'theirsAccount', 'theirsBankCode', 'theirsName',
);

public function __construct(array $data = null) {
	$this->reset();
	if (!empty($data)) {
		foreach ($data as $field => $value) {
			if (in_array($field, static::$fields))
				$this->$field = $value;
		}
	}
}

public function reset() {
	foreach (self::$fields as $field) 
		$this->$field = null;
}

///**
// * Try to convert field values from strings to appropriate types
// * @param array $callbacks Array of callbacks to override standard conversions: array( 'fieldName' => callback, ... )
// */
//public function formatFields(array $callbacks = array()) {
//	
//}

public function __toString() {
	$debet = ($this->isDebet ? 'A' : 'N');
	return "{VS:{$this->variableSymbol}; KS:{$this->constantSymbol}; DEBET:{$debet};"
		. " CASTKA:{$this->amount}; MENA:{$this->currencyIso} ZUCTU: {$this->theirsAccount}/{$this->theirsBankCode}; }";
}

public static function getCsvHeader($separator = ';', $stringDelim = '"') {
	return implode($separator, array_map(function($field) use($stringDelim) { return "$stringDelim$field$stringDelim"; }, static::$fields));
}

public function toCsvLine($separator = ';', $stringDelim = '"') {
	$self = $this;
	return implode($separator, array_map(
		function($field) use($self, $stringDelim) {
			if ('dateOfAccounting' == $field) {
				$t = $self->$field;
				return $stringDelim . strftime("%F %T", mktime($t['tm_hour'], $t['tm_min'], $t['tm_sec'], $t['tm_mon']+1, $t['tm_mday'], $t['tm_year']+1900) ) . $stringDelim;
			}
			else
				return (is_string($self->$field) ? "$stringDelim{$self->$field}$stringDelim" : $self->$field);
		},
		static::$fields
	));
}
} // class
