<?php
class It6_Campaign_Get_CreateMoneyTicket extends It6_Campaign_GetCampaign { 
	const POINTS_TRANSACTION_TYPE = 4;
	
	const PARAM_MIN_AMOUNT = 'minAmount';
	const PARAM_MIN_COUNT = 'minCount';
	const PARAM_MIN_RATE = 'minRate';
	const PARAM_POINT_RATIO = 'pointRatio';
	const PARAM_POINT_OFFSET = 'pointOffset';
	const RATIO_DELIMITER = ';';

	public static function validate($crc) {		
		
		if ( !empty($crc['pointTicket']) )
			return false;
		
		if ( empty($crc['userId']) )
			return false;

		if ( empty($crc['combinations']) ) {
			$combs = array(array(
				'betCount' => $crc['betCount'],
				'rate' => $crc['rate'],
				'stake' => $crc['stake']
			));
		}
		else {
			$combs = $crc['combinations'];
		}
		
		
		$ret = false;
		foreach ( $combs as $comb ) {	
			$ret = static::_validateOne($comb);
			
			if ( $ret )
				break;
		}
		
		
		if ( $ret ) {
			$value = static::getValue($crc);
			return $value > 0;
		}
		else
			return false;
	}

	public static function validateOne($comb) {
		return static::_validateOne($comb);
	}

	protected static function _validateOne($comb) {
		$params = static::getParameter(array(
			static::PARAM_MIN_AMOUNT, static::PARAM_MIN_COUNT, static::PARAM_MIN_RATE
		));
		$minAmount = $params[static::PARAM_MIN_AMOUNT];
		$minCount = $params[static::PARAM_MIN_COUNT];
		$minRate = $params[static::PARAM_MIN_RATE];
		
		return $minAmount <= $comb['stake'] &&
			$minCount <= $comb['betCount'] &&
			$minRate <= $comb['rate'];
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
		
		if ( empty($crc['combinations']) ) {
			$combs = array(array(
				'betCount' => $crc['betCount'],
				'rate' => $crc['rate'],
				'stake' => $crc['stake']
			));
		}
		else {
			$combs = $crc['combinations'];
		}		
		
		$type = Webservice_PointsTransactionType::getById(static::POINTS_TRANSACTION_TYPE);
		$limit = Webservice_PointsTransaction::getValueLimits($crc['userId'], $type->pointTypeId);
		
		if ( $limit == 0 )
			return 0;
		
		$value = 0;		
		foreach ( $combs as $comb ) {
			if ( !static::_validateOne($comb) )
				continue;
			$ratio = static::getRatio($comb);
			$value += round(floor($comb['stake'] / 10) * $ratio);
		}
		
		return $limit < $value ? $limit : $value;
	}
	
	public static function getRatio($crc) {		
		$ratios = static::getParameter(static::PARAM_POINT_RATIO);
		$ratios = explode(static::RATIO_DELIMITER,$ratios);
		$ratio = 0;
		for ( $i = 0; $i < count($ratios)/2; ++$i ) {
			if ( $crc['rate'] < floatval($ratios[2*$i]) )
				break;
			 $ratio = floatval($ratios[2*$i+1]);
		}
		return $ratio;
		
	}
}