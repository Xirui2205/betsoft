<?php

/**
 * Bet related static methods
 * @author Pavel Klinger
 * @see Entities_Bet
 *
 */
class Webservice_Bet extends Webservice_AbstractWebService  {

	const PARAMETER_ROOT_ALIAS_INTERVAL_FROM = "rootBetAliasInterval.from";
	const PARAMETER_ROOT_ALIAS_INTERVAL_TO = "rootBetAliasInterval.to";

	const PARAMETER_ROOT_ALIAS_INTERVAL_NEW_FROM = "rootBetAliasIntervalNew.from";
	const PARAMETER_ROOT_ALIAS_INTERVAL_NEW_TO = "rootBetAliasIntervalNew.to";

	public static $TABLE = "sazky";
	public static $TABLE_PREFIX = "s";
	public static $ODDS_TABLE = "sazka_kurz";
	public static $ODDS_TABLE_PREFIX = "sk";
	public static $ALIAS_TABLE = "bet_free_alias";
	public static $ALIAS_TABLE_NEW = "bet_free_alias_new";
	public static $OUTCOME_TABLE = "podtyp_sloupce";
	public static $OUTCOME_TABLE_PREFIX = "ps";
	public static $BET_VIEW = "sazka_pohled";
	public static $ODDS_TYPE_TABLE = "typ";
	public static $ODDS_TYPE_TABLE_PREFIX = "t";
	public static $BET_COLUMN_TABLE = "bet_column";
	public static $BET_COLUMN_TABLE_PREFIX = "bc";
	public static $IDENTITY = "sazka_id";

	protected static $CONV = array(
		'sazka_id'				=> 'betId',
		'status'				=> 'status',
		'status_ext'			=> 'statusExt',
		'live'					=> 'live',
		'bookmaker_id'			=> 'bookmakerId',
		//'sport_id'			=> 'sportId',
		//'oblast_id'			=> 'regionId',
		's.typ_id'				=> 'typeId',
		's.real_typ_id'			=> 'realTypeId',
		't.typ_alias_id'		=> 'oddsTypeAliasId',
		't2.nazev'				=> 'typeName',
		'podtyp_id'				=> 'oddsTypeId',
		'overena'				=> 'verified',
		'text'					=> 'name',
		'ticket_text'			=> 'ticketName',
		'jednoducha'			=> 'simple',
		'vysledek'				=> 'result',
		'proplacena'			=> 'payedOff',
		'proplatil_bookmaker'	=> 'payedOffBookmakerId',
		'risk_limit'			=> 'riskLimit',
		'risk_limit_balance'	=> 'riskLimitBalance',
		'info'					=> 'info',
		'message'				=> 'message',
		'ako'					=> 'ako',
		'platna_od'				=> 'validFromTime',
		'platna_do'				=> 'validToTime',
		'parent_id'				=> 'parentId',
		'alias'					=> 'alias',
		'alias_new'				=> 'aliasNew',
		'alias_released'		=> 'aliasReleased',
		'alias_released_new'	=> 'aliasReleased_new',
		'betradar_autoupdate'	=> 'betradarAutoupdate',
		//'betradar_sazka_id'	 => 'betradarBetId',
		'betradar_match_id'		=> 'betradarMatchId',
		'u.udalost_id'			=> 'eventId',
		'text_note'				=> 'textNote',
		'sp.nazev'				=> 'sportName',
		'sp.sport_id'			=> 'sportId',
		'o.nazev'				=> 'regionName',
		'o.oblast_id'			=> 'regionId',
		'u.nazev'				=> 'eventName',
		'CONCAT(alias,t.typ_alias_id)' => 'fullAlias'
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->join(
				array('u' => 'udalost'),
				static::$TABLE_PREFIX . '.udalost_id = u.udalost_id',
				array())
			->join(
				array('sp' => 'sport'),
				'u.sport_id = sp.sport_id',
				array())
			->join(
				array('o' => 'oblast'),
				'u.oblast_id = o.oblast_id',
				array())
			->join(
				array(static::$ODDS_TYPE_TABLE_PREFIX => static::$ODDS_TYPE_TABLE),
				'COALESCE(' . static::$TABLE_PREFIX . '.real_typ_id,' . static::$TABLE_PREFIX
					. '.typ_id) = '.static::$ODDS_TYPE_TABLE_PREFIX.'.typ_id',
				array('typ_alias_id')
			)
			->join(
				array('t2' => static::$ODDS_TYPE_TABLE),
				static::$TABLE_PREFIX . '.typ_id = t2.typ_id',
				array('nazev')
			);
	}

	/**
	 * Returns all bets in the system.
	 * @return array array of bet structs
	 * @see Entities_Bet
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find bet by given identifier.
	 * @param integer $betId identifier of the bet
	 * @return struct bet structure
	 * @see Entities_Bet
	 */
	public static function getById($betId, $extensions = null) {
		return parent::getById($betId, $extensions);
	}

	/**
	 * Find bet by given identifier with correlated bets.
	 * @param integer $betId identifier of the bet
	 * @return struct bet structure
	 * @see Entities_Bet
	 */
	public static function getByIdWithCorrelated($betId, $extensions = null) {
		$ret =  static::getById($betId, $extensions);
		$ret['correlated'] = static::getCorrelatedBets($ret['betId']);
		return $ret;
	}

	/**
	 * Find bet by given alias, returns only relevant bets.
	 * @param string $alias
	 * @param string $oddsTypeId
	 * @return struct bet structure
	 * @see Entities_Bet
	 * @see Entities_Bet#alias
	 */
	public static function getByAlias($alias, $oddsTypeAliasId, $extensions = null) {
		$bet = static::getOneWhere(
			array(
				'alias_new = ?' => (int)($alias.$oddsTypeAliasId),
				'status = ?' => 0,
				'validFromTime < ?' => It6_Date::dbNow(),
				'validToTime > ?' => It6_Date::dbNow(),
			),
			$extensions
		);
		return $bet;
	}

	public static function getByFullAlias($fullAlias, $extensions = null) {
		$oddsTypeAliasId = substr($fullAlias, -2, 2);
		$alias = substr($fullAlias, 0, strlen($fullAlias)-2);
		return static::getByAlias($alias, $oddsTypeAliasId, $extensions);
	}

	/**
	 * Find all bets by given alias, returns only relevant bets.
	 * @param integer $alias
	 * @param integer $oddsTypeId
	 * @param bool $noActive
	 * @return struct bet structure
	 * @see Entities_Bet
	 * @see Entities_Bet#alias
	 */
	public static function getAllBetsByAlias($alias, $oddsTypeAliasId, $extensions = null) {
		$where_alias_new = array(
			'alias_new = ?' => $alias.$oddsTypeAliasId
		);
		$bet = static::getAllWhere($where_alias_new, $extensions);

		if (empty($bet)) {
			$where = array(
				'alias = ?' => $alias,
				'oddsTypeAliasId = ?' => $oddsTypeAliasId
			);
			$bet = static::getAllWhere($where, $extensions);
		}

		if (empty($bet)) {
			$where_three = array(
				'fullAlias = ?' => sprintf("%d%02d", $alias, $oddsTypeAliasId),
			);
			$bet = static::getAllWhere($where_three, $extensions);
		}
		return $bet;
	}

	/**
	 * Find bet by given alias, returns only relevant bets.
	 * @param string $alias
	 * @param string $oddsTypeId
	 * @return struct bet structure
	 * @see Entities_Bet
	 * @see Entities_Bet#alias
	 */
	public static function getByAliasWithCorrelated($alias, $oddsTypeAliasId, $extensions = null) {
		$ret = static::getByAlias($alias, $oddsTypeAliasId, $extensions);
		$ret['correlated'] = static::getCorrelatedBets($ret['betId']);
		return $ret;
	}

	/**
	 * Insert new bet. Value of the bet identifier is ignored and new
	 * is generated.
	 * @param struct $bet structure of the bet
	 * @return integer bet identifier of the created bet
	 * @see Entities_Bet
	 */
	public static function insert($bet) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Update bet.
	 * @param struct $bet structure of the bet
	 * @return true on success
	 * @see Entities_Bet
	 */
	public static function update($bet) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Delete bet.
	 * @param struct $betId idenetifier of the bet.
	 * @return true on success
	 * @see Entities_Bet
	 */
	public static function delete($betId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns all current bets in the system.
	 * @return array array of bet structs
	 * @see Entities_Bet
	 */
	public static function getOpened($extensions = null) {
		return static::getAllWhere(
			array(
				'validFromTime < ?' => It6_Date::dbNow(),
				'validToTime > ?' => It6_Date::dbNow(),
				'status = 0'),
			$extensions);
	}

	/**
	 * Returns all current main bets in the system.
	 * @return array array of bet structs
	 * @see Entities_Bet
	 */
	public static function getMainOpened($extensions = null) {
		return static::getAllWhere(
			array(
				'validFromTime < ?' => It6_Date::dbNow(),
				'validToTime > ?' => It6_Date::dbNow(),
				'parentId IS NULL', 'status = 0'),
			$extensions);
	}

	/**
	 * Returns all main bets in the system.
	 * @return array array of bet structs
	 * @see Entities_Bet
	 */
	public static function getMainAll($extensions = null) {
		return static::getAllWhere(
			array('typeId = 19'), $extensions
		);
	}

	/**
	 * Returns all relevant statistics.
	 * @return string
	 * @see Entities_Bet
	 */
	public static function getStatisticsRelevant($betId) {
		try {
			$db = static::getMainDb();
			$select = $db->select()
				->from(
					array('tk' => Webservice_Ticket::$TIPS_TABLE),
					array('sazka_id' => 'tk.sazka_id', 'count' => 'COUNT(*)')
				)
				->join(
					array('ps' => 'podtyp_sloupce'),
					'ps.sloupec_id = tk.sloupec_id',
					'ps.nazev'
				)
				->where('tk.sazka_id = ?', $betId)
				->order(array('poradi ASC'))
				->group(array('tk.sazka_id', 'ps.nazev'))
				->query()->fetchAll();

			$ret = '';
			if (!empty($select)) {
				foreach ($select as $sel) {
					$ret .= $sel["nazev"]."-".$sel["count"]."x/";
				}
			}

			if (!empty($ret)) return $ret;
			else return false;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("getStatisticsRelevant: ".$e);
		}
	}

	/**
	 * Returns all columns for subtyp.
	 * @return array
	 * @see Entities_Bet
	 */
	public static function getAllColumnsStatisticsBet($betId, $subTypId) {

		try {
			$db = static::getMainDb();
			$subSelect = $db->select()
				->from(
					array('tk' => Webservice_Ticket::$TIPS_TABLE),
					array('count' => 'COUNT(*)', 'amount' => 'SUM(tk.amount)')
				)
				->join(
					array('ps' => static::$OUTCOME_TABLE),
					'ps.sloupec_id = tk.sloupec_id',
					'ps.sloupec_id'
				)
				->where('tk.sazka_id = ?', $betId)
				->group(array('tk.sloupec_id'));

			$select = $db->select()
				->from(
					array(self::$OUTCOME_TABLE_PREFIX => self::$OUTCOME_TABLE),
					array('nazev' => 'ps.nazev', 'sloupec' => 'ps.sloupec_id')
				)
				->joinLeft(
					array('a' => new Zend_Db_Expr('('.$subSelect->assemble().')')),
					'a.sloupec_id = ps.sloupec_id',
					array('a.count', 'a.amount')
				)
				->where(self::$OUTCOME_TABLE_PREFIX.'.podtyp_id = ?', $subTypId)
				->order(array('poradi ASC'))
				->query()->fetchAll();

			if (!empty($select)) return $select;
			else return false;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("getAllColumnsStatisticsBet: ".$e);
		}
	}

	/**
	 * Returns all not current bets in the system.
	 * @return array of bet structs
	 * @see Entities_Bet
	 */
	public static function getClosed($extensions = null) {
		return static::getAllWhere(
			array(
				'validFromTime > ?' => It6_Date::dbNow(),
				'validToTime < ?' => It6_Date::dbNow()),
			$extensions);

	}

	/**
	 * Returns all not current main bets in the system.
	 * @return array of bet structs
	 * @see Entities_Bet
	 */
	public static function getMainClosed($extensions = null) {
		return static::getAllWhere(
			array(
				'validFromTime > ?' => It6_Date::dbNow(),
				'validToTime < ?' => It6_Date::dbNow(),
				'parentId IS NULL'),
			$extensions);
	}


	/**
	 * Returns correlated bets
	 * @param int $betId
	 * @return array array struct of bets
	 */
	public static function getCorrelatedBets($betId) {
		$db = static::getDb();
		$bets = It6_Models_Bet::getCorrelatedBets($betId,true,$db);
		if ( empty($bets) )
			return array();
		$ret = array();
		foreach ($bets as $bet) {
			$ret[] = $bet['sazka1_id'] == $betId ? $bet['sazka2_id'] : $bet['sazka1_id']; 
		}
		return $ret;
	}

	/**
	 * Returns child bets for given main bet.
	 * @param integer $betId
	 * @return array array of Bet structures
	 */
	public static function getChildBets($betId, $extensions = null) {
		return static::getAllWhere(
			array(
				'validFromTime < ?' => It6_Date::dbNow(),
				'validToTime > ?' => It6_Date::dbNow(),
				'parentId = ?' => $betId ),
			$extensions);
	}

	/**
	 * Returns all child bets for given main bet.
	 * @param integer $betId
	 * @return array array of Bet structures
	 */
	public static function getAllChildBets($betId, $extensions = null) {
		return static::getAllWhere(
			array('parentId = ?' => $betId),
			$extensions
		);
	}

	/** Sets bet result.
	 * @param integer $betId Identifier of the branch
	 * @param string $score null is all users
	 * @return integer betId of the bet
	 */
	public static function setBetScore($betId, $score) {
		$db = static::getDb();

		It6_DbTransaction::begin($db);
		try {
			//$entity = new It6_ArrayWrapper($entity);
			$data = array('score' => $score);

			$db->update(
				static::$TABLE,
				$data,
				array(static::$IDENTITY . '= ?' => $betId)
			);

			It6_DbTransaction::commit($db);
			return $betId;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update entity. (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	public static function fetchAllEntities($query, $columns = null) {
		$ret = array();
		while ( $row = $query->fetch() )
			$ret[] = static::toEntityImpl($row, $columns);
		static::translateEntity($ret, true);
		return $ret;
	}

	public static function toEntity($bet, $columns = null) {
		$ret = static::toEntityImpl($bet, $columns);
		static::translateEntity($ret, false);
		return $ret;
	}

	private static function translateEntity(&$bet, $multiple) {
		$db = static::getDb();
		if (!$multiple)
			$bet = array($bet);
		$dictionary = array();
		foreach ($bet as $i => $_bet) {
			$dictionary[$_bet['typeName']] = true;
			$dictionary[$_bet['sportName']] = true;
			$dictionary[$_bet['regionName']] = true;
			$dictionary[$_bet['eventName']] = true;
			if (!empty($_bet['odds'])) {
				foreach ($_bet['odds'] as $odd) {
					$dictionary[$odd['oddsOutcomeName']] = true;
					$dictionary[$odd['oddsOutcomeShortCut']] = true;
				}
			}
		}
		if (!empty($dictionary)) {
			$dictionary = It6_Models_Translator::translate(array_keys($dictionary), 1, $db);
			foreach ($bet as $i => $_bet) {
				$bet[$i]['typeName'] = $dictionary[$_bet['typeName']];
				$bet[$i]['sportName'] = $dictionary[$_bet['sportName']];
				$bet[$i]['regionName'] = $dictionary[$_bet['regionName']];
				$bet[$i]['eventName'] = $dictionary[$_bet['eventName']];
				if (!empty($bet[$i]->odds)) {
					foreach ($bet[$i]->odds as $j => $_) {
						$ptr = &$bet[$i]->odds[$j];
						$ptr->oddsOutcomeName = $dictionary[$ptr->oddsOutcomeName];
						$ptr->oddsOutcomeShortCut = $dictionary[$ptr->oddsOutcomeShortCut];
					}
				}
			}
		}
		if (!$multiple)
			$bet = $bet[0];
	}

	private static function toEntityImpl($bet, $columns = null) {
		$db = static::getDb();

		try {
			$ret = parent::toEntity($bet, $columns);

			//pokud sazka nema nastaveny specialni text co zobrazovt na tiketu, dame tam text
			if (empty($bet['ticketName']))
				$ret->ticketName = $ret->name;

			if ( empty($columns) || in_array('odds', $columns) ) {
				$odds = array();
				$outcomes = $db->select()
					->from(static::$OUTCOME_TABLE, array('sloupec_id', 'nazev'))
					->where('podtyp_id = ?', $ret->oddsTypeId)
					->query();

				while ( $outcome = $outcomes->fetch() ) {
					$odd = $db->select()
							->from(static::$ODDS_TABLE, null)
							->columns(array('kurz'))
							->where('sazka_id = ?', $ret->betId)
							->where('sloupec_id = ?', $outcome["sloupec_id"])
							->order(array('poradi DESC'))
							->limit(1, 0)
							->query()->fetch();

					$oddEntity = new Entities_BetOdds();

					$oddEntity->rate = $odd["kurz"];

					$oddEntity->oddsOutcomeId = $outcome["sloupec_id"];
					$oddEntity->oddsOutcomeName = $outcome["nazev"];
					$oddEntity->oddsOutcomeShortCut = $outcome["nazev"];

					$odds[] = $oddEntity;
				}

				$ret->odds = $odds;
			}

		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('Bet::toEntity',0,$e);
		}

		return $ret;

	}

	/**
	 * Generates alias
	 * @param integer $betId identifier of the bet
	 * @return string
	 * @see Entities_Bet
	 */
	public static function generateAlias($betId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$bet = static::getById($betId);
			if ( false === $bet )
				throw new Exception ('Unknown bet id: \''. $betId . '\'');

			if ( !empty($bet['alias']) )
				throw new Exception('Alias already generated');

			$alias = static::getFreeAlias($bet);
			$alias_new = static::getFreeAliasNew($bet);

			if ( empty($alias) )
				throw new Exception("Out of bet aliases");

			if ( empty($alias_new) )
				throw new Exception("Out of bet aliases new");

			$db->update(
				static::$TABLE,
				array('alias' => $alias),
				array( static::$IDENTITY . '= ?' => $betId)
			);

			$db->update(
				static::$TABLE,
				array('alias_new' => $alias_new),
				array( static::$IDENTITY . '= ?' => $betId)
			);

			$db->delete(
				static::$ALIAS_TABLE,
				array( 'alias = ?' => $alias)
			);

			$db->delete(
				static::$ALIAS_TABLE_NEW,
				array( 'alias = ?' => $alias_new)
			);

			It6_DbTransaction::commit($db);
			return $alias;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception('Can not generate bet alias', 0, $e);
		}
	}

	/**
	 * Free alias
	 * @param integer $betId identifier of the bet
	 * @return boolean true if alias was completle released (was last)
	 * @see Entities_Bet
	 */
	public static function releaseAlias($betId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$bet = static::getById($betId);

			if ( false === $bet )
				throw new Exception ('Unknown bet id: \''. $betId . '\'');

			if ( empty($bet['alias']) )
				throw new Exception('Alias was not generated');

			if ( !empty($bet['aliasReleased']) )
				throw new Exception('Alias was already released.');

			if ( It6_Date::fromDbAsTimestamp($bet['validToTime']) >= It6_Date::nowAsTimestamp() )
				throw new Exception('Bet is still valid.');

			$alias = $bet['alias'];

			$db->update(
				static::$TABLE,
				array('alias_released' => 1),
				array( static::$IDENTITY . '= ?' => $betId));

			$allReleased = $db->select()
				->from(static::$TABLE, array('v' => 'Count(*) = 0'))
				->where('alias = ?', $alias)
				->where('alias_released IS NULL')
				->query()->fetch();

			if ( $allReleased['v'] ) {
				$_ = $db->select()
					->from(static::$ALIAS_TABLE, array('v' => 'Count(*) = 0'))
					->where('alias = ?', $alias)
					->query()->fetch();

				if ( $_['v'] ) {
					$db->insert(
						static::$ALIAS_TABLE,
						array('alias'=> $alias)
					);

					It6_Log::info(
						"Alias '%alias%' released.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('alias' => $alias)
					);
				} else {
					It6_Log::emerg(
						"Alias '%alias%' wrongly in free aliases table.",
						It6_Log::TAG_DEFAULT,
						array('alias' => $alias)
					);
					$allReleased['v'] = false;
				}
			}

			It6_DbTransaction::commit($db);
			return $allReleased['v'];
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception('Can not free bet alias', 0, $e);
		}
	}

	/**
	 * Free alias_new
	 * @param integer $betId identifier of the bet
	 * @return boolean true if alias_new was completle released (was last)
	 * @see Entities_Bet
	 */
	public static function releaseAliasNew($betId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$bet = static::getById($betId);

			if ( false === $bet )
				throw new Exception ('Unknown bet id: \''. $betId . '\'');

			if ( empty($bet['aliasNew']) )
				throw new Exception('Alias new was not generated');

			if ( !empty($bet['aliasReleasedNew']) )
				throw new Exception('Alias new was already released.');

			if ( It6_Date::fromDbAsTimestamp($bet['validToTime']) >= It6_Date::nowAsTimestamp() )
				throw new Exception('Bet is still valid.');

			$alias_new = $bet['aliasNew'];

			$db->update(
				static::$TABLE,
				array('alias_released_new' => 1),
				array( static::$IDENTITY . '= ?' => $betId)
			);

			$released = $db->select()
				->from(static::$ALIAS_TABLE_NEW, array('v' => 'Count(*) = 0'))
				->where('alias = ?', $alias_new)
				->query()->fetch();

			if ($released) {
				$db->insert(
					static::$ALIAS_TABLE_NEW,
					array('alias' => $alias_new)
				);
				It6_Log::info(
					"Alias new '%alias%' released.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('alias' => $alias_new)
				);
			} else {
				It6_Log::emerg(
					"Alias new '%alias%' wrongly in free aliases new table.",
					It6_Log::TAG_DEFAULT,
					array('alias' => $alias)
				);
			}

			It6_DbTransaction::commit($db);
			return $released;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception('Can not free bet alias', 0, $e);
		}
	}

	public static function releaseAliases() {

		try {
			$bets = static::getAllWhereColumns(
				array(
					'validToTime < ?' => It6_Date::dbNow(),
					'aliasReleased IS NULL',
					'alias IS NOT NULL',
					'payedOff = 1'),
				array('betId'));

			$error = 0;
			$count = 0;
			$count_new = 0;

			foreach ( $bets as $bet ) {
				try {
					if (static::releaseAlias($bet->betId)) ++$count;
					if (static::releaseAliasNew($bet->betId)) ++$count_new;
				}
				catch (Exception $e) {
					++$error;
				}
			}

			It6_Log::info(
				"'%count%' aliases released ('%count_new%' aliases new released), there were '%error%' errors.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('count' => $count, 'count_new' => $count_new, 'error' => $error)
			);

			return $error == 0;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception('Can not free bet alias', 0, $e);
		}
	}

	protected static function getFreeAlias($bet) {
		if (!empty($bet->parentId)) {
			$parent = static::getById($bet->parentId);

			if ( false === $parent )
				throw new Exception ('Unknown bet id: \''. $bet->parentId . '\'');

			if (
				$parent->typeId == $bet->typeId
				&& (
					empty($parent->realTypeId)
					|| empty($bet->realTypeId)
					|| $parent->realTypeId == $bet->realTypeId
				)
			)
				throw new Exception("Unable to create bet duplicate");

			if ( !empty($parent->alias) )
				return $parent->alias;

			$sbs = static::getBetPack($bet->parentId);
		} else {
			$sbs = static::getBetPack($bet->betId);
		}

		foreach ( $sbs as $sb ) {
			if ( !empty($sb['alias']) ) return $sb['alias'];
		}

		$event = Webservice_Event::getById($bet->eventId);

		if ( false === $event )
			throw new Exception ('Unknown event id: \''. $bet->eventId . '\'');

		if ( !empty($event->betAliasFrom) && !empty($event->betAliasTo) ) {
			$alias = static::getFreeAliasFromInterval($event->betAliasFrom, $event->betAliasTo);
		}

		if ( empty($alias) ) {
			$sport = Webservice_Sport::getById($event->sportId);
			if ( false === $sport )
				throw new Exception ('Unknown sport id: \''. $event->sportId . '\'');

			if ( !empty($sport->betAliasFrom) && !empty($sport->betAliasTo) ) {
				$alias = static::getFreeAliasFromInterval($sport->betAliasFrom, $sport->betAliasTo);
			}
		}

		if ( empty($alias) ) {
			$from = Webservice_Parameter::getGlobalParameter(self::PARAMETER_ROOT_ALIAS_INTERVAL_FROM);
			$to = Webservice_Parameter::getGlobalParameter(self::PARAMETER_ROOT_ALIAS_INTERVAL_TO);
			$alias = static::getFreeAliasFromInterval($from, $to);
		}

		return $alias;

	}

	/**
	 * Get free alias new.
	 */
	protected static function getFreeAliasNew($bet) {

		$event = Webservice_Event::getById($bet->eventId);
		if (false === $event) throw new Exception ('Unknown event id: \''. $bet->eventId . '\'');

		// zustava puvodni ptz u udalosti se zatim nenastavuji rozsahy
		if (!empty($event->betAliasFrom) && !empty($event->betAliasTo)) {
			$alias = static::getFreeAliasNewFromInterval($event->betAliasFrom, $event->betAliasTo);
		}

		if (empty($alias)) {
			$sport = Webservice_Sport::getById($event->sportId);
			if (false === $sport) throw new Exception ('Unknown sport id: \''. $event->sportId . '\'');
			if (!empty($sport->betAliasFromNew) && !empty($sport->betAliasToNew)) {
				$alias = static::getFreeAliasNewFromInterval($sport->betAliasFromNew, $sport->betAliasToNew);
			}
		}

		if (empty($alias)) {
			$from = Webservice_Parameter::getGlobalParameter(self::PARAMETER_ROOT_ALIAS_INTERVAL_NEW_FROM);
			$to = Webservice_Parameter::getGlobalParameter(self::PARAMETER_ROOT_ALIAS_INTERVAL_NEW_TO);
			$alias = static::getFreeAliasNewFromInterval($from, $to);
		}

		return $alias;
	}

	/**
	 * Return full alias
	 * @param integer $betId identifier of the bet
	 * @return string
	 * @see Entities_Bet
	 */
	public static function getFullAlias($betId) {
		$bet = static::getById($betId);
		if ( false === $bet )
			throw new Exception ('Unknown bet id: \''. $betId . '\'');

		return $bet->alias.'/'.$bet->typeId;
	}

	/**
	 * Return true if bet has alias
	 * @param integer $betId identifier of the bet
	 * @return string
	 * @see Entities_Bet
	 */
	public static function hasAlias($betId) {
		$bet = static::getById($betId);
		if ( false === $bet )
			throw new Exception ('Unknown bet id: \''. $betId . '\'');

		return !empty($bet->alias);
	}

		/**
	 * Return true if bet has alias
	 * @param integer $betId identifier of the bet
	 * @return string
	 * @see Entities_Bet
	 */
	public static function isBetradarBet($betId) {
		$bet = static::getById($betId);
		if ( false === $bet )
			throw new Exception ('Unknown bet id: \''. $betId . '\'');

		return !empty($bet->betradarMatchId);
	}

	public static function isSuspendedAndOpened($betId,$extensions = null) {
		$bet = static::getOneWhere(
			array('betId' => $betId, 'status' => 2, 'validFromTime < ?' => It6_Date::dbNow(), 'validToTime > ?' => It6_Date::dbNow()),
			$extensions);

		/*if ( false === $bet )
			throw new Exception ('Unknown bet id: \''. $betId . '\'');*/

		return !empty($bet->betId);
	}

	public static function isActivatedAndOpened($betId,$extensions = null) {
		$bet = static::getOneWhere(
			array('betId' => $betId, 'status' => 0, 'validFromTime < ?' => It6_Date::dbNow(), 'validToTime > ?' => It6_Date::dbNow()),
			$extensions);

		/*if ( false === $bet )
			throw new Exception ('Unknown bet id: \''. $betId . '\'');*/

		return !empty($bet->betId);
	}

	/** Updates status to active (0..active, 2 .. suspended).
	 * @param integer $id Identifier of the bet
	 */
	public static function activateBet($id) {
		$db = static::getDb();

		It6_DbTransaction::begin($db);
		try {
			//$entity = new It6_ArrayWrapper($entity);
			$data = array(
				'status' => It6_Models_Bet::STATUS_NEW,
				'status_ext' => null,
			);

			$n = $db->update(
				static::$TABLE,
				$data,
				array( static::$IDENTITY . ' = ?' => $id)
			);
			if (0 < $n) {
				It6_Models_BetChangelog::saveBetChangeOfStatus($id, It6_Models_Bet::STATUS_NEW, 0, 0, $db);
			};

			It6_GlobalCache_Invalidator::invalidateSportsbookByBet($id);
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

			It6_DbTransaction::commit($db);
			return $id;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update entity. (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	/**
	 * Updates bet pack hierarchy (main bets and subbets)
	 * @param integer $id Identifier of the branch
	 * @param bool $isBetId Identifier of the branch
	 */
	public static function updateBetPackHierarchy($id, $isBetId = TRUE) {

		if ($isBetId)
			$bet = static::getById($id);
		else {
			throw new It6_XmlRpc_Exception('Deprecated way to call this method');
			$bet = static::getOneWhere(array('betradarBetId = ?' => $id), NULL);
		}
			
		if ($bet) {
			if ($isBetId) {
				if (empty($bet['parentId']))
					$betParentId = $id;
				else
					$betParentId = $bet['parentId'];
			}
			else
				$betParentId = $bet['betradarMatchId'];

			if ($betParentId!=NULL) {
				$betPack = static::getBetPack($betParentId, $isBetId);

				$newParentId = static::getMinBetTypId($betParentId, $betPack);

				if (!$isBetId) {
					$betHelp = static::getById($newParentId);
					$betradarBetId = $betHelp['betradarBetId'];
				}
				else
					$betradarBetId = null;

				if ($newParentId != $betParentId) //je treba updatovat smecku
					return static::updateBetPackParentId($newParentId, $betParentId, $isBetId, $betradarBetId);
				else //netreba updatovat smecku
					return false;
			}
		}
		return false;

	}

	/** Updates parent_id of given bet.
	 * @param integer $betId Identifier of the branch
	 * @param integer $parentId null is all users
	 */
	public static function updateBetParentId($id, $parentId, $isBetId = TRUE) {
		$db = static::getDb();

		if ($isBetId)
			$idColumn = static::$IDENTITY;
		else
			//$idColumn = 'betradar_sazka_id';
			throw new It6_XmlRpc_Exception('Deprecated parameter value for isBetId parameter');

		try {
			//$entity = new It6_ArrayWrapper($entity);
			$data = array('parent_id' => $parentId);

			$db->update(
				static::$TABLE,
				$data,
				array( $idColumn . '= ?' => $id)
			);

			return $id;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("Can not update entity. (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	/**
	 *  Updates parent_id of given bet.
	 * @param integer $newParentId Identifier of the branch
	 * @param integer $oldParentId null is all users
	 */
	public static function updateBetPackParentId($newParentId, $oldParentId, $isBetId, $betradarBetId = NULL) {
		try {
			//subbets oldParentId nastavit na newParentId
		//	if ($isBetId)
				$sb = static::getBetPack($oldParentId, $isBetId);
		//	else
			//	$sb = static::getBetPack($id, $isBetId);

			foreach ($sb as $bet) {
				static::updateBetParentId($bet['sazka_id'],$newParentId);
			}
			//vynulovat parenta u newParentId
			if (!$isBetId) $newParentId = $betradarBetId;

			static::updateBetParentId($newParentId, NULL, $isBetId);

			return $newParentId;

		} catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("Can not update entity. (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	public static function getMinBetTypId($betId, $betPack) {
		//$betPack = static::getBetPack($betId);
		//get betId where typ_alias_id is minimal
		$typeIds = array();
		$betIds = array();
		foreach ($betPack as $b) {
			$typeIds[] = $b['typ_alias_id'];
			$betIds[] = $b['sazka_id'];
		}
		$minKey = array_search(min($typeIds),$typeIds);
		return $betIds[$minKey];
	}

	/**
	 * returns main bet and all subbets
	 * @param integer $betId id of the bet
	 * @param bool $isBetId indicates whether to use sazka_id or betradar_match_id
	 * @return integer
	 * */
	public static function getBetPack($id, $isBetId = TRUE) {
		if ($isBetId) $idColumn = 'sazka_id';
		else $idColumn = 'betradar_match_id';

		$db = static::getDb();
		$bp = $db->select()
			->from(static::$TABLE)
			->where('parent_id = ?',$id)
			->orWhere($idColumn . ' = ?',$id)
			->join(array('t' => 'typ'),
					'sazky.typ_id = t.typ_id',
					array('typ_alias_id' => 'typ_alias_id'))
			->query()->fetchAll();

		return $bp;
	}

	/*public static function getSubBets($betId) {
		$db = static::getDb();
		$bp = $db->select()
			->from(static::$TABLE)
			->where('parent_id = ?',$betId)
			->query()->fetchAll();

		return $bp;
	}*/

	public static function getMainBet($betId) {
		$db = static::getDb();
		$b = $db->select()
			->from(static::$TABLE)
			->where('parent_id = ?',$betId)
			->query()->fetch();

		//return $b['sazka_id'];
		if ( empty($b) )
			return null;
		else
			return $b['sazka_id'];
	}

	/**
	 * Get sub bets count by main (parent) bet id.
	 * (Terminology: sub bets = aux bets = podpurky)
	 * @param int $mainBetId
	 * @return int sub bets count
	 */
	public static function getSubBetsCount($mainBetId) {
		$result = static::getDb()->select()
			->from(static::$TABLE, array('COUNT(*) as subBetsCount'))
			->where('parent_id = ?', $mainBetId)
			->where('status = 0')
			->where('platna_do > ?', It6_Date::dbNow())
			->query()->fetch();
		return $result['subBetsCount'] ? $result['subBetsCount'] : 0;
	}

	protected static function getFreeAliasFromInterval($from, $to) {
		$db = static::getDb();
		$alias = $db->select()
			->from(static::$ALIAS_TABLE)
			->where('alias >= ?',$from)
			->where('alias <= ?',$to)
			->order('alias')
			->limit(1)
			->query()->fetch();
		if ( empty($alias) )
			return null;
		else
			return $alias['alias'];
	}

	/**
	 * Get new free alias from interval.
	 */
	protected static function getFreeAliasNewFromInterval($from, $to) {
		$db = static::getDb();
		$alias = $db->select()->from(static::$ALIAS_TABLE_NEW)
			->where('alias >= ?',$from)
			->where('alias <= ?',$to)
			->order('alias')
			->limit(1)
			->query()->fetch();

		if (empty($alias)) return null;
		else return $alias['alias'];
	}

	/**
	 * Resets bet day risk limits.
	 */
	public static function cleanDayRiskLimits() {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE,
				array('risk_limit_balance' => 0),
				'1');

			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
			It6_GlobalCache_Invalidator::invalidateSportsbook();

			It6_DbTransaction::commit($db);
		}
	 	catch ( Exception $e ) {
	 		It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not clean day risk limits:", 0, $e);
		}
		return true;
	}
	
	/**
	 * Get top bets with top risk 
	 * @param float $factor ratio betwean risk_limit and risk_limit_balance
	 */
	public static function getTopRisklimit($limit) {
		$db = static::getDb();
		return $db->select()
			->from(
				array(static::$TABLE_PREFIX => static::$TABLE),
				array(
					'betId' =>'sazka_id',
					'name'  =>'text',
					'eventName'  =>'u.nazev',
					'sportName'  =>'sp.nazev',
					'typeName'  =>'t.nazev',
					'riskLimit'  =>'risk_limit',
					'riskLimitBalance'  =>'risk_limit_balance',
					'factor'=>'(risk_limit_balance / risk_limit)',
				))
			->join(
				array('u' => 'udalost'),
				static::$TABLE_PREFIX . '.udalost_id = u.udalost_id',
				null)
			->join(
				array('sp' => 'sport'),
				'u.sport_id = sp.sport_id',
				null)
			->join(
				array('t' => static::$ODDS_TYPE_TABLE),
				'COALESCE(' . static::$TABLE_PREFIX . '.real_typ_id,' . static::$TABLE_PREFIX
					. '.typ_id) = t.typ_id',
				array('t.typ_alias_id', 't.nazev')
				)
			->where('status = 0')
			->where('platna_od < ?', It6_Date::dbNow())
			->where('platna_do > ?', It6_Date::dbNow())
			->order('factor DESC')
			->limit($limit)
			->query()->fetchAll();
	}

	/**
	 * Creates initial record for column newly created bet.
	 * @param integer $betId
	 * @param array $columnIds
	 * @return integer number of records initialized
	 */
	public static function initBetColumnData($betId, $columnIds) {
		$n = 0;
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			foreach ($columnIds as $columnId) {
		 		$n += $db->insert(
		 			static::$BET_COLUMN_TABLE,
		 			array(
		 				'sazka_id' => $betId,
		 				'sloupec_id' => $columnId,
		 				'risk_limit_balance' => 0,
		 			)
				);
			}
		}
		catch (Exception $e) {
			It6_Log::err(
				'Bet column data not inititlized',
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array('betId' => $betId, 'columnId' => $columnId),
				$e
			);
			It6_DbTransaction::rollback($db);
			$n = 0;
		}
		return $n;
	}

	/**
	 * Increases/decreases bet column accumulated data.
	 * @param integer $betId
	 * @param integer $columnId
	 * @param struct $changes Fields:
	 *                        riskLimitBalance ... value that will be added to current balance
	 * @return integer Count of records that was effectively changed
	 */
	public static function changeBetColumnData($betId, $columnId, $changes) {
		$n = 0;
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$dbChanges = array();
			if (isset($changes['riskLimitBalance'])) {
				$delta = floatval($changes['riskLimitBalance']);
				if (0 != $delta)
					$dbChanges['risk_limit_balance'] = new Zend_Db_Expr("risk_limit_balance + $delta");
			}
			if (isset($changes['absoluteStake'])) {
				$delta = floatval($changes['absoluteStake']);
				if (0 != $delta)
					$dbChanges['absolute_stake'] = new Zend_Db_Expr("absolute_stake + $delta");
			}
			if (isset($changes['betCount'])) {
				$delta = floatval($changes['betCount']);
				if (0 != $delta)
					$dbChanges['bet_count'] = new Zend_Db_Expr("bet_count + $delta");
			}
			
			if(!empty($dbChanges)) {
				$n = $db->update(
					static::$BET_COLUMN_TABLE,
					$dbChanges,
					array('sazka_id=?' => $betId, 'sloupec_id=?' => $columnId)
				);
			}

			It6_DbTransaction::commit($db);

			Webservice_Alert::assert(
				'RiskLimit',
				array('betId' => $betId)
			);
		}
		catch (Exception $e) {
			It6_Log::err(
				'Bet column risk limit balance not changed',
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array('betId' => $betId, 'columnId' => $columnId, 'changes' => $changes),
				$e
			);
			It6_DbTransaction::rollback($db);
			$n = 0;
		}
		return $n;
	}

	/**
	 * Fetch risk limit balances for given bet column(s)
	 * @param integer|array $betId betId or array of betIds
	 * @param integer|array|NULL $columnId One column ID, array of IDs or empty value for all columns
	 * @return float|array|NULL If one column was requested, one bet column data structure or NULL is returned.
	 *                     If all or list of columns were requested, array (columnId => struct) is returned,
	 *                     data for columns that were not found are not present in returned array.
	 *                     Bet column data structure fields:
	 *                        riskLimitBalance ... current risk limit balance
	 */
	public static function getBetColumnData($betIds, $columnId=null, $nonEmptyOnly=null, $order=null) {
		$betColumns = array();
		$ordBetCols = array();
		$select = static::getDb()->select()
			->from(
				static::$BET_COLUMN_TABLE,
				array(
					'betId' => 'sazka_id',
					'columnId' => 'sloupec_id',
					'weightedStake' => 'risk_limit_balance',
					'absoluteStake' => 'absolute_stake',
					'betCount' => 'bet_count'
				)
			)
			->where('sazka_id IN (?)', $betIds);
		if (!empty($columnId))
			$select->where('sloupec_id IN (?)', $columnId);
		if ($nonEmptyOnly === true)
			$select->where('bet_count > ?', 0);
		if (!empty($order))
			$select->order($order.' DESC');
		$rows = $select->query()->fetchAll();

		foreach ($rows as $row) {
			$betColumns[$row['betId']][$row['columnId']] = array(
				'weightedStake'	=> $row['weightedStake'],
				'absoluteStake'	=> $row['absoluteStake'],
				'betCount'		=> $row['betCount'],
			);
		}
		
		foreach($betColumns as $betId => $cols) {
			$ordBetCols[] = array(
				'betId'	=> $betId,
				'cols'	=> $cols
			);
		}
		
		if(is_array($betIds))			
			return $ordBetCols;
		else {
			$columns = reset($betColumns);
			if (is_array($columnId))
				return $columns;
			else if (isset($columns[$columnId]))
				return $columns[$columnId];
			else
				return null;
		}
	}

	/**
	 * Returns merged bet changelog, returned structure(s) will have WS bet fields.
	 * @see It6_Models_BetChangelog::getMergedChanges()
	 * @param integer|array $betId
	 * @param integer|string $from
	 * @param integer|string $to
	 * @return array|struct
	 */
	public static function getMergedChanges($betId, $from, $to = null) {
		static $fieldMap = array(
			'validFrom' => 'validFromTime',
			'validTo' => 'validToTime',
			'text' => 'name',
			'ticketText' => 'ticketName',
		);
		$db = static::getMainDb();
		return It6_Models_BetChangelog::getMergedChanges($betId, $from, $to, $fieldMap, $db);
	}

	/**
	 * Returns merged bet changelog(s), returned structure(s) will have WS bet fields.
	 * @see It6_Models_BetChangelog::getMergedChanges()
	 * @param struct $betIds map (bet ID => afterId) 
	 * @return array|struct
	 */
	public static function getMergedChangesAfterId($betIds) {
		static $fieldMap = array(
				'validFrom' => 'validFromTime',
				'validTo' => 'validToTime',
				'text' => 'name',
				'ticketText' => 'ticketName',
		);
		$db = static::getMainDb();
		return It6_Models_BetChangelog::getMergedChangesAfterId($betIds, $fieldMap, $db);
	}
	
	public static function getBetTips($betParentId, $langId = 1) {
		$select = static::getDb()->select()
				->from('sazky', array('sazky.sazka_id as betId',
					'IF(sazky.ticket_text IS NULL OR sazky.ticket_text=\'\',sazky.text,sazky.ticket_text) as name',
					'text_note as betNote'))
				->join('typ', 'typ.typ_id = sazky.typ_id', array('TRANSLATE(typ.nazev,'.$langId.') as typeName'))
				->where('parent_id = '.$betParentId.' OR sazky.sazka_id = '.$betParentId)
				->where('platna_do >= \''.It6_Date::dbNow().'\'')
				->order('sazka_id');
		 
		return $select->query()->fetchAll();
	}
	
	public static function getOddsByBetId($betId) {
		return static::getDb()->select()
				->from('sazka_kurz_aktualni')
				->join('podtyp_sloupce', 'sazka_kurz_aktualni.sloupec_id = podtyp_sloupce.sloupec_id')
				->where('sazka_id = ?', $betId)
				->where('sazka_kurz_aktualni.kurz > 1')
				->query()->fetchAll();
	}
	
	public static function getSportRegionEventUrl($betId, $langId) {
		$bet = It6_ArrayWrapper::toNativeArray(self::getById($betId));		
		
		$sportUrl = Zend_Registry::get('ws')->SeoUrl->getByPrimaryKey(Webservice_SeoUrl::TYPE_SPORT, $bet['sportId'], $langId);
		$regionUrl = Zend_Registry::get('ws')->SeoUrl->getByPrimaryKey(Webservice_SeoUrl::TYPE_REGION, $bet['regionId'], $langId);
		$eventUrl = Zend_Registry::get('ws')->SeoUrl->getByPrimaryKey(Webservice_SeoUrl::TYPE_EVENT, $bet['eventId'], $langId);
		
		return "$sportUrl/$regionUrl/$eventUrl";
	}
}
