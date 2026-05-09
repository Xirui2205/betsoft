<?php

class Models_Form_ManualBalance extends Models_Form_ManualTransactionAbstract {

	public static $FORM_NAME = 'Balance';
	public static $ADMIN_SECTION_ID = 245;
	public static $TRANSACTION_TYPE_DEPOSIT_NAME = 'user.deposit.manual';
	public static $TRANSACTION_TYPE_WITHDRAW_NAME = 'user.withdraw.manual';

	protected static function getTransactionType($values) {
		return $values['amount'] * static::$SIGN < 0
			? static::$TRANSACTION_TYPE_WITHDRAW_NAME
			: static::$TRANSACTION_TYPE_DEPOSIT_NAME;
	}
	
	protected static function addAmountValidators($amount) {
		return $amount;
	}
	

}
