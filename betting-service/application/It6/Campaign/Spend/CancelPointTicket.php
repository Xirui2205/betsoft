<?php
class It6_Campaign_Spend_CancelPointTicket extends It6_Campaign_SpendCampaign {
	 
	const POINTS_TRANSACTION_TYPE = 2;
	
	public static function validate($crc) {
		$exchangePointTransaction = Webservice_PointsTransaction::getOneWhere(array(
			'ticketId = ?' => $crc['ticketId'],
			'typeId = ?' => It6_Campaign_Spend_CreatePointTicket::POINTS_TRANSACTION_TYPE,
		));

		return !empty($exchangePointTransaction);
	}

	public static function run($crc) {
		$exchangePointTransaction = Webservice_PointsTransaction::getOneWhere(array(
			'ticketId = ?' => $crc['ticketId'],
			'typeId = ?' => It6_Campaign_Spend_CreatePointTicket::POINTS_TRANSACTION_TYPE,
		));
		
		$exchangeTransaction = Webservice_Transaction::getOneWhere(array(
			'ticketId = ?' => $crc['ticketId'],
			'typeName = ?' => Webservice_TransactionType::NAME_USER_EXCHANGE_FROM_POINTS,
		));
		
		Webservice_Transaction::make(array(
			'userId'     => $exchangeTransaction['userId'],
			'ticketId'   => $exchangeTransaction['ticketId'],
			'hostId'     => $exchangeTransaction['hostId'],
			'value'      => -$exchangeTransaction['value'],
			'currencyId' => $exchangeTransaction['currencyId'],
			'typeName'   => Webservice_TransactionType::NAME_USER_EXCHANGE_FROM_POINTS_CANCEL,
		));		
		
		Webservice_PointsTransaction::make(array(
			'userId'   => $exchangePointTransaction['userId'],
			'ticketId' => $exchangePointTransaction['ticketId'],
			'hostId'   => $exchangePointTransaction['hostId'],
			'value'    => -$exchangePointTransaction['value'],
			'typeId'   => static::POINTS_TRANSACTION_TYPE,
		));
		
		return true;
	}
}