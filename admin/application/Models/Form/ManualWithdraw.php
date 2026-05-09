<?php

class Models_Form_ManualWithdraw extends Models_Form_ManualTransactionAbstract {

	protected static $FORM_NAME = 'Withdraw';
	protected static $ADMIN_SECTION_ID = 244;
	protected static $TRANSACTION_TYPE_NAME = 'user.withdraw.bank';
	protected static $SIGN = -1;

}
