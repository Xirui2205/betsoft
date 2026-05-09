<?php

class It6_Models_WebPayOrder extends It6_Models_Abstract {

const STATUS_FAIL = -1;
const STATUS_PRE_REQUEST = 0;
const STATUS_IN_PROGRESS = 1;
const STATUS_OK = 2;
const STATUS_REJECTED = 3;

/*
`order_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
`user_id` INT(10) UNSIGNED NOT NULL,
`transaction_id` INT(11) UNSIGNED NULL DEFAULT NULL,
`amount` DECIMAL(11,2) NOT NULL,
`fee` DECIMAL(11,2) NOT NULL,
`currency_id` SMALLINT(5) UNSIGNED NOT NULL,
`created` DATETIME NOT NULL,
`updated` DATETIME NULL DEFAULT NULL,
`status` SMALLINT(5) NOT NULL COMMENT '-1=failed, 0=pre-request, 1=in progress, 2=ok, 3=rejected',
`result_text` TEXT NULL DEFAULT NULL,
*/

protected static $_cache = array();
protected static $_table = 'webpay_order';
protected static $_joinPrefix = false;
protected static $_columns = array(
	'id' => 'order_id',
	'userId' => 'user_id',
	'transactionId' => 'transaction_id',
	'amount' => 'amount',
	'fee' => 'fee',
	'currencyId' => 'currency_id',
	'created' => 'created',
	'updated' => 'updated',
	'status' => 'status',
	'resultText' => 'result_text',
);
protected static $_joinedColumns = array();
protected static $_joinConstraints = array();
protected static $_joinPrefixes = false;
protected static $_primaryKey = 'id';
protected static $_readDataModifiers = array();
protected static $_readDataAllModifiers = array();

//public static function readDataForUser($userId, &$db = null) {
//}


} // class It6_Models_WebPayOrder