<?php
class It6_Campaign_Get_CancelMoneyTicket extends It6_Campaign_GetCampaign {
	const POINTS_TRANSACTION_TYPE = 12;

	public static function validate($crc) {

		$createTransaction = Webservice_PointsTransaction::getOneWhere(array(
			'ticketId = ?' => $crc['ticketId'],
			'typeId = ?' => It6_Campaign_Get_CreateMoneyTicket::POINTS_TRANSACTION_TYPE,
		));

		return !empty($createTransaction);
	}

	public static function run($crc) {
		
		$createTransaction = Webservice_PointsTransaction::getOneWhere(array(
			'ticketId = ?' => $crc['ticketId'],
			'typeId = ?' => It6_Campaign_Get_CreateMoneyTicket::POINTS_TRANSACTION_TYPE,
		));
		
		Webservice_PointsTransaction::make(array(
			'userId'   => $createTransaction['userId'],
			'ticketId' => $createTransaction['ticketId'],
			'hostId'   => $createTransaction['hostId'],
			'value'    => -$createTransaction['value'],
			'typeId'   => static::POINTS_TRANSACTION_TYPE
		));
		
		return true;
	}

}