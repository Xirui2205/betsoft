<?php
class It6_Campaign_Exchange_ToMoney extends It6_Campaign_ExchangeCampaign { 
	const PARAM_SPEND_RATIO = 'spendRatio';
	const PARAM_AMOUNT_OFFSET = 'amountOffset';
	const POINTS_TRANSACTION_TYPE = 8;
	

	public static function validate($crc) {
		return $crc['amount'] <= static::getAmountLimit($crc);
	}

	public static function getAmountLimit($crc) {
		$user = Webservice_User::getById($crc['userId']);
		$type = Webservice_PointsTransactionType::getById(static::POINTS_TRANSACTION_TYPE);

		foreach ( $user['points'] as $point ) {
			if ( $point['pointTypeId'] == $type->pointTypeId ) {

				$spendRatio = static::getParameter(static::PARAM_SPEND_RATIO);
				$amountOffset = static::getParameter(static::PARAM_AMOUNT_OFFSET);
				$amountLimit = $amountOffset + $spendRatio * $point['balanceSpend'] - $point['balanceExchange']; 

				return max(0, min($point['balance'], $amountLimit));
			}
		}

		throw new It6_XmlRpc_Exception("Unknown pointTypeId.", 0, $e);
	}
	
	public static function getRate($crc) {
		$db = Zend_Registry::get('db');

		$type = Webservice_PointsTransactionType::getById(static::POINTS_TRANSACTION_TYPE);
		
		$rate = 1/Webservice_PointsType::getRate($type->pointTypeId);
		$currencyId = It6_Models_User::get($crc['userId'], 'currencyId', $db);
		$rate *= Webservice_Currency::getRate($currencyId);

		return $rate;
	}

	public static function run($crc) {

		$db = Webservice_AbstractWebService::getMainDb();
		$admindb = Webservice_AbstractWebService::getAdminDb();
		
		It6_DbTransaction::begin($db);
		It6_DbTransaction::begin($admindb);

		try {
			Webservice_PointsTransaction::make(array(
				'userId' => $crc['userId'],
				'value'  => -$crc['amount'],
				'typeId' => static::POINTS_TRANSACTION_TYPE));

			$type = Webservice_PointsTransactionType::getById(static::POINTS_TRANSACTION_TYPE);

			$currencyId = It6_Models_User::get($crc['userId'], 'currencyId', $db);

			Webservice_Transaction::make(array(
				'userId'     => $crc['userId'],
				'value'      => Webservice_PointsType::exchangeToMoney($currencyId, $type->pointTypeId, $crc['amount']),
				'currencyId' => $currencyId,
				'typeName'   => Webservice_TransactionType::NAME_USER_EXCHANGE_FROM_POINTS,
				'hostId'     => It6_Models_Host::ID_INTERNET));

			It6_DbTransaction::commit($db);
			It6_DbTransaction::commit($admindb);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not run exchangeToMoney campaign.", 0, $e);
		}

		return true;
	}
}