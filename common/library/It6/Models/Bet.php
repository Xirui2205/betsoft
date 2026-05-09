<?php

class It6_Models_Bet extends It6_Models_DbDependent {

	protected static $_cache = array();

	const STATUS_NEW = 0;
	const STATUS_CANCELED = 1;
	const STATUS_SUSPENDED = 2;
	const STATUS_EVALUATED = 3; // if column `overena` is nonzero, bet is evaluated and confirmed
	// if column `proplacena` is nonzero, bet is paid-out

	const STATUSEXT_BY_BETRADAR = 1;

	//TODO: modify to be cached like getResultName?
	public static function readBetRate($betId, $colId, $date, &$db = null) {
		static::assureDbParam($db);
		$row = $db->select()
		->from('sazka_pohled', array('kurz'))
		->where('sazka_id = ?', $betId)
		->where('sloupec_id = ?', $colId)
		->where('platny_od <= ?', $date)
		->order('platny_od DESC')
		->limit(1, 0)
		->query()->fetch();

		return (false === $row ? false : $row['kurz']);
	}

	//TODO: modify to be like getResultName
	public static function readBetText($betId, &$db = null) {
		static::assureDbParam($db);
		$row = $db->select()
		->from('sazky', array('text'))
		->where('sazka_id=?', $betId)
		->query()
		->fetch();
		return (empty($row['text']) ? '' : $row['text']);
	}

	/**
	 * @param $betId ID of bet which should be updated
	 * @param $delta Difference which should be added to current risk_limit_balance
	 * @return number of records updated
	 */
	public static function saveRiskLimitBalanceChange($betId, $delta, &$db = null) {
		static::assureDbParam($db);

		$ret = $db->update(
				'sazky',
				array( 'risk_limit_balance' => new Zend_Db_Expr('risk_limit_balance+' . $db->quote($delta, Zend_Db::FLOAT_TYPE)) ),
				array('sazka_id=?' => $betId)
		);

		$over = $db->select()
		->from('sazky',array('risk_limit','risk_limit_balance'))
		->where('sazka_id = ?', $betId)
		->query()->fetchObject();

		if ( $over->risk_limit <= $over->risk_limit_balance ) {
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
			It6_GlobalCache_Invalidator::invalidateSportsbookByBet($betId);
		}

		return $ret;
	}

	/**
	 * @param array $betIds set of bet IDs to be filtered
	 * @return array of bet IDs from $betIds that has 'simple' flag in DB
	 */
	public static function readSimpleBets(array $betIds, &$db = null) {
		static::assureDbParam($db);
		$result = array();
		if (!empty($betIds)) {
			$rows = $db->select()
			->from('sazky', array('sazka_id'))
			->where('sazka_id IN (?)', $betIds)
			->where('jednoducha=1')
			->query()
			->fetchAll();
			foreach ($rows as $row)
				$result[] = $row['sazka_id'];
		}
		return $result;
	}

	/**
	 * @param array $betIds set of bet IDs to be checked against correlations
	 * @return array of bet IDs from $betIds that has 'simple' flag in DB
	 */
	public static function readCorrelatedBets(array $betIds, &$db = null) {
		static::assureDbParam($db);
		$result = array();
		if (!empty($betIds)) {
			// check correlated bet IDs
			$rows = $db->select()
			->from(array('sazka_kombinace'), array('sazka1_id', 'sazka2_id'))
			->where('sazka1_id IN (?)', $betIds)
			->where('sazka2_id IN (?)', $betIds)
			->query()
			->fetchAll();
			foreach ($rows as $row) {
				if (!in_array($row['sazka1_id'], $result))
					$result[] = $row['sazka1_id'];
				if (!in_array($row['sazka2_id'], $result))
					$result[] = $row['sazka2_id'];
			}
			// check correlated bet types
			$rows = $db->select()
			->from(array('s' => 'sazky'), array('betId' => 'sazka_id', 'event' => 'udalost_id','type' => 'typ_id'))
			->join(
					array('k' => 'kombinace_druh'),
					'(s.udalost_id=k.udalost_id_1 AND s.typ_id=k.typ_id_1) OR (s.udalost_id=k.udalost_id_2 AND s.typ_id=k.typ_id_2)',
					array('type1' => 'typ_id_1', 'event1' => 'udalost_id_1', 'type2' => 'typ_id_2', 'event2' => 'udalost_id_2')
			)
			->where('s.sazka_id IN (?)', $betIds)
			->query()
			->fetchAll();
			$typeBets = array();
			$typeRels = array();
			foreach ($rows as $row) {
				$type = "{$row['event']}:{$row['type']}";
				if (!array_key_exists($type, $typeBets))
					$typeBets[$type] = array($row['betId']);
				else if (!in_array($row['betId'], $typeBets[$type]))
					$typeBets[$type][] = $row['betId'];

				$type1 = "{$row['event1']}:{$row['type1']}";
				$type2 = "{$row['event2']}:{$row['type2']}";
				if (array_key_exists($type1, $typeRels)) {
					if (!in_array($type2, $typeRels[$type1]))
						$typeRels[$type1][] = $type2;
				}
				else if (array_key_exists($type2, $typeRels)) {
					if (!in_array($type1, $typeRels[$type2]))
						$typeRels[$type2][] = $type1;
				}
				else
					$typeRels[$type1] = array($type2);
			}
			foreach ($typeRels as $type1 => $rels) {
				foreach ($rels as $type2) {
					if (!empty($typeBets[$type1]) && !empty($typeBets[$type2]))
						$result = array_merge($result, $typeBets[$type1], $typeBets[$type2]);
				}
			}
		}
		return $result;
	}

	/**
	 * @param array $betIds set of bet IDs to be checked against correlations
	 * @return array of bet IDs from $betIds that has 'simple' flag in DB
	 */
	public static function readRealCorrelatedBetsDetail($betId, $includeHidden = FALSE, &$db = null) {

		$result = array();
		if (!empty($betId)) {
			$rows = self::getCorrelatedBets($betId, $includeHidden, $db);
			/*
			 if (!empty($rows))
				$result[$betId] = array(
						'id' => $betId,
						'text' => '-',
						'alias' => '-',
						'kombinace_show' => '-');
			*/
			foreach ($rows as $row) {
				if (!in_array($row['sazka1_id'], $result) && $betId != $row['sazka1_id']) {
					$result[$row['sazka1_id']] = array(
							'id' => $row['sazka1_id'],
							'text' => $row['b1text'],
							'alias' => $row['b1alias'],
							'type' => $row['typ_1'],
							'kombinace_show' => $row['kombinace_show'],
							'update_platna_do' => $row['update_platna_do'],
					);
				}
				else if (!in_array($row['sazka2_id'], $result) && $betId != $row['sazka2_id']) {
					$result[$row['sazka2_id']] = array(
							'id' => $row['sazka2_id'],
							'text' => $row['b2text'],
							'alias' => $row['b2alias'],
							'type' => $row['typ_2'],
							'kombinace_show' => $row['kombinace_show'],
							'update_platna_do' => $row['update_platna_do'],
					);
				}
			}
		}
		return $result;
	}

	/**
	 * @param array $betIds set of bet IDs to be checked against correlations
	 * @return array of bet IDs from $betIds that has 'simple' flag in DB
	 */
	public static function readRealCorrelatedBets($betId, $mode = 'list', $includeHidden = false, &$db = null) {
		$result = array();
		if (!empty($betId)) {
			$rows = self::getCorrelatedBets($betId, $includeHidden);

			if (!empty($rows) && ($mode == 'list'))
				$result[] = $betId;

			foreach ($rows as $row) {
				$risk1 = $row['b1risk'] - $row['b1riskbal'];
				$risk2 = $row['b2risk'] - $row['b2riskbal'];
				if (!in_array($row['sazka1_id'], $result) && $betId != $row['sazka1_id']) {
					if ($risk1 > 0)
						$result[] = $row['sazka1_id'];
				}
				else if (!in_array($row['sazka2_id'], $result) && $betId != $row['sazka2_id']) {
					if ($risk2 > 0)
						$result[] = $row['sazka2_id'];
				}
			}
		}		
		if ($mode == 'list')
			return implode(',',$result);
		else
			return count($result);
	}

	public static function getCorrelatedBets($betId, $includeHidden = FALSE, &$db = null) {
		static::assureDbParam($db);
		$now = It6_Date::dbNow();
		$rows = $db->select()
		->from(array('sk' => 'sazka_kombinace'), array('sazka1_id', 'sazka2_id', 'kombinace_show', 'update_platna_do' ))
		->join(
				array('s1'=>'sazky'),
				's1.sazka_id = sk.sazka1_id AND s1.platna_od < "'.$now. '" ' .(($includeHidden==TRUE) ? '' : 'AND s1.platna_do > "'.$now.'" AND s1.status=0'),
				array('platna_od','platna_od','status', 'b1text' => 'text',  'typ_1' => 'typ_id', 'b1alias' => 'alias',  'b1risk' => 'risk_limit', 'b1riskbal' => 'risk_limit_balance')
		)
		->join(
				array('s2'=>'sazky'),
				's2.sazka_id = sk.sazka2_id AND s2.platna_od < "'.$now.'" ' . (($includeHidden==TRUE) ? '' : 'AND s2.platna_do > "'.$now.'" AND s2.status=0'),
				array('platna_od','platna_od','status', 'b2text' => 'text',  'typ_2' => 'typ_id', 'b2alias' => 'alias',  'b2risk' => 'risk_limit', 'b2riskbal' => 'risk_limit_balance')
		)
		->where('sk.sazka1_id = ? OR sk.sazka2_id = ?', $betId, $betId);

		if ($includeHidden==FALSE)
			$rows = $rows->where('sk.kombinace_show=1');

		$rows =$rows->query()->fetchAll();

		return $rows;
	}

	public static function getBetsByBetradarMatchId($betradarMatchId, &$db = null) {
		static::assureDbParam($db);
		$rows = $db->select()
		->from(array('sazky'), array('sazka_id'))
		->where('betradar_match_id=?',$betradarMatchId)
		->query()
		->fetchAll();

		$output = array();
		foreach ($rows as $row) {
			$output[] = $row['sazka_id'];
		}
		return $output;
	}

	public static function getBetsByBetradarMatchIdOrParentIds($betradarMatchId, $parentIds, &$db = null) {
		static::assureDbParam($db);
		$select = $db->select()
		->from(array('sazky'), array('sazka_id'))
		->where('betradar_match_id=?', $betradarMatchId);
		if (!empty($parentIds))
			$select->orWhere('parent_id IN (?)', $parentIds);
		$rows = $select->query()
		->fetchAll();

		$output = array();
		foreach ($rows as $row)
			$output[] = $row['sazka_id'];
		return $output;
	}

	/**
	 * @param int $subTypeId podtyp ID
	 * @return boolean|array() result textual reprentation(s)
	 */
	public static function getColumnsAndNames($subTypeId, &$db = null) {
		static::assureDbParam($db);

		if (!empty($subTypeId)) {
			$rows = $db->select()
			->from('podtyp_sloupce', array('sloupec_id', 'nazev'))
			->where('podtyp_id = ?', $subTypeId)
			->query()
			->fetchAll();
			return $rows;
		} else return false;
	}

	/**
	 * @param int|array $subTypeId podtyp ID
	 * @return struct
	 * <ul>
	 * <li>podtyp_id
	 */
	public static function getSubtypesAndColumns($subTypeId, &$db = null) {
		static::assureDbParam($db);

		$rows = $db->select()
		->from(
				array('ps' => 'podtyp_sloupce'),
				array('sloupec_id', 'nazev', 'podtyp_id')
		)
		->join(
				array('p' => 'podtyp'),
				'ps.podtyp_id = p.podtyp_id',
				array('interni_nazev')
		)
		->where('p.podtyp_id IN (?)', $subTypeId)
		->query()->fetchAll();

		$res = array();
		foreach($rows as $row) {
			$res[$row['podtyp_id']]['podtyp_id'] = $row['podtyp_id'];
			$res[$row['podtyp_id']]['interni_nazev'] = $row['interni_nazev'];
			$res[$row['podtyp_id']]['columns'][$row['sloupec_id']] = $row['nazev'];
		}

		return $res;
	}

	/**
	 * @param int|array $columnId result (column) ID
	 * @return string|array() result textual reprentation(s)
	 */
	public static function getResultName($columnId, &$db = null) {
		static $cache = array();
		static::assureDbParam($db);
		$more = is_array($columnId);
		if (!$more)
			$columnId = array($columnId);
		$unknown = array();
		foreach ($columnId as $id) {
			if (!empty($id) && !array_key_exists($id, $cache) && !in_array($id, $unknown))
				$unknown[] = $id;
		}
		if (!empty($unknown)) {
			$rows = $db->select()
			->from('podtyp_sloupce', array('sloupec_id', 'nazev'))
			->where('sloupec_id IN (?)', $unknown)
			->query()
			->fetchAll();

			foreach ($rows as $row)
				$cache[$row['sloupec_id']] = $row['nazev'];
		}
		if ($more) {
			$texts = array();
			foreach ($columnId as $id) {
				if (!empty($id))
					$texts[$id] = $cache[$id];
			}
			return $texts;
		}
		else
			return $cache[$columnId[0]];
	}

	/**
	 * @param integer|array $betId One or more bet IDs
	 * @param Zend_Db_Adapter $db
	 * @return array Map (column ID => risk data) if scalar $betId was passed
	 *               or map (bet ID => column ID => risk data) if array $betId waas passed.
	 *               Map(s) (column ID => risk data) can be empty.
	 *               Risk data fields:
	 *               <ul>
	 *               <li>id ... column ID</li>
	 *               <li>name ... column name</li>
	 *               <li>riskAmount ... risk amount for given column and bet</li>
	 *               </ul>
	 */
	public static function readBetColumnRiskAmounts($betId, &$db = null) {
		/*
		 static $cache = array(); // cached helpers
		static::assureDbParam($db);
		// get all tickets that have given bet
		$tickets = $db->select()
		->from('ticket_pohled', array('ticket_id', 'castka'))
		->where('sazka_id=?', $betId)
		->where('zruseno=0')
		->group('ticket_id')
		->query()
		->fetchAll();
		$ticketIds = array();
		$unknownIds = array();
		foreach ($tickets as $ticket) {
		$ticketId = $ticket['ticket_id'];
		$ticketIds[] = $ticketId;
		if (!array_key_exists($ticketId, $cache))
			$unknownIds[] = $ticketId;
		}
		// cache new helper instances
		if (!empty($unknownIds)) {
		$helpers = It6_Models_TicketFactory::newTicket($unknownIds, It6_Models_Ticket::DATA_ADMIN_TICKET, true, true, $db);
		foreach ($helpers as $ticketId => &$helper) {
		$cache[$ticketId] = $helper;
		}
		}
		// compute risk amount for each column from all tickets with given bet
		$columnRiskAmounts = array();
		foreach ($ticketIds as $ticketId) {
		if (!array_key_exists($ticketId, $cache))
			continue; // error, maybe?
		$helper = &$cache[$ticketId];
		$bets = $helper->getBet($betId, null);
		foreach ($bets as $bet) {
		$columnId = $bet['column'];
		if (!array_key_exists($columnId, $columnRiskAmounts))
			$columnRiskAmounts[$columnId] = $bet['riskAmount'];
		else
			$columnRiskAmounts[$columnId] += $bet['riskAmount'];
		}
		}
		// get all possible columns for given betId
		$columns = $db->select()
		->from('sazka_pohled', array('sloupec_id', 'nazev'))
		->where('sazka_id=?', $betId)
		->group('sloupec_id')
		->query()
		->fetchAll();
		$result = array();
		foreach ($columns as $column) {
		$columnId = $column['sloupec_id'];
		$result[$columnId] = array(
				'id' => $columnId,
				'name' => $column['nazev'],
				'riskAmount' => round(array_key_exists($columnId, $columnRiskAmounts) ? $columnRiskAmounts[$columnId] : 0, 2),
		);
		}
		return $result;
		*/
		// this implementation assumes that all column have its resord in DB table bet_column
		static::assureDbParam($db);
		$rows = $db->select()->from(array('s' => 'sazky'), array('betId' => 'sazka_id'))
			->join(
				array('pts' => 'podtyp_sloupce'),
				's.podtyp_id=pts.podtyp_id',
				array('columnId' => 'sloupec_id', 'name' => 'nazev')
			)
			->joinLeft(
				array('bc' => 'bet_column'),
				'bc.sazka_id=s.sazka_id AND bc.sloupec_id=pts.sloupec_id',
				array('riskLimitBalance' => 'risk_limit_balance')
			)
			->where('s.sazka_id IN (?)', $betId)
			->query()
			->fetchAll();
		$result = array();
		foreach ($rows as $row) {
			$columnId = $row['columnId'];
			$result[$row['betId']][$columnId] = array(
				'id' => $columnId,
				'name' => $row['name'],
				'riskAmount' => (isset($row['riskLimitBalance']) ? round($row['riskLimitBalance'], 2) : 0.0),
			);
		}
		if (It6_ArrayWrapper::isArray($betId)) {
			foreach ($betId as $id) {
				if (!isset($result[$id])) {
					$result[$id] = array();
				}
			}
		}
		else {
			if (empty($result[$betId])) {
				$result = array();
			}
		}
		return $result;
	}

	/**
	 * Retrieves entended data of bets for ticket validation.
	 * @param array $betIds List of bet IDs
	 * @param Zend_Db_Adapter $db
	 * @return array Associative array (betId -> {ako, eventId, subtypeId})
	 */
	public static function readBetsValidationData(array $betIds, &$db = null) {
		static::assureDbParam($db);
		$result = array();
		if (!empty($betIds)) {
			$rows = $db->select()
				->from('sazky', array('sazka_id', 'ako', 'udalost_id', 'podtyp_id'))
				->where('sazka_id IN (?)', $betIds)
				->query()
				->fetchAll();
			$subtypeIds = array();
			foreach ($rows as $row) {
				$subtypeId = $row['podtyp_id'];
				$subtypeIds[$subtypeId] = $subtypeId;
				$result[$row['sazka_id']] = array(
					'ako' => $row['ako'],
					'eventId' => $row['udalost_id'],
					'subtypeId' => $row['podtyp_id'],
				);
			}
		}
		return $result;
	}

	/**
	 * Finds event IDs for given bet(s)
	 * @param integer|array $betId One or more bet IDs
	 * @return NULL|integer|array One or more event IDs, type of returned value matches
	 *                       type of passed $betId, array will be map (bet ID => event ID).
	 *                       If bet is not found, NULL will be returned as event ID.
	 */
	public static function readBetEventId($betId, &$db = null) {
		static::assureDbParam($db);
		$rows = $db->select()
			->from('sazky', array(
				'betId' => 'sazka_id',
				'eventId' => 'udalost_id',
			))
			->where('sazka_id IN (?)', $betId)
			->query()
			->fetchAll();
		if (is_array($betId)) {
			$map = array();
			foreach ($rows as $row) {
				$map[$row['betId']] = $row['eventId'];
			}
			foreach ($betId as $_betId) {
				if (array_key_exists($_betId, $map)) {
					$map[$_betId] = null;
				}
			}
			return $map;
		}
		else {
			return (empty($rows) ? null : $rows[0]['eventId']);
		}
	}

	/**
	 * Formats bet alias to common format ("NNNN/NN")
	 * @param integer $betAlias
	 * @param integer $typeAlias
	 * @return string
	 */
	public static function formatAlias($betAlias, $typeAlias) {
		return str_pad(intval($betAlias), 4, '0', STR_PAD_LEFT) . '/'
		. str_pad(intval($typeAlias), 2, '0', STR_PAD_LEFT);
	}

	public static function getDefaultTextNote($typeId, $eventId, &$db = null) {
		static $cache = array();
		if (array_key_exists($typeId, $cache) && array_key_exists($eventId, $cache))
			return $cache[$typeId][$eventId];
		else {
			static::assureDbParam($db);
			$rows = $db->select()->from(array('n' => 'typ_sport_note'), array('typeId' => 'typ_id', 'note' => 'text_note'))
			->join(array('e' => 'udalost'), 'e.sport_id=n.sport_id', array('eventId' => 'udalost_id'))
			->where('n.typ_id=?', $typeId)
			->where('e.udalost_id=?', $eventId)
			->query()
			->fetchAll();
			$note = (empty($rows) ? null : $rows[0]['note']);
			$cache[$typeId][$eventId] = $note;
			return $note;
		}
	}

	/**
	 * Fixes rate to be greater or equal to 1.0, rounds to use three significant digits (eg. 1.23 12.3 123)
	 * @param float $rate
	 * @return float
	 */
	public static function roundRate($rate) {
		if ($rate <= 1.0)
			return 1.0;
		else if ($rate < 10)
			return round($rate, 2);
		else if ($rate < 100)
			return round($rate, 1);
		else
			return round($rate);
	}

	/**
	 * Computes new rates from given rates and if desired also from given win ratio.
	 * Also can computes complementary rates (eg. rates for results "all others" like 1X 12 2X from 1 X 2)
	 * Basic formulas (W = winRatio, r = rate, p = probability): W = sum(1/r), r = 1/(W*p), r' = r * W/W'
	 * @param array $rates (name => rate) eg. ('1' => 1.2, 'X' => 3.55, '2' => 2.55)
	 * @param float $winRatio Desired new win ratio
	 * @param boolean|array $addComplementary If TRUE, for each rate will be added its "all others" complementary rate (eg. X2 for 1 from 1X2 etc.) into result.
	 *                                        If array with map (complementaryName => name), only "all others" complementary rates for rates
	 *                                        identified by "name" will be computed and in result will be with key "complementaryName".
	 *                                        If array with map (complementaryName => array(name1, name2, ...)), new complementary rates will be
	 *                                        computed as combination of "some others" eg. for ('1X' => array('1', 'X')) new rate for "1 or X" result
	 *                                        will be added into result with key '1X'.
	 * @return array (name => rate)
	 */
	public static function recomputeRates($rates, $winRatio = null, $addComplementary = false) {
		$ratesWinRatio = self::computeWinRatio($rates);
		if (empty($winRatio)) {
			$newRates = $rates;
			$winRatio = $ratesWinRatio;
		}
		else {
			$newRates = array();
			foreach ($rates as $name => $rate)
				$newRates[$name] = $rate * $ratesWinRatio / $winRatio;
		}
		foreach ($newRates as &$rate)
			$rate = static::roundRate($rate);
		unset($rate);
		if (!empty($addComplementary)) {
			$addRates = array();
			if (!is_array($addComplementary)) {
				$addComplementary = array();
				foreach ($newRates as $name => $odd) {
					$others = array_filter(array_keys($newRates), function($v) use ($name) {
						return ($v != $name);
					});
					$addComplementary[implode($others)] = $name;
				}
			}
			foreach ($addComplementary as $newName => $name) {
				if (is_array($name)) {
					$sumInv = 0;
					foreach ($name as $_name)
						$sumInv += (1 / $newRates[$_name]);
					$rate = (1 / $sumInv);
				}
				else {
					$rate = $newRates[$name];
					$rate = $rate / ($winRatio * $rate - 1);
				}
				$rate = static::roundRate($rate);
				if ($rate < 1.0)
					$rate = 1.0;
				$addRates[$newName] = $rate;
			}
			$newRates += $addRates;
		}
		return $newRates;
	}

	/**
	 * Modifies bet history for given user.
	 * @param integer $userId
	 * @param integer|array $betId One or list of bet IDs (list is useful when adding same history to more bets)
	 * @param struct $historyUpdate Fields (all required):
	 *                              <ul>
	 *                              <li>ticketCount ... integer; How many tickets user made</li>
	 *                              <li>stakeBalance ... float; Total amount that will be added to current stake balance</li>
	 *                              </ul>
	 * @param boolean $revert TRUE if history change is being reverted, FALSE otherwise. FALSE is default.
	 * @param Zend_Db_Adapter $db
	 * @return integer Number of rows updated
	 */
	public static function changeUserHistory($userId, $betId, $history, $revert = false, &$db = null) {
		static::assureDbParam($db);
		$sign  = ($revert ? '-' : '+');
		$count = abs(intval($history['ticketCount']));
		$stake = abs(floatval($history['stakeBalance']));

		$rows = $db->select()
		->from('bet_user_history', array('betId' => 'sazka_id'))
		->where('user_id=?', $userId)
		->where('sazka_id IN (?)', $betId)
		->query()
		->fetchAll();
		$existing = array();
		foreach ($rows as $row) {
			$_betId = $row['betId'];
			$existing[$betId] = $_betId;
		}
		$n = 0;
		$betIds = (is_array($betId) ? $betId : array($betId));
		foreach ($betIds as $_betId) {
			if (isset($existing[$_betId])) {
				$n += $db->update(
						'bet_user_history',
						array(
								'ticket_count' => new Zend_Db_Expr("ticket_count $sign $count"),
								'stake_balance' => new Zend_Db_Expr("stake_balance $sign $stake"),
						),
						array(
								'user_id=?' => $userId,
								'sazka_id IN (?)' => $_betId,
						)
				);
			}
			else {
				$n += $db->insert(
						'bet_user_history',
						array(
								'user_id' => $userId,
								'sazka_id' => $_betId,
								'ticket_count' => $count,
								'stake_balance' => $stake,
						)
				);
			}
		}
		return $n;
	}

	/**
	 * Fetches bet user history for given bet(s) and given user(s).
	 * @param integer|array $betId One or list of bet IDs
	 * @param integer|array $userId One or list of user IDs
	 * @param Zend_Db_Adapter $db
	 * @return struct|array|NULL One or array(s) of history structs.
	 *                           History struct fields: ticketCount, stakeBalance.
	 *                           If bet ID list or user ID list were used, tree with history structs will be returned:
	 *                              <ul>
	 *                              <li>betId and userId both integer ... history_struct|NULL</li>
	 *                              <li>betId and/or userId array ... (userId => betId => history_struct)</li>
	 *                              </ul>
	 *                           Returned array contain only records found.
	 */
	public static function getUserHistory($userId, $betId, &$db = null) {
		static::assureDbParam($db);
		$rows = $db->select()->from('bet_user_history',	array(
				'betId' => 'sazka_id',
				'userId' => 'user_id',
				'ticketCount' => 'ticket_count',
				'stakeBalance' => 'stake_balance',
		))
		->where('sazka_id IN (?)', $betId)
		->where('user_id IN (?)', $userId)
		->query()
		->fetchAll();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['userId']][$row['betId']] = array(
					'ticketCount' => $row['ticketCount'],
					'stakeBalance' => $row['stakeBalance'],
			);
		}
		if (is_array($userId) || is_array($betId))
			return $result;
		else
			return (isset($result[$userId][$betId]) ? $result[$userId][$betId] : null);
	}

	/**
	 * Fetch bet count in last five minutes.
	 * @param integer|array $betId One or list of bet IDs
	 * @param integer|array $userId One or list of user IDs
	 * @param Zend_Db_Adapter $db
	 * @return string of count
	 */
	public static function getCountLastFiveMinuts($userId, $betId, &$db = null) {
		static::assureDbParam($db);
		$select = $db->select()
			->from(
				array('tk' => 'vic_main.ticket_kurz'),
				array('count' => 'COUNT(*)')
			)
			->join(
				array('t' => 'vic_main.ticket'),
				't.ticket_id = tk.ticket_id',
				null
			)
			->where('tk.sazka_id = ?', $betId)
			->where('t.user_id = ?', $userId)
			->where('t.zalozen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)')
			->limit(1)
			->query()->fetch();

		return $select;
	}

	/**
	 * Get all bet odd types for which there are active bets
	 * @param array $typeIds array of type ids to limit the search by
	 * @return struct of the odd types
	 */
	public static function getActiveOddTypes($typeIds=null, $eventIds=null) {
		static::assureDbParam($db);
		$oddTypes = array();


		$where = "s.status != 3	AND bc.bet_count > 0";
		if(!empty($typeIds))
			$where .= $db->quoteInto(" AND s.typ_id IN (?)", $typeIds);
		if(!empty($eventIds))
			$where .= $db->quoteInto(" AND s.udalost_id IN (?)", $eventIds);

		$oddTypes = $db->query(
			"SELECT
				s.podtyp_id,
				s.typ_id,
				COALESCE(p1.text, pt.interni_nazev) AS oddTypeName,
				COALESCE(p2.text, t.nazev) AS typeName
			FROM sazky s
			JOIN podtyp pt
				ON pt.podtyp_id = s.podtyp_id
			JOIN typ t
				ON t.typ_id = s.typ_id
			JOIN bet_column bc
				ON bc.sazka_id = s.sazka_id
			LEFT JOIN preklady p1
				ON pt.interni_nazev = p1.index_pole AND p1.lang_id=1
			LEFT JOIN preklady p2
				ON t.nazev = p2.index_pole AND p2.lang_id=1
			WHERE
				".$where."
			GROUP BY pt.podtyp_id;"
		);
		
		$ret = array();
		while($row = $oddTypes->fetch()) {
			$ret[$row['podtyp_id']] = array(
				'oddTypeId' => $row['podtyp_id'],
				'name' => $row['oddTypeName']
			);
		}

		return $ret;
	}

/**
 * Just computes win ratio from given rates.
 * @param array $rates Values are all main rates (disjunctive column)
 * @return float Win ratio (not rounded)
 */
public static function computeWinRatio($rates) {
	return array_reduce($rates, function ($acc, $v) {
		return $acc + 1/$v;
	}, 0);
}

/**
 * Computes next rate to given rates so that all together be giving desired win ratio.
 * @param array $rates Values are rates (one rate missing to complete column set)
 * @param float $winRatio Desired win ratio
 */
public static function computeLastRateFromWinRatio($rates, $winRatio) {
	$invSum = array_reduce($rates, function ($acc, $v) {
		return $acc + 1/$v;
	}, 0);
	return self::roundRate(1 / ($winRatio - $invSum));
}

/**
 * Gets rates dependency info for given sport and bet type. 
 * @param integer $sportId ID of sport
 * @param integer $eventId ID of event
 * @param integer $betTypeId ID of bet type with (possibly) dependent rates
 * @return struct|boolean FALSE if there is no dependency,
 *                        otherwise following structure:
 *                        <ul>
 *                          <dt>betType</dt>
 *                          <dd>integer, ID of bet type that has rates for calculation of new rates</dd>
 *                          <dt>function</dt>
 *                          <dd>string, name of function for new rates calculation</dd>
 *                        </ul>
 */
public static function getRateDependencyForBetType($sportId, $eventId, $betTypeId) {
	// map (sport ID or 0 => event ID or 0 => dependent type ID => dependency data)
	// each ID should be present just once in the event data
	static $dependencies = array(
		0 => array( // default
			0 => array( //TODO: remove this testing record
				// winner -> match ft
				19 => array('betType' => 22, 'function' => 'table_basketball_ft'),
			),
		),
		1003 => array( // tennis
			0 => array(
				// match -> 1st set
				49 => array('betType' => 19, 'function' => 'table_tennis_set')
			),
		),
		1006 => array( // basketball
			0 => array(
				// winner -> match ft
				19 => array('betType' => 22, 'function' => 'table_basketball_ft'),
			),
			115 => array( // nba
				// winner -> match ft
				19 => array('betType' => 22, 'function' => 'table_basketball_ft_nba'),
			),
		),
		1013 => array( // volleyball
			0 => array(
				// match -> 1st set
				49 => array('betType' => 19, 'function' => 'table_volleyball_set')
			),
		),
	);

	$sportKey = (empty($dependencies[$sportId]) ? 0 : $sportId);
	if (empty($dependencies[$sportKey])) {
		return false;
	}
	$eventKey = (empty($dependencies[$sportKey][$eventId]) ? 0 : $eventId);
	if (empty($dependencies[$sportKey][$eventKey])) {
		return false;
	}
	if (empty($dependencies[$sportKey][$eventKey][$betTypeId])) {
		return false;
	}
	return $dependencies[$sportKey][$eventKey][$betTypeId];
}

/**
 * This method computes rates of one bet type from rates
 * of another bet type.
 * @param string $function String ID of rates calculation function  
 * @param array $mainRates Original rates (column ID => rate)
 * @param float|NULL $mainWinRate Original win rate, if NULL it is computed   
 * @param float|NULL $dependentWinRate Target win rate, if NULL then same as $mainWinRate is used
 * @param integer|NULL $newBetSubtypeId Bet subtype ID if newly computed rates are using other columns, NULL otherwise
 * @return array|boolean Calculated dependent rates (column ID => rate)
 *                       or FALSE if calculation was not possible
 */
public static function calculateDependentRates($function, $mainRates, $mainWinRate, $dependentWinRate, &$newBetSubtypeId) {
	// map (function string ID => callback)
	// callback must take three parameters: mainRates, mainWinRate, dependentWinRate
	static $functions = array(
		'table_tennis_set' => array('It6_Models_Bet', 'calculateDependentRatesTennisSet'),
		'table_volleyball_set' => array('It6_Models_Bet', 'calculateDependentRatesVolleyballSet'),
		'table_basketball_ft' => array('It6_Models_Bet', 'calculateDependentRatesBasketballFtDefault'),
		'table_basketball_ft_nba' => array('It6_Models_Bet', 'calculateDependentRatesBasketballFtNba'),
	);
	if (!isset($mainWinRate)) {
		$mainWinRate = self::computeWinRatio($mainRates);
	}
	if (!isset($dependentWinRate)) {
		$dependentWinRate = $mainWinRate;
	}
	if (isset($functions[$function])) {
		return call_user_func($functions[$function], $mainRates, $mainWinRate, $dependentWinRate, $newBetSubtypeId);
	}
	else {
		return false;
	}
}

/**
 * 
 * @param float $rate X value for which Y value should be searched in table
 * @param array $table (main rate (float as string) =&gt; float dependent rate | array function description).
 *                     Favorite rate is coupled with table value from maximum table main rate less or equal to rate.
 *                     Favorite rate that is higher than highest main rate in table or less than lowest
 *                     main rate in table is considered not calculable.
 *                     Outsider rate is calculated using calculated favorite rate and passed win ratio.
 *                     Function description is array (func name[, func dependent params ...]),
 *                     supported functions:
 *                     <dl>
 *                       <dd>'+'</dd>
 *                       <dl>
 *                         gives main rate plus constant from parameter, eg. ('+', 0.05)
 *                         is <em>f(r) = r + 0.05</em>
 *                       </dl>
 *                     <dl>
 * @param boolean $toInfinity If table ends with last entry (returning FALSE for X past last enrty)
 *                            or if it is endless
 * @return float|boolean Found Y value or FALSE if not found 
 */
private static function searchRateTable($rate, $table, $toInfinity) {
	$tableXs = array_map(function($i) { return floatval($i); }, array_keys($table));
	sort($tableXs, SORT_NUMERIC);
	$tableSize = count($tableXs);
	if ($rate < $tableXs[0] || $rate > $tableXs[$tableSize - 1]) {
		return false;
	}
	$tableX1 = false;
	for ($i = 0; $i < $tableSize; ++$i) {
		if ($tableXs[$i] == $rate) {
			$tableX1 = $tableXs[$i];
			break;
		}
		else if ($tableXs[$i] > $rate) {
			$tableX1 = $tableXs[$i - 1];
			break;
		}
	}
	if (false === $tableX1) {
		$tableX1 = $tableXs[$tableSize - 1];
	}
	$fnY = function($x, $yDesc) {
		// yDesc is array: (string operation name[, param1[, param2[, ...]]])
		$fn = $yDesc[0];
		switch ($fn) {
			case '+': // y = x + param_1
				return It6_Models_Bet::roundRate($x + floatval($yDesc[1]));
			default:
				return false;
		}
	};
	$tableY1 = $table["$tableX1"];
	if (is_array($tableY1)) {
		$tableY1 = $fnY($tableX1, $tableY1);
	}
	return $tableY1;
}

/**
 * Helper function for two-way rates where original win rate doesn't matter and calculation is from value table
 * @see It6_Models_Bet::searchRateTable()
 * @param array $table
 * @param array $mainRates (column ID => rate), must be two-way type, so exactly two rates in array
 * @param float $dependentWinRate Target win ratio
 * @param integer $favoriteColumnId If new rates were calculated, this parameter will receive
 *                                  actual column ID of favorite 
 * @return array|boolean
 */
private static function getDependentRatesTwoWayFromTable($table, $mainRates, $dependentWinRate, &$favoriteColumnId = null) {
	if (2 != count($mainRates) || empty($table)) {
		return false;
	}
	$columns = array_keys($mainRates);
	if ($mainRates[$columns[0]] <= $mainRates[$columns[1]]) {
		$favoriteColumn = $columns[0];
		$outsiderColumn = $columns[1];
	}
	else {
		$favoriteColumn = $columns[1];
		$outsiderColumn = $columns[0];
	}
	$favoriteRate = $mainRates[$favoriteColumn];
	$outsiderRate = $mainRates[$outsiderColumn];
	$newFavoriteRate = self::searchRateTable($favoriteRate, $table, false);
	if (false === $newFavoriteRate) {
		return false;
	}
	$favoriteColumnId = $favoriteColumn;
	$newRates = $mainRates;
	$newRates[$favoriteColumn] = $newFavoriteRate;
	$newRates[$outsiderColumn] = self::computeLastRateFromWinRatio(array($newFavoriteRate), $dependentWinRate);
	return $newRates;
}

/**
 * Expects two-way rates, main win rate doesn't matter
 * @see It6_Models_Bet::calculateDependentRates()
 * @param array $mainRates
 * @param float $mainWinRate
 * @param float $dependentWinRate
 * @param NULL $newBetSubtypeId
 * @return array|boolean
 */
private static function calculateDependentRatesTennisSet($mainRates, $mainWinRate, $dependentWinRate, &$newBetSubtypeId) {
	static $table = array(
		'1.00' => 1.01,
		'1.01' => 1.02,
		'1.02' => 1.04,
		'1.03' => 1.06,
		'1.04' => 1.08,
		'1.05' => array('+', 0.05),
		'1.24' => array('+', 0.06),
		'1.28' => array('+', 0.07),
		'1.33' => array('+', 0.08),
		'1.38' => array('+', 0.09),
		'1.41' => array('+', 0.10),
		'1.70' => 1.80,
		'1.76' => 1.83,
		'1.81' => 1.85,
	);
	$newBetSubtypeId = null;
	return self::getDependentRatesTwoWayFromTable($table, $mainRates, $dependentWinRate);
}

/**
 * Expects two-way rates, main win rate doesn't matter
 * @see It6_Models_Bet::calculateDependentRates()
 * @param array $mainRates
 * @param float $mainWinRate Ignored
 * @param float $dependentWinRate
 * @param NULL $newBetSubtypeId
 * @return array|boolean
 */
private static function calculateDependentRatesVolleyballSet($mainRates, $mainWinRate, $dependentWinRate, &$newBetSubtypeId) {
	static $table = array(
		'1.00' => 1.01,
		'1.01' => 1.02,
		'1.02' => 1.04,
		'1.03' => 1.06,
		'1.04' => 1.08,
		'1.05' => array('+', 0.05),
		'1.31' => array('+', 0.08),
		'1.41' => array('+', 0.10),
		'1.66' => 1.76,
		'1.69' => 1.77,
		'1.73' => 1.78,
		'1.77' => 1.79,
		'1.89' => 1.80,
	);
	$newBetSubtypeId = null;
	return self::getDependentRatesTwoWayFromTable($table, $mainRates, $dependentWinRate);
}

/**
 * Expects two way rates, win rates doesn't matter
 * @see It6_Models_Bet::calculateDependentRates()
 * @param array $mainRates
 * @param float $mainWinRate Ignored
 * @param float $dependentWinRate Ignored
 * @param integer $favoriteColumnId If new rates were calculated, this parameter will receive
 *                                  actual column ID of favorite 
 * @return float|boolean Rate for favorite to be used in three-way match or FALSE if not calculable
 */
private static function getBasketballThreeWayFavoriteRateFromTwoWay($mainRates, $mainWinRate, $dependentWinRate, &$favoriteColumnId = null) {
	// favorite in 12 => favorite in 1X2
	static $table = array(
		'1.00' => 1.01,
		'1.01' => 1.02,
		'1.02' => 1.03,
		'1.03' => 1.04,
		'1.04' => 1.06,
		'1.05' => 1.08,
		'1.06' => 1.10,
		'1.07' => 1.11,
		'1.08' => 1.12,
		'1.09' => 1.13,
		'1.10' => 1.14,
		'1.11' => 1.15,
		'1.12' => 1.17,
		'1.13' => 1.18,
		'1.14' => 1.19,
		'1.15' => array('+', 0.05),
		'1.40' => array('+', 0.06),
		'1.49' => array('+', 0.07),
		'1.58' => array('+', 0.08),
		'1.61' => array('+', 0.10),
		'100.00' => array('+', 0.10),
	);
	$newRates = self::getDependentRatesTwoWayFromTable($table, $mainRates, $dependentWinRate, $favoriteColumnId);
	return (false === $newRates ? false : $newRates[$favoriteColumnId]);
}

/**
 * Expects two way rates, main win rate doesn't matter
 * @see It6_Models_Bet::calculateDependentRates()
 * @see It6_Models_Bet::searchRateTable()
 * @param array $table (favorite rate -> draw rate) table
 * @param array $mainRates
 * @param float $mainWinRate Ignored
 * @param float $dependentWinRate
 * @param integer|NULL $newBetSubtypeId
 * @return array|boolean Will always return 1X2/1X12X2 six-column bet subtype so six rates in array
 */
private static function getDependentRatesBasketballFtFromTable($table, $mainRates, $mainWinRate, $dependentWinRate, &$newBetSubtypeId) {
	
	$favoriteRate = self::getBasketballThreeWayFavoriteRateFromTwoWay($mainRates, $mainWinRate, $dependentWinRate, $favoriteColumnId);
	if (false === $favoriteRate) {
		return false;
	}
	$columns = array_keys($mainRates);
	$outsiderColumnId = $columns[ $columns[0] == $favoriteColumnId ? 1 : 0 ];
	// let's assume that "home/1" column has always lower ID than "away/2" column
	if ($favoriteColumnId < $outsiderColumnId) {
		$homeColumnId = $favoriteColumnId;
		$awayColumnId = $outsiderColumnId;
	}
	else {
		$homeColumnId = $outsiderColumnId;
		$awayColumnId = $favoriteColumnId;
	}
	$drawRate = self::searchRateTable($favoriteRate, $table, false);
	if (false === $drawRate) {
		return false;
	}
	$outsiderRate = self::computeLastRateFromWinRatio(array($favoriteRate, $drawRate), $dependentWinRate);
	$newRates = (
		$favoriteColumnId == $homeColumnId
		? array('1' => $favoriteRate, 'X' => $drawRate, '2' => $outsiderRate)
		: array('1' => $outsiderRate, 'X' => $drawRate, '2' => $favoriteRate)
	);
	$newRates = self::recomputeRates(
		$newRates,
		$dependentWinRate,
		array(
			'1X' => array('1', 'X'),
			'12' => array('1', '2'),
			'X2' => array('X', '2'),
		)
	);
	// just using bet subtype 218 (1X2/1X12X2) columns
	$newBetSubtypeId = 218;	
	return array(
		1857 => $newRates['1'],
		1858 => $newRates['X'],
		1859 => $newRates['2'],
		1860 => $newRates['1X'],
		1861 => $newRates['12'],
		1862 => $newRates['X2'],
	);
}

/**
 * Expects two-way rates, main win rate doesn't matter
 * @see It6_Models_Bet::calculateDependentRates()
 * @param array $mainRates
 * @param float $mainWinRate Ignored
 * @param float $dependentWinRate
 * @param integer|NULL $newBetSubtypeId
 * @return array|boolean
 */
private static function calculateDependentRatesBasketballFtDefault($mainRates, $mainWinRate, $dependentWinRate, &$newBetSubtypeId) {
	static $table = array(
		'1.01' => 25.00,
		'1.02' => 23.00,
		'1.03' => 22.00,
		'1.04' => 21.00,
		'1.05' => 20.50,
		'1.06' => 20.00,
		'1.11' => 19.00,
		'1.16' => 18.50,
		'1.21' => 18.00,
		'1.31' => 17.00,
		'1.41' => 16.00,
		'1.61' => 15.00,
		'1.81' => 14.00,
		'1.95' => 14.00,
	);
	return self::getDependentRatesBasketballFtFromTable($table, $mainRates, $mainWinRate, $dependentWinRate, $newBetSubtypeId);
}

/**
* Expects two-way rates, main win rate doesn't matter
* @see It6_Models_Bet::calculateDependentRates()
* @param array $mainRates
* @param float $mainWinRate Ignored
* @param float $dependentWinRate
* @param integer|NULL $newBetSubtypeId
* @return array|boolean
*/
private static function calculateDependentRatesBasketballFtNba($mainRates, $mainWinRate, $dependentWinRate, &$newBetSubtypeId) {
	static $table = array(
		'1.01' => 21.00,
		'1.04' => 20.00,
		'1.07' => 19.00,
		'1.10' => 18.00,
		'1.14' => 17.00,
		'1.17' => 16.00,
		'1.21' => 15.00,
		'1.31' => 14.00,
		'1.46' => 13.00,
		'1.66' => 12.00,
		'1.96' => 12.00,
	);
	return self::getDependentRatesBasketballFtFromTable($table, $mainRates, $mainWinRate, $dependentWinRate, $newBetSubtypeId);
}

} // class It6_Models_Bet
