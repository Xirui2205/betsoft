<?php
class It6_Campaign_Get_HappyHourVisit extends It6_Campaign_GetCampaign { 
	const POINTS_TRANSACTION_TYPE = 11;
	const HAPPY_HOUR_TYPE = 'visit';
	
	const PARAM_TIME_FROM = 'timeFrom';
	const PARAM_TIME_TO = 'timeTo';
	const PARAM_AMOUNT = 'amount';
	const PARAM_USERS_COUNT= 'usersCount';

	public static function validate($crc) {
		$hh = Webservice_HappyHour::getRunning(static::HAPPY_HOUR_TYPE);
		
		if ( false === $hh ) {
			return false;
		} 
						
		$where = array(
			'typeId = ?' => static::POINTS_TRANSACTION_TYPE,
			'userId = ?' => $crc['userId'],
			'time > ?' => $hh['timeFrom']
		);
		
		if ( !empty($hh['timetTo']) ) {
			$where['time < ?'] = $hh['timeTo'];
		}
		else {
			list($_,$to) = It6_Date::todayToDbInterval();
			$where['time < ?'] = $to;
		}
		
		$count = Webservice_PointsTransaction::getAllWhereCount($where);
		
		return $count == 0;
	}

	public static function run($crc) {

		$db = Webservice_HappyHour::getMainDb();
		
		$value = static::getValue($crc);
		
		It6_DbTransaction::begin($db);
		try {		
			Webservice_PointsTransaction::make(array(
				'userId'   => $crc['userId'],
				'hostId'   => It6_Models_Host::ID_INTERNET,
				'value'    => $value,
				'typeId'   => static::POINTS_TRANSACTION_TYPE));
			
			Webservice_HappyHour::useHappyHour(static::HAPPY_HOUR_TYPE);
			It6_DbTransaction::commit($db);
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			throw $e;
		}
		
		return true;
	}
	
	public static function getValue($crc) {
		$hh = Webservice_HappyHour::getRunning(static::HAPPY_HOUR_TYPE);
		return $hh['amount'];
	}
	
	public static function generate() {
		$now = It6_Date::dbNowAsDate();
		$params = static::getParameter(array(
			static::PARAM_TIME_FROM,
			static::PARAM_TIME_TO,
			static::PARAM_AMOUNT,
			static::PARAM_USERS_COUNT,
		));
		Webservice_HappyHour::generate(
			static::HAPPY_HOUR_TYPE,
			$now . ' ' . $params[static::PARAM_TIME_FROM],
			$now . ' ' . $params[static::PARAM_TIME_TO],
			null,
			$params[static::PARAM_AMOUNT],
			$params[static::PARAM_USERS_COUNT]
		);
	}
}