<?php
/**
 * CRC PARAMETERS : type, betCount, stake, rate, userId, ticketId, hostId 
 */
class It6_Campaign_Spend_CreatePointTicket extends It6_Campaign_SpendCampaign { 

	const POINTS_TRANSACTION_TYPE = 1;
	
	const PARAM_MIN_AMOUNT = 'minAmount';
	const PARAM_MIN_COUNT = 'minCount';
	const PARAM_MIN_RATE = 'minRate';

	public static function getMinCount() {
		return static::getParameter(static::PARAM_MIN_COUNT);
	}

	public static function getMinAmount() {
		return static::getParameter(static::PARAM_MIN_AMOUNT);
	}

	public static function validate($crc) {

		if ( It6_Models_Ticket::TYPE_COMBI != $crc['type'] )
			return false;

		$params = static::getParameter(array(
			static::PARAM_MIN_AMOUNT, static::PARAM_MIN_COUNT, static::PARAM_MIN_RATE
		));
		$minAmount = $params[static::PARAM_MIN_AMOUNT];
		$minCount = $params[static::PARAM_MIN_COUNT];
		$minRate = $params[static::PARAM_MIN_RATE];
		
		$count = $crc['betCount'];
		
		$rateCondition = 0;
		foreach ( $crc['rates'] as $rate ) {
			if ( $minRate <= $rate ) {
				++$rateCondition;
			}
		}
		$rateCondition = ($minCount <= $rateCondition);

		return $minAmount <= $crc['stakeInPoints'] &&
				$minCount <= $count &&
				$rateCondition;
	}

	public static function run($crc) {
		
		Webservice_PointsTransaction::make(array(
			'userId'   => $crc['userId'],
			'ticketId' => $crc['ticketId'],
			'hostId'   => $crc['hostId'],
			'value'    => -$crc['stakeInPoints'],
			'typeId'   => static::POINTS_TRANSACTION_TYPE
		));
		
		Webservice_Transaction::make(array(
			'userId'     => $crc['userId'],
			'ticketId'   => $crc['ticketId'],
			'hostId'     => $crc['hostId'],
			'value'      => $crc['stake'],
			'currencyId' => $crc['currencyId'],
			'typeName'   => Webservice_TransactionType::NAME_USER_EXCHANGE_FROM_POINTS
		));
		
		return true;
	}
}