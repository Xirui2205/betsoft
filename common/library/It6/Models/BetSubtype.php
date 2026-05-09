<?php

class It6_Models_BetSubtype extends It6_Models_DbDependent {

/**
 * Retrieves complementary columns data for given bet subtype(s).
 * @param integer|array $subtypeId One or list of bet subtype IDs
 * @param Zend_Db_Adapter $db
 * @return array One array if integer was passed or map of arrays (subtypeId => array)
 *               if list was passed is returned, where array is (columnId => list of complement column IDs).
 */
public static function getComplementColumns($subtypeId, &$db = null) {
	static $cache = array();
	static::assureDbParam($db);
	$more = is_array($subtypeId);
	if (!$more)
		$subtypeId = array($subtypeId);
	$result = array();
	$missing = array();
	foreach ($subtypeId as $id) {
		if (isset($cache[$id]))
			$result[$id] = $cache[$id];
		else
			$missing[$id] = $id;
	}
	if (!empty($missing)) {
		$rows = $db->select()->from(
				array('ps' => 'podtyp_sloupce'),
				array('subtypeId' => 'podtyp_id')
			)
			->join(
				array('psc' => 'podtyp_sloupec_complement'),
				'ps.sloupec_id=psc.sloupec_id',
				array(
					'columnId' => 'sloupec_id',
					'complementId' => 'complement_id',
				)
			)
			->where('ps.podtyp_id IN (?)', $missing)
			->query()
			->fetchAll();
		$found = array();
		foreach ($rows as $row)
			$found[ $row['subtypeId'] ][ $row['columnId'] ][] = $row['complementId'];
		foreach ($missing as $id) {
			$cols = (isset($found[$id]) ? $found[$id] : array());
			$result[$id] = $cols;
			$cache[$id] = $cols;
		}
	}
	if ($more)
		return $result;
	else {
		$subtypeId = $subtypeId[0];
		return (isset($result[$subtypeId]) ? $result[$subtypeId] : array());
	}
}

//TODO: test It6_Models_Bet::recomputeRates for data based on IDs not column names returned by functio nabove

/**
 * maxEventCount + maxEventCountParam : this feature prevents players to bet eg. three bets for first-to-second place result,
 * which occur very rarely (making this for player's good).
 * @param integer|array $subtypeId One or list of bet subtype IDs
 * @return array For one passed id map (columnId -> columnData), for list passed map (subtypeId -> columnId -> columnData),
 *               where columnData is structure with fields:
 *               <ul>
 *               <li>maxEventCount ... max.allowed count of bets of same column from one event on one ticket</li>
 *               <li>maxEventCountParam ... currently used for counting, counted are only bets with columns having param less or equal</li>
 *               </ul>
 *               Note that if there are no such data for particular subtype then columnData fields will be NULL,
 *               also map (columnId -> columnData) can be NULL if no subtype was found by given ID.
 */
public static function getValidationColumnData($subtypeId, &$db = null) {
	static $cache = array();
	$more = is_array($subtypeId);
	$ids = ($more ? $subtypeId : array($subtypeId));
	$result = array();
	$unknown = array();
	foreach ($ids as $id) {
		if (array_key_exists($id, $cache))
			$result[$id] = $cache[$id];
		else
			$unknown[$id] = $id;
	}
	if (!empty($unknown)) {
		static::assureDbParam($db);
		$rows = $db->select()
			->from('podtyp_sloupce', array(
				'subtypeId' => 'podtyp_id',
				'columnId' => 'sloupec_id',
				'maxEventCount' => 'max_event_count',
				'maxEventCountParam' => 'max_event_count_param',
			))
			->where('podtyp_id IN (?)', $unknown)
			->query()
			->fetchAll();
		$found = array();
		foreach ($rows as $row) {
			$id = $row['subtypeId'];
			$found[$id] = true;
			$cache[$id][ $row['columnId'] ] = array(
				'maxEventCount' => $row['maxEventCount'],
				'maxEventCountParam' => $row['maxEventCountParam'],
			);
		}
		foreach ($unknown as $id) {
			if (empty($found[$id])) {
				$cache[$id] = null;
				$result[$id] = null;
			}
			else
				$result[$id] = $cache[$id];
		}
	}
	return ($more ? $result : $result[$subtypeId]);
}

} // class
