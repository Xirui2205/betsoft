<?php

class Models_Form_ManualPoints extends Models_Form_ManualTransactionAbstract {

	protected static $FORM_NAME = 'Points';
	protected static $ADMIN_SECTION_ID = 246;
	protected static $TRANSACTION_TYPE_NAME = 'manual';
	protected static $POINT_TYPE_ID = 1;
	protected static $OMIT_CURRENCY = true;

	protected static function make($values) {
		try {
			$ret = Zend_Registry::get('ws')->PointsTransaction->make(array(
				'typeName' => static::$TRANSACTION_TYPE_NAME,
				'pointTypeId' => static::$POINT_TYPE_ID,
				'value' => $values['amount'],
				'userId' => $values['userId']));

			return $ret;
		}
		catch (Exception $e) {
			return $e;
		}
	}

	protected static function findSimilarTransaction($values) {
		return Zend_Registry::get('ws')->PointsTransaction->findSimilar(array(
			'typeName' => static::$TRANSACTION_TYPE_NAME,
			'pointTypeId' => static::$POINT_TYPE_ID,
			'value' => $values['amount'] * static::$SIGN,
			'userId' => $values['userId']));
	}

	protected static function getType() {
		$ws = Zend_Registry::get('ws');

		if ( is_array(static::$TRANSACTION_TYPE_NAME) )
			$transactionTypeName = current(array_keys(static::$TRANSACTION_TYPE_NAME));
		else
			$transactionTypeName = static::$TRANSACTION_TYPE_NAME;

		return $ws->PointsTransactionType->getByNameAndPointsTypeId($transactionTypeName, static::$POINT_TYPE_ID);
	}

}
