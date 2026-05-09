<?php
class It6_Alert_RateChange extends It6_Alert_MailAbstract {

	const PARAM_LIMIT = 'limit';

	public static function check($params, $userId = null, $branchId = null) {
		if ( !empty($params['betId']) )
			$changes = Webservice_BetOdds::getRateChanges($params['betId']);
		else
			$changes = static::getRateChanges($params);
		$limit = static::getParameter(self::PARAM_LIMIT) / 100;

		foreach( $changes as $change )
			if ( $change['change'] >= $limit )
				return true;

		return false;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$ret['bookmaker'] = $params['bookmaker'];
		$ret['betId'] = $params['betId'];

		if ( !empty($params['betId']) )
			$tmp = Webservice_BetOdds::getRateChanges($params['betId'], !empty($params['oddsId']) ? $params['oddsId'] : null);
		else
			$tmp = static::getRateChanges($params);
		$changes = array();
		foreach ( $tmp as $id => $change) {
			$change['id'] = $id;
			$changes[] = $change;
		}
		$ret['changes'] = $changes;

		return $ret;
	}

	protected function getRateChanges($params) {
		$changes = array();
		for ( $i = 0; $i < count($params['oddsIds']); ++$i ) {
			$oddsId = $params['oddsIds'][$i];
			$changes[$oddsId] = array();
			$changes[$oddsId]['oldValue'] = $params['oldRates'][$i];
			$changes[$oddsId]['newValue'] = $params['newRates'][$i];
			$changes[$oddsId]['change']
				= abs(($params['oldRates'][$i] - $params['newRates'][$i]) / $params['oldRates'][$i]);
		}
		return $changes;
	}
}
