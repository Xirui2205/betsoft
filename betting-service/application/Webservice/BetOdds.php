<?php

/**
 * Bet odds related static methods.
 * @author Pavel Klinger
 * @see Entities_Event
 *
 */
class Webservice_BetOdds extends Webservice_AbstractWebService  {

	public static $TABLE = "sazka_kurz";
	public static $TABLE_PREFIX = "bo";
	public static $IDENTITY = "sazka_id";

	protected static $ENTITY_NAME = "Entities_BetOdds";
	protected static $CONV = array(
		'sazka_id'      => 'betId',
		'poradi'		=> 'rateOrder',
		'platny_od'		=> 'validFrom',
		'bo.sloupec_id' => 'oddsOutcomeId',
		'ps.nazev'      => 'oddsOutcomeName',
		//commented out by Martin; double entityName for single dbName? Why?
		//'ps.nazev'      => 'oddsOutcomeShortCut',
		'kurz'          => 'rate'
	);



	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query)
			->join(
				array(Webservice_Bet::$OUTCOME_TABLE_PREFIX => Webservice_Bet::$OUTCOME_TABLE),
				Webservice_Bet::$OUTCOME_TABLE_PREFIX.'.sloupec_id = '.self::$TABLE_PREFIX.'.sloupec_id',
				null
			);

		return $query;
	}

	/**
	 * Returns all events in the system
	 * @return array array of the event structures
	 * @see Entities_Event
	 */
	public static function getAll($extensions = null) {
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	public static function getRateChanges($betId, $oddsId = null) {
		try {
			$db = static::getDb();
			$select = $db->select()
				->from(array(Webservice_Bet::$OUTCOME_TABLE_PREFIX => 'podtyp_sloupce'), null)
				->join(
					array('b' => 'sazky'),
					Webservice_Bet::$OUTCOME_TABLE_PREFIX.'.podtyp_id = b.podtyp_id',
					null
				)
				->columns(array('sloupec_id'))
				->where('b.sazka_id = ?', $betId);

			if ( !empty($oddsId) )
				$select = $select->where('sloupec_id = ?', $oddsId);

			$outcomes = $select->query();

			$ret = array();
			while ( $outcome = $outcomes->fetch() ) {
				$odd = $db->select()
					->from(static::$TABLE, null)
					->columns(array('kurz'))
					->where('sazka_id = ?', $betId)
					->where('sloupec_id = ?', $outcome["sloupec_id"])
					->order(array('poradi DESC'))
					->limit(2)
					->query()->fetchAll();

				$oddEntity = new Entities_BetOdds();

				$ret[$outcome["sloupec_id"]] = array();
				if ( count($odd) < 2 ) {
					$ret[$outcome["sloupec_id"]]['oldValue'] = null;
					$ret[$outcome["sloupec_id"]]['newValue'] = $odd[0]["kurz"];
					$ret[$outcome["sloupec_id"]]['change'] = 0;
				}
				else {
					$ret[$outcome["sloupec_id"]]['oldValue'] = $odd[1]["kurz"];
					$ret[$outcome["sloupec_id"]]['newValue'] = $odd[0]["kurz"];
					$ret[$outcome["sloupec_id"]]['change']
						= abs(($odd[1]["kurz"] - $odd[0]["kurz"]) / $odd[1]["kurz"]);

					It6_Log::debug('Change is:' . $ret[$outcome["sloupec_id"]]['change']);
				}

			}

			return $ret;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception("getRateChanges", 0, $e);
		}
	}

	public static function getByBetId($betId, $extensions = null) {
		$betOdds = array();

		$res = parent::getAllWhere(array('sazka_id = ?' => $betId));

		foreach ($res as $row) {
			if(empty($rates[$row['rateOrder']]['cols']))
				$rates[$row['rateOrder']]['cols'] = array();

			$rates[$row['rateOrder']] = array(
				'cols'		=> array_merge(
					$rates[$row['rateOrder']]['cols'],
					array($row['oddsOutcomeId'] => $row['rate'])
				),
				'validFrom'	=> It6_Date::fromDb($row['validFrom']),
			);

			$colNames[$row['oddsOutcomeId']] = $row['oddsOutcomeName'];
		}

		$betOdds = array(
			'rates'		=> $rates,
			'betId' 	=> $betId,
			'colNames'	=> $colNames,
		);

		return $betOdds;
	}



	public static function insert($event) {
		throw new It6_XmlRpc_Exception("Unimplemented");
	}


	public static function update($event) {
		throw new It6_XmlRpc_Exception("Unimplemented");
	}


	public static function delete($eventId) {
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

}
