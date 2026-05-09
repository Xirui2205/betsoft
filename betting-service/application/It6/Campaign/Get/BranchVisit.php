<?php
class It6_Campaign_Get_BranchVisit extends It6_Campaign_GetCampaign { 
	const POINTS_TRANSACTION_TYPE = 5;
	const PARAM_POINTS = 'points';
	const VALUE_DELIMITER = ';';

	public static function validate($crc) {

		list($startTime, $endTime) = It6_Date::todayToDbInterval();
		
		$count = Webservice_PointsTransaction::getAllWhereCount(array(
			'typeId = ?' => static::POINTS_TRANSACTION_TYPE,
			'userId = ?' => $crc['userId'],
			'time >= ?' => $startTime
		));
		
		return $count == 0;
	}

	public static function run($crc) {

		$value = static::getValue($crc);
		
		Webservice_PointsTransaction::make(array(
			'userId'   => $crc['userId'],
			'ticketId' => $crc['ticketId'],
			'hostId'   => $crc['hostId'],
			'value'    => $value,
			'typeId'   => static::POINTS_TRANSACTION_TYPE));
		
		return true;
	}
	
	public static function getValue($crc) {
		
		$values = static::getParameter(static::PARAM_POINTS);
		
		$count = Webservice_PointsTransaction::getAllWhereCount(array(
					'typeId = ?' => static::POINTS_TRANSACTION_TYPE,
					'userId = ?' => $crc['userId']
		));
		
		++$count;
		
		$values = explode(static::VALUE_DELIMITER,$values);
		$value = 0;
		for ( $i = count($values)/2 - 1; $i >= 0 ; --$i ) {
			if ( $count % intval($values[2*$i]) == 0 ) {
				$value = intval($values[2*$i+1]);
				break;
			}
		}
		return $value;
	}
}