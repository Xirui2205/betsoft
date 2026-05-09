<?php
/**
 * CRC PARAMETERS : type, betCount, stake, rate, preferenceSize, userId, ticketId, hostId 
 */
class It6_Campaign_Spend_PreferenceRate extends It6_Campaign_SpendCampaign { 
	const POINTS_TRANSACTION_TYPE = 7;
	
	const PARAM_MAX_AMOUNT = 'maxAmount';
	const PARAM_MIN_COUNT = 'minCount';
	const PARAM_MIN_RATE = 'minRate';
	const PARAM_MAX_DAY_LIMIT = 'maxDayLimit';
	const PARAM_COST = 'cost';
	
	const COST_DELIMITER = ';';

	public static function validate($crc) {

		if ( !empty($crc['pointTicket']) )
			return false;
		
		if ( It6_Models_Ticket::TYPE_COMBI != $crc['type'] )
			return false;

		if ( empty($crc['userId']) )
			return false;

		$params = static::getParameter(array(
			static::PARAM_MAX_AMOUNT, static::PARAM_MIN_COUNT, static::PARAM_MIN_RATE,
			// just preloading:
			static::PARAM_MAX_DAY_LIMIT, static::PARAM_COST,
		));
		$maxAmount = $params[static::PARAM_MAX_AMOUNT];
		$minCount = $params[static::PARAM_MIN_COUNT];
		$minRate = $params[static::PARAM_MIN_RATE];

		$tmp = static::getLimits($crc['userId']);		
		
		$balance = $tmp['balance'];
		$daySum = $tmp['daySum'];
		$maxDayLimit = $tmp['maxDayLimit'];
		
		$cost = static::getCost($crc);
		
		if ( $cost > $balance )
			return false;		
		
		$count = $crc['betCount'];
		
		if(!empty($crc['rateAdvance']) && $crc['rateAdvance'] > 1) {
			$realTicketRate = $crc['rate'] / $crc['rateAdvance'];
		}
		else {
			$realTicketRate = $crc['rate'];
		}

		return $maxDayLimit >= -$daySum + $cost &&
				$maxAmount >= $crc['stake'] &&
				$minCount <= $count &&
				$minRate <= $realTicketRate;
	}
	
	public static function getLimits($userId) {
		
		$maxDayLimit = static::getParameter(static::PARAM_MAX_DAY_LIMIT);
		
		$db = Webservice_PointsTransaction::getMainDb();
		
		$type = Webservice_PointsTransactionType::getById(static::POINTS_TRANSACTION_TYPE);
		
		
		$balance = $db->select()
			->from(Webservice_PointsTransaction::$TABLE_POINT_ACCOUNT,array('balance' => 'balance'))
			->where('point_type_id = ?', $type->pointTypeId)
			->where('user_id = ?', $userId)
			->limit(1)
			->query()->fetch();
		$balance = $balance['balance'];
		
		
		list($startTime, $endTime) = It6_Date::todayToDbInterval();
		
		$daySum = $db->select()
			->from(Webservice_PointsTransaction::$TABLE, array('sum' => "SUM(value)"))
			->where(
				'type_id IN (?)', 
				array(
					static::POINTS_TRANSACTION_TYPE,
					It6_Campaign_Spend_PreferenceRateCancel::POINTS_TRANSACTION_TYPE
				)
			)
			->where('user_id = ?', $userId)
			->where('time >= ?', $startTime)
			->where('time < ?', $endTime)
			->query()->fetch();
		$daySum = empty($daySum['sum']) ? 0.0 : $daySum['sum'];
		
		return array('balance' => $balance, 'daySum' => $daySum, 'maxDayLimit' => $maxDayLimit);
	
	}

	public static function getPreferenceSizes() {
		$costs = static::getParameter(static::PARAM_COST);
		$costs = explode(static::COST_DELIMITER,$costs);
		$ret = array();
		for ( $i = 0; $i < count($costs)/2; ++$i ) {
			$ret[] = intval($costs[2*$i]);
		}
		return $ret;
	}
	
	public static function getValidPreferenceSizes($crc) {
		$ret = array();
		
		$preferenceSizes = static::getPreferenceSizes();
		
		foreach ( $preferenceSizes as $size ) {
			$ret[$size] = array();
			$ret[$size]['valid'] = static::validate(
				array(
					'preferenceSize' => $size,
					'type' => $crc['type'],
					'betCount' => $crc['betCount'],
					'stake' => $crc['stake'],
					'rate' => $crc['rate'],
					'userId' => $crc['userId'],
					'pointTicket' => $crc['pointTicket'],
					'rateAdvance' => $crc['rateAdvance']
				)
			);
			
			$ret[$size]['cost'] = static::getCost(array('preferenceSize' => $size));
		}
		return $ret;
	}

	public static function getCost($crc) {
		$costs = static::getParameter(static::PARAM_COST);
		$costs = explode(static::COST_DELIMITER,$costs);
		for ( $i = 0; $i < count($costs)/2; ++$i ) {
			if ( intval($crc['preferenceSize']) == intval($costs[2*$i]) ) {
				$cost = intval($costs[2*$i+1]);
				if ( $cost <= 0 )
					throw new Exception("Invalid preference cost");
				break;
			}
		}
		
		if ( empty($cost) )
			throw new Exception("Invalid preverence size: " . $crc['preferenceSize']);
				
		return $cost;
	}

	public static function run($crc) {

		Webservice_PointsTransaction::make(array(
			'userId'   => $crc['userId'],
			'ticketId' => $crc['ticketId'],
			'hostId' => $crc['hostId'],
			'value'    => -static::getCost($crc),
			'typeId'   => static::POINTS_TRANSACTION_TYPE));
		
		return true;
	}
}