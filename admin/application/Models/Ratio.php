<?php

class Models_Ratio{
	
	public static function getOtherRateByRatio($ratio, $rate, $rateNumberToGet) {

		if (!is_float((float)$rate))
			return false;

		$ratioColumnName = "rate" . $rateNumberToGet;
		$knownRateColumnName = "rate" . (($rateNumberToGet == 1) ? 2 : 1);

		$db = Zend_Registry::get('db');

		$select = $db->select()
						->from('two_column_ratio', array(
								"otherRate" => $ratioColumnName
							))
						->where('ratio = ?', $ratio)
						->where("$knownRateColumnName = ?", $rate);

		$res = $select->query()->fetch();
		//return $select->__toString();
		if (isset($res["otherRate"]))
			return $res["otherRate"];
		else return self::getOtherRateByRatio($ratio, $rate, (($rateNumberToGet == 1) ? 2 : 1));

	}

}