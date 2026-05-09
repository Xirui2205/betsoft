<?php
class It6_Campaign_Get_UserActivation extends It6_Campaign_GetCampaign {

	const PARAM_AMOUNT = 'amount';
	const POINTS_TRANSACTION_TYPE = 6;

	public static function validate($crc) {
		return true;
	}

	public static function run($crc) {
		Webservice_PointsTransaction::make(array(
			'userId' => $crc['userId'],
			'value'  => static::getParameter(static::PARAM_AMOUNT),
			'typeId' => static::POINTS_TRANSACTION_TYPE));
		
		return true;
	}
}