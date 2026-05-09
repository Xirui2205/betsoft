<?php

/**
 * Event related static methods. Event is trournament etc.
 * @author Pavel Klinger
 * @see Entities_Event
 *
 */
class Webservice_Event extends Webservice_AbstractWebService {

	const SEO_URL_TYPE = 3;

	public static $TABLE = "udalost";
	public static $TABLE_PREFIX = "ev";
	public static $IDENTITY = "udalost_id";
	public static $BETRADAR_EVENT_TABLE = 'udalost_betradar';
	public static $SEO_URL_TABLE = 'seo_url';
	public static $TYPE_EVENT_TABLE = 'typ_udalost';
	public static $TYPE_EVENT_PREFIX = 'tu';
	public static $TYPE_SUBTYP_TABLE = 'typ_podtyp';
	public static $TYPE_SUBTYP_PREFIX = 'ts';

	protected static $ENTITY_NAME = "Entities_Event";
	protected static $CONV = array(
		'ev.udalost_id' => 'eventId',
		'ev.sport_id' => 'sportId',
		'ev.oblast_id' => 'regionId',
		//TODO Add multi languiages support
		'TRANSLATE(ev.nazev,1)' => 'name', // 1 .. CZ
		'ev.nazev' => 'key',
		'ev.pozice' => 'navigationOrder',
		'ev.zvyrazneni' => 'navigationHighlight',
		'ev.zobrazeno' => 'navigationVisible',
		'ev.oddeleni' => 'navigationDelimiter',
		'ev.betradar_udalost_id' => 'betradarId',
		'ev.betradar_time_offset' => 'betradarTimeOffset',
		'ev.platne_od' => 'validFromTime',
		'ev.platne_do' => 'validToTime',
		'ev.approval_group_id' => 'approvalGroupId',
		'ev.bet_alias_from' => 'betAliasFrom',
		'ev.bet_alias_to' => 'betAliasTo',
		'ev.pozice_offergen' => 'navigationOrderOffergen',

		'sp.nazev' => 'sportName',
		'tr.preklad_id' => 'translationId',
		//TODO Add multi languiages support
		'TRANSLATE(rg.nazev, 1)' => 'regionName', // 1 .. CZ
	);


	protected static function defaultJoins($query) {

		$query = parent::defaultJoins($query);
		$query
			->join(
				array(Webservice_Sport::$TABLE_PREFIX => Webservice_Sport::$TABLE),
				self::$TABLE_PREFIX.'.sport_id = '.Webservice_Sport::$TABLE_PREFIX.'.sport_id',
				null
			)
			->joinLeft(
				array(Webservice_Translate::$TABLE_PREFIX => Webservice_Translate::$TABLE),
				self::$TABLE_PREFIX.'.nazev = '.Webservice_Translate::$TABLE_PREFIX.'.index_pole AND lang_id = '.DEFAULT_LANG_ID,
				null
			)
			->join(
				array(Webservice_Region::$TABLE_PREFIX => Webservice_Region::$TABLE),
				self::$TABLE_PREFIX.'.oblast_id = '.Webservice_Region::$TABLE_PREFIX.'.oblast_id',
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
		return parent::getAll($extensions);
	}

	/**
	 * Returns all events in the system with given sport
	 * @return array array of the event structures
	 * @see Entities_Event
	 */
	public static function getAllBySport($sportId, $extensions = null) {
		return parent::getAllWhere(array('sportId = ?' => $sportId), $extensions);
	}

	/**
	 * Return all events by given Betradar ID(s) and other contraints
	 * @param integer|array $betradarId One or more betradar IDs
	 * @param array $where Standard WHERE constraints
	 * @return array Array of Entities_Event
	 */
	public static function getAllWhereAndByBetradarId($betradarId, $where, $extensions = null) {
		$class = get_called_class();
		$hooks = array();
		$hooks['joins2'] = function($select) use ($class) {
			return $select->join(
				array('evbr' => $class::$BETRADAR_EVENT_TABLE),
				'evbr.udalost_id=ev.udalost_id',
				array()
			)
			->group('ev.udalost_id');
		};
		return parent::getAllWhereInjected(
			array_merge($where, array('evbr.betradar_udalost_id IN (?)' => $betradarId)),
			$hooks,
			$extensions
		);
	}
	
	/**
	 * Find event by given identifier.
	 * @param integer $eventId identifier of the event
	 * @return struct event structure
	 * @see Entities_Event
	 */
	public static function getById($eventId, $extensions = null) {
		return parent::getById($eventId, $extensions);
	}

	/**
	 * Find event by given handle
	 * @param string $handle
	 * @return struct event structure
	 * @see Entities_Event
	 * @see Entities_Event#handle
	 */
	public static function getByHandle($handle, $extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * @param struct $event BR event ID will be read from structure and then updated to valid value
     * @return array List of BR event IDs that should be used for event-BRID mapping table, can be empty
	 */
	private static function fixBetradarId(&$event) {
		if (empty($event['betradarId']))
			$betradarIds = array();
		else
			$betradarIds = (!is_array($event['betradarId']) ? array($event['betradarId']) : $event['betradarId']);
		$betradarIds = array_filter($betradarIds, function($id) { return !empty($id); });
		if ( !empty($event['betradarId']) && is_array($event['betradarId']) )
			$event['betradarId'] = current($event['betradarId']);
		if (empty($event['betradarId']))
			$event['betradarId'] = new Zend_Db_Expr('NULL');
		return $betradarIds;
	}

	/**
	 * Insert new event. Value of the event identifier is ignored and new
	 * is generated.
	 * @param struct $event structure of the event
	 * @return integer event identifier of the created event
	 * @see Entities_Event
	 */
	public static function insert($event) {
		$db = static::getDb();
		
		$_ = static::getAllWhere(array('key = ?' => $event['key']));
		if ( count($_) > 0 ) {			
			throw new It6_XmlRpc_Exception("Event of the name '" . $event['key'] . "' already exists.");
		}
		
		if ( !empty($event['name']) )
			$primaryTrans = $event['name'];
		unset($event['name']);
		$betradarIds = static::fixBetradarId($event);

		self::modifyNavigationOrder($event);
		It6_DbTransaction::begin($db);

		try {
			$languages = Webservice_Language::getAllActive();
			
			if ( isset($primaryTrans) ) {
				
				foreach($languages as $language) {
					$translation = array(
						'index'			=> $event['key'],
						'langId'		=> $language->languageId,
					);
					if($language->languageId == DEFAULT_LANG_ID)
						$translation['text'] = $primaryTrans;
	
					Webservice_Translate::insert($translation);
				}
	
			}
			
			unset($event['translationId']);
			if ( !isset($event['navigationVisible']))
				$event['navigationVisible'] = 1;

			$event['eventId'] = parent::insert($event);

			if ( isset($event['seoUrl'.$languages[0]['languageId']]) ) {
				foreach($languages as $language) {
					$urlKey				= 'seoUrl' . $language['languageId'];
					$seoUrl				= array();
					$seoUrl['url']		= $event[$urlKey];
					$seoUrl['langId']	= $language['languageId'];
					$seoUrl['objectId']	= $event['eventId'];
					Webservice_SeoUrl_Event::insert($seoUrl);
					unset($event[$urlKey]);
				}
			}

			foreach( $betradarIds as $betradarId ) {
				$db->insert(
						static::$BETRADAR_EVENT_TABLE,
						array(
							'udalost_id' => $event['eventId'],
							'betradar_udalost_id' => $betradarId));
			}

			It6_DbTransaction::commit($db);
		}
		
		
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not insert event.", 0, $e);
		}
		
		It6_GlobalCache_Invalidator::invalidateSportsbook($event['sportId'], $event['regionId'], $event['eventId']);
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
		return $event['eventId'];
	}

	/**
	 * Update event.
	 * @param struct $event structure of the event
	 * @return true on success
	 * @see Entities_Event
	 */
	public static function update($event, $updateOrderOnly=null) {
		if ( empty($event['eventId']) )
			return static::insert($event);

		$db = static::getDb();
		if ( !empty($event['name']) )
			$primaryTrans = $event['name'];
		unset($event['name']);
		$betradarIds = static::fixBetradarId($event);
		if (isset($event['betradarTimeOffset'])) {
			if (!ctype_digit($event['betradarTimeOffset'])) {
				$event['betradarTimeOffset'] = new Zend_Db_Expr('NULL');
			}
		}

		It6_DbTransaction::begin($db);
		try {
			if($updateOrderOnly !== true) {
				$languages = Webservice_Language::getAllActive();
				
				if ( isset($event['seoUrl'.$languages[0]['languageId']]) ) {
					foreach($languages as $language) {
						$urlKey				= 'seoUrl' . $language['languageId'];
						$seoUrl				= array();
						$seoUrl['url']		= $event[$urlKey];
						$seoUrl['langId']	= $language['languageId'];
						$seoUrl['objectId']	= $event['eventId'];
						Webservice_SeoUrl_Event::update($seoUrl);
						unset($event[$urlKey]);
					}
				}
				
				if ( isset($primaryTrans) ) {
					foreach($languages as $language) {
						$translation = array(
							'index'			=> $event['key'],
							'langId'		=> $language->languageId,
							'translationId'	=> $event['translationId']
						);
						if($language->languageId == DEFAULT_LANG_ID)
							$translation['text'] = $primaryTrans;
		
						Webservice_Translate::update($translation);
					}
				}
				unset($event['translationId']);
				

				$db->delete(
					static::$BETRADAR_EVENT_TABLE,
					array('udalost_id = ?' => $event['eventId']));

				foreach( $betradarIds as $betradarId ) {
					$db->insert(
							static::$BETRADAR_EVENT_TABLE,
							array(
								'udalost_id' => $event['eventId'],
								'betradar_udalost_id' => $betradarId));
				}
			}

			$eventTmp = $event;
			$eventTmp['navigationOrder']			= 0;
			$eventTmp['navigationOrderOffergen']	= 0;

			parent::update($eventTmp);
			self::modifyNavigationOrder($event);
			parent::update($event);

			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update event.", 0, $e);
		}

		It6_GlobalCache_Invalidator::invalidateSportsbook($event['sportId'], $event['regionId'], $event['eventId']);
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
		return $event['eventId'];
	}


	/**
	 * Delete event.
	 * @param struct $eventId idenetifier of the event.
	 * @return true on success
	 * @see Entities_Event
	 */
	public static function delete($eventIds) {
		$db = static::getDb();

		It6_DbTransaction::begin($db);
		
		try {
			$db->delete(
					static::$BETRADAR_EVENT_TABLE,
					array('udalost_id IN (?)' => implode(',', $eventIds)));

			$sql = '
				DELETE `'.Webservice_Translate::$TABLE_PREFIX.'`, `'.self::$TABLE_PREFIX.'`
					FROM `'.self::$TABLE.'` AS `'.self::$TABLE_PREFIX.'`
				LEFT JOIN `'.Webservice_Translate::$TABLE.'` AS `'.Webservice_Translate::$TABLE_PREFIX.'`
					ON `'.Webservice_Translate::$TABLE_PREFIX.'`.`index_pole` = `'.self::$TABLE_PREFIX.'`.`nazev`
				WHERE `'.self::$TABLE_PREFIX.'`.`udalost_id` IN ('.implode(',', $eventIds).');
			';

			$res = $db->query($sql);
			$res = $res->execute();

			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update event.", 0, $e);
		}
		It6_GlobalCache_Invalidator::invalidateSportsbook($event['sportId'], $event['regionId'], $event['eventId']);
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
		return true;
	}

	/**
	 * Sets "valid to" to now datetime for given event(s).
	 * This can be used as "soft delete" for event(s).
	 * @param integer|array $eventId One event ID or list of event IDs
	 * @return boolean TRUE on success, FALSE on error 
	 */
	public static function cancelValidity($eventId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE,
				array('platne_do' => It6_Date::dbNow()),
				array('udalost_id IN (?)' => $eventId)
			);
			It6_DbTransaction::commit($db);
			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			It6_Log::warn('Cannot delete event(s).', array('eventId' => $eventId), $e);
			return false;
		}
	}

	public static function toEntity($event, $columns = null) {
		$db = static::getDb();

		$ret = parent::toEntity($event, $columns);

		$query = $db->select()
			->from(static::$BETRADAR_EVENT_TABLE,null)
			->columns('betradar_udalost_id')
			->where('udalost_id = ?',$event['eventId'])
			->query();
		
		
		$tmp = array();
		while ( $row = $query->fetchObject() )
			$tmp[] = $row->betradar_udalost_id;
			
		$ret['betradarId'] = $tmp;
		
		return $ret;

	}
	

	public static function show($eventIds) {
		$db = static::getDb();

		It6_DbTransaction::begin($db);
		try {
			$db->update(self::$TABLE, array('zobrazeno' => 1), array('udalost_id IN (?)' => $eventIds));

			It6_DbTransaction::commit($db);
			return true;
		}

		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update event.", 0, $e);
		}
	}


	public static function hide($eventIds) {
		$db = static::getDb();

		It6_DbTransaction::begin($db);
		try {
			$db->update(self::$TABLE, array('zobrazeno' => 0), array('udalost_id IN (?)' => $eventIds));

			It6_DbTransaction::commit($db);
			return true;
		}

		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update event.", 0, $e);
		}
	}



	public static function getRelevantQuery($query) {
		return $query->join(
			array('bt' => Webservice_Bet::$TABLE),
			"bt.udalost_id = ev.udalost_id AND bt.platna_od < '"
				. It6_Date::dbNow() . "' AND bt.platna_do > '"
				. It6_Date::dbNow() . "' AND bt.status = 0"
				. ' AND bt.live = 0'
				. ' AND bt.risk_limit > bt.risk_limit_balance'
				. ' AND ev.zobrazeno = 1'
				. " AND ev.platne_od < '" . It6_Date::dbNow()
				. "' AND ev.platne_do > '" . It6_Date::dbNow() . "'",
			null);
	}

	/**
	 * Returns all events where can be done bets.
	 * @return array array of the event structures
	 * @see Entities_Event
	 */
	public static function getRelevant() {
		try {
			$select = static::getRelevantQuery(
					static::defaultQuery( static::getDb()->select() ))
				->where('ev.zobrazeno = 1')
				->group('bt.udalost_id')
				->having('COUNT(bt.sazka_id) > 0');
			return static::fetchAllEntities($select->query());
		}
		catch ( Exception $e) {
			throw new It6_XmlRpc_Exception('getRelevant', 0, $e);
		}
	}

	/**
	 * Returns all bets related to the given event
	 * @param int $eventId identifier of the event
	 * @return array array of bet structures
	 * @see Entities_Bet
	 */
	public static function getAllBets($eventId, $extensions = null) {
		return Webservice_Bet::getAllWhere(
			array('eventId = ?' => $eventId),
			$extensions);
	}

	/**
	 * Returns current bets related to the given event
	 * @param int $eventId identifier of the event
	 * @return array array of the bet structures
	 * @see Entities_Bet
	 */
	public static function getOpenedBets($eventId, $extensions = null) {
		return Webservice_Bet::getAllWhere(
			array(
				'eventId = ?' => $eventId,
				'validFromTime < ?' => It6_Date::dbNow(),
				'validToTime > ?' => It6_Date::dbNow()),
				'riskLimit > riskLimitBalance',
				'status = 0',
			$extensions);
	}

	/**
	 * Returns current main bets related to the given event
	 * @param int $eventId identifier of the event
	 * @return array array of the bet structures
	 * @see Entities_Bet
	 */
	public static function getOpenedMainBets($eventId, $extensions = null) {
		return Webservice_Bet::getAllWhere(
			array(
				'eventId = ?' => $eventId,
				'validFromTime < ?' => It6_Date::dbNow(),
				'validToTime > ?' => It6_Date::dbNow(),
				'riskLimit > riskLimitBalance',
				'parentId IS NULL',
				'status = 0'),
			$extensions);
	}

	/**
	 * Returns not current bets related to the given event
	 * @param int $eventId identifier of the event
	 * @return array array of the bet structures
	 * @see Entities_Bet
	 */
	public static function getClosedBets($eventId, $extensions = null) {
		return Webservice_Bet::getAllWhere(
			array(
				'eventId = ?' => $eventId,
				'validFromTime > ?' => It6_Date::dbNow(),
				'validToTime < ?' => It6_Date::dbNow()),
			$extensions);
	}

	/**
	 * Returns not current bets related to the given event
	 * @param int $eventId identifier of the event
	 * @return array array of the bet structures
	 * @see Entities_Bet
	 */
	public static function getClosedMainBets($eventId) {
		return Webservice_Bet::getAllWhere(
			array(
				'eventId = ?' => $eventId,
				'validFromTime > ?' => It6_Date::dbNow(),
				'validToTime < ?' => It6_Date::dbNow(),
				'parentId IS NULL'),
			$extensions);
	}

	/**
	 * Returns all bet types of all opened bets.
	 * @param int $eventId identifier of the event
	 * @return array array of the bet type structures
	 * @see Entities_BetType
	 */
	public static function getOpenedBetTypes($eventId) {
		$db = static::getDb();

		$betTypes = $db->select()->from(array('s' => Webservice_Bet::$TABLE), array())
				->join(array('t' => "typ"),'s.typ_id = t.typ_id AND t.zobrazeno = 1')
				->where('s.platna_od < NOW()')
				->where('s.platna_do > NOW()')
				->where('s.risk_limit > s.risk_limit_balance')
				->where('status = 0')
				->where('s.udalost_id = ?', $eventId)
				->group('t.typ_id')
				->query()->fetchAll();

		return Webservice_Event::toBetTypeEntities($betTypes);
	}

	/**
	 * Returns all bet types of all opened bets in given event (names are translated to given language).
	 * @param int $eventId identifier of the event
	 * @param integer $langId identifier of the language (cz=1, en=2, sk=16)
	 * @return array array of the bet type structures
	 * @see Entities_BetType
	 */
	public static function getOpenedBetTypesTranslated($eventId, $langId) {
		$db = static::getDb();

		$betTypes = $db->select()->from(array('s' => Webservice_Bet::$TABLE), array())
				->join(
					array('t' => "typ"),
					's.typ_id = t.typ_id AND t.zobrazeno = 1',
					array('t.typ_id', 'nazev' => new Zend_Db_Expr('COALESCE(p.text, t.nazev)'))
				)
				->joinLeft(
					array('p' => 'preklady'),
					't.nazev=p.index_pole AND p.lang_id='.intval($langId),
					array()
				)
				->where('s.platna_od < NOW()')
				->where('s.platna_do > NOW()')
				->where('s.risk_limit > s.risk_limit_balance')
				->where('status = 0')
				->where('s.udalost_id = ?', $eventId)
				->group('t.typ_id')
				->query()->fetchAll();

		return Webservice_Event::toBetTypeEntities($betTypes);
	}

	/**
	 * Returns all opened bets with given event type
	 * @param int $eventId
	 * @param int $betTypeId
	 * @return array array of the bet structures
	 * @see Entities_Bet
	 */
	public static function getOpenedBetsByType($eventId, $betTypeId) {
		$now = It6_Date::dbNow();
		return Webservice_Bet::getAllWhere(array(
			'eventId = ?' => $eventId,
			'typeId = ?' => $betTypeId,
			'validFromTime < ?' => $now,
			'validToTime > ?' => $now,
			'riskLimit > riskLimitBalance',
			'status = 0'));
	}


	public static function toBetTypeEntities($betTypes) {
		$ret = array();

		foreach ( $betTypes as $betType ) {
			$ret[] = Webservice_Event::toBetTypeEntity($betType);
		}
		return $ret;
	}

	public static function toBetTypeEntity($betType) {

		$ret = new Entities_BetType();

		$ret->betTypeId = $betType['typ_id'];
		$ret->name = $betType['nazev'];

		return $ret;
	}

	/**
	 * Set approval event Id for
	 * @param array $eventIds identifier of the sports
	 * @param int $groupId identifier of the approval group
	 * @return bool
	 */
	public static function setApprovalGroupId($eventIds, $groupId) {
		try {
			$data = array('approval_group_id' => $groupId);
			$where = array();
			$where[static::$IDENTITY.' IN (?)'] = $eventIds;
			$res = static::getDb()->update(self::$TABLE, $data, $where);
			return true;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("toEntity.", 0, $e);
		}
	}

	/**
	 * Returns data for event filter
	 * @param integer|NULL $sportId The sport determining returned events, NULL for all sports
	 * @param integer|NULL $languageId Language for names translation, NULL for no translation
	 * @param string $eventValueCol Name of the column to be added to the defualt list of selected columns - useful if we need the filter to return other value then eventId
	 * @return array
	 *    [
	 *       [ sport_1_id, sport_1_name,
	 *          [ region_1_id, region_1_name,
	 *             [ [ event_id, event_name], ... ],
	 *          ],
	 *          [ region_2_id, ... ],
	 *          ...
	 *       ],
	 *       [ sport_2_id, ... ],
	 *       ...
	 *    ]
	 */
	public static function getSportFilterEvents($sportId=null, $languageId=null, $eventValueCol=null, $onlyValid = true) {
		$db			= static::getDb();
		$sports		= array();
		$sportId	= intval($sportId);
		$languageId	= intval($languageId);
		$translate	= !empty($languageId);
		$collation	= '';
		$eventCols	= array('eventId','key');
		if(!empty($eventValueCol))
			$eventCols[] = $eventValueCol;

		$eventColsSql = array();
		foreach($eventCols as $col) {
			$eventColsSql[$col] = array_search($col, self::$CONV);
		}

		if (!empty($languageId)) {
			$collation = It6_Models_Language::get($languageId, 'collation', $db);
			$collation = (empty($collation) ? '' : " COLLATE $collation");
		}
		
		
		$select = $db->select()
			->from(
				array('s' => 'sport'),
				array('sportId' => 'sport_id', 'sName' => 'nazev'));
				
		if ($translate) {
			$select->joinLeft(
				array('ts' => 'preklady'),
				"ts.index_pole=s.nazev AND ts.lang_id=$languageId",
				array('tsName' => 'text'));
		}
		
		$validEventsSql = $onlyValid ? ' AND (ev.platne_od <= \'' . It6_Date::dbNow() . "' AND " . 'ev.platne_do >=\'' . It6_Date::dbNow() ."')": "";

		$select->joinLeft(
			array(self::$TABLE_PREFIX => 'udalost'),
			's.sport_id='.self::$TABLE_PREFIX.'.sport_id' . (empty($sportId) ? '' : $db->quoteInto(' AND '.self::$TABLE_PREFIX.'.sport_id IN (?)', $sportId)) . $validEventsSql,
			$eventColsSql);
			
		if ($translate) {
			$select->joinLeft(
				array('te' => 'preklady'),
				"te.index_pole=".self::$TABLE_PREFIX.".nazev AND te.lang_id=$languageId",
				array('teName' => 'text'));
		}
		
		$select->joinLeft(
			array('r' => 'oblast'),
			self::$TABLE_PREFIX.'.oblast_id=r.oblast_id',
			array('regionId' => 'oblast_id', 'rName' => 'nazev'));
			
		if ($translate) {
			$select->joinLeft(
				array('tr' => 'preklady'),
				"tr.index_pole=r.nazev AND tr.lang_id=$languageId",
				array('trName' => 'text'));
		}
				
		$select->order(
			!$translate
			? array("s.nazev$collation", "r.nazev$collation", self::$TABLE_PREFIX.".nazev$collation")
			: array(
				"(TRIM(COALESCE(ts.text, s.nazev)))$collation",
				"(TRIM(COALESCE(tr.text, r.nazev)))$collation",
				"(TRIM(COALESCE(te.text, ".self::$TABLE_PREFIX.".nazev)))$collation"
			) 
		);
				
//var_dump($select->assemble());exit;
		$res = $select->query();
		$sportId = null;
		$regionId = null;
		while ($row = $res->fetch()) {
			if ($row['sportId'] != $sportId) {
				$sportId = $row['sportId'];
				$sports[] = array($sportId, (empty($row['tsName']) ? $row['sName'] : $row['tsName']), array());
				$regions = &$sports[ count($sports) - 1 ][2];
				$regionId = null;
			}
			if (!empty($row['eventId'])) {
				if ($row['regionId'] != $regionId) {
					$regionId = $row['regionId'];
					$regions[] = array($regionId, (empty($row['trName']) ? $row['rName'] : $row['trName']), array());
					$events = &$regions[ count($regions) - 1 ][2];
				}

				foreach($eventCols as $col) {
					$eventDetail[$col] = $row[$col];
				}
				$eventDetail['name']	= (empty($row['teName']) ? $row['key'] : $row['teName']);
				$events[] = $eventDetail;
			}
		}

		return $sports;
	}
	
	/**
	 * Returns array of seo urls for given eventId
	 * @param integer $eventId 
	 * @return struct associative array of urls lang_id => url
	 */
	public static function getSeoUrl($eventId) {
		$db = static::getDb();
		$ret = $db->select()
			->from(static::$SEO_URL_TABLE,array('lang_id','url'))
			->where('type = ?',static::SEO_URL_TYPE)
			->where('event_id = ?',$eventId)
			->query()
			->fetchAll();

		return It6_ArrayWrapper::toAssocArray($ret,'lang_id','%url%');
	}
	
	/**
	 * Updates all events with higher navigation and navigationOffergen to
	 * accomodate for the values passed in with the event
	 * @param strucy $event
	 * @return boolean
	 */
	 private static function modifyNavigationOrder(&$event) {
		//can not use self::$CONV as the "ev." in col name messes it up.
		//Can not remove "ev." as it causes errors with FOP
		$db = static::getDb();
		
		
		if(empty($event['navigationOrder']) || empty($event['navigationOrderOffergen'])) {
			$dbRes = $db->select()
				->from(
					array(static::$TABLE_PREFIX => static::$TABLE),
					array(
						'maxNavOrder'			=> 'MAX(pozice)',
						'maxNavOrderOffergen'	=> 'MAX(pozice_offergen)',
					)
				)
				->query()->fetch();
				
			$maxNavOrder			= $dbRes['maxNavOrder'];
			$maxNavOrderOffergen	= $dbRes['maxNavOrderOffergen'];
			$emptyOrdering			= true;
		}

		if(empty($event['navigationOrder']))
			$event['navigationOrder'] = $maxNavOrder + 1;
		else
			$emptyOrdering = false;

		if(empty($event['navigationOrderOffergen']))
			$event['navigationOrderOffergen'] = $maxNavOrderOffergen + 1;
		else
			$emptyOrdering = false;


		if($emptyOrdering == false) {
			It6_DbTransaction::begin($db);
			
			try {
				$allPositions = $db->select()
					->from(
						array(static::$TABLE_PREFIX => static::$TABLE),
						array(
							'navigationOrder'			=> 'pozice',
							'navigationOrderOffergen'	=> 'pozice_offergen',
							'eventId'					=> 'udalost_id',
						)
					)
					->where('pozice >= ?', $event['navigationOrder'])
					->orWhere('pozice_offergen >= ?', $event['navigationOrderOffergen'])
					->query()->fetchAll();

				$allNavOrder			= array();
				$allNavOrderOffergen	= array();
				
				foreach($allPositions as $eventPositions) {
					if(
						$eventPositions['navigationOrder'] >= $event['navigationOrder']
						&& $eventPositions['eventId'] != $event['eventId']
					)
						$allNavOrder[$eventPositions['eventId']] = $eventPositions['navigationOrder'];
					if(
						$eventPositions['navigationOrderOffergen'] >= $event['navigationOrderOffergen']
						&& $eventPositions['eventId'] != $event['eventId']
					)
						$allNavOrderOffergen[$eventPositions['eventId']] = $eventPositions['navigationOrderOffergen'];
				}


				if(!empty($allNavOrder) && in_array($event['navigationOrder'], $allNavOrder)) {
					$db->query(
						'UPDATE '.self::$TABLE.'
						SET pozice = pozice + 1
						WHERE pozice IN ('.implode(',',$allNavOrder).')
						ORDER BY pozice DESC'
					);
				}
				if(!empty($allNavOrderOffergen) && in_array($event['navigationOrderOffergen'], $allNavOrderOffergen)) {
					$db->query(
						'UPDATE '.self::$TABLE.'
						SET pozice_offergen = pozice_offergen + 1
						WHERE pozice_offergen IN ('.implode(',',$allNavOrderOffergen).')
						ORDER BY pozice_offergen DESC'
					);
				}
				
				It6_DbTransaction::commit($db);
			}
			
			
			catch (Exception $e) {
				It6_DbTransaction::rollback($db);
				throw new It6_XmlRpc_Exception("Can not correctly set navigation order.", 0, $e);
			}
		}
	}

	/**
	 * @param integer|array $betradarId One or more event betradar IDs
	 * @param integer|array|NULL $eventId One or more event BBAS IDs to exclude, can be empty
	 * @return integer|array Count for one passed ID, array(ID => count) for more passed IDs
 	 */
	public static function getBetradarIdEventCount($betradarId, $eventId) {
	 	$select = static::getDb()->select()
	 		->from(static::$BETRADAR_EVENT_TABLE, array(
	 			'brId' => 'betradar_udalost_id',
	 			'c' => new Zend_Db_Expr('COUNT(udalost_id)')
	 		))
	 		->where('betradar_udalost_id IN (?)', $betradarId)
	 		->group('betradar_udalost_id');
	 	if (!empty($eventId))
	 		$select->where('udalost_id NOT IN (?)', $eventId);
		$rows = $select->query()->fetchAll();
	 	$more = It6_ArrayWrapper::isArray($betradarId);
	 	$ids = ($more ? $betradarId : array($betradarId));
	 	$result = array();
	 	foreach ($ids as $id)
	 		$result[$id] = 0;
	 	foreach ($rows as $row) {
	 		$result[$row['brId']] = intval($row['c']);
	 	}
	 	return ($more ? $result : $result[$betradarId]);
	}


	/**
	 * Returns all events with data about type binding
	 * @param int $eventId id of the event
	 * @return struct of structures representing the individual types. Each structure has following keys
	 */
	public static function getTypeEventsByEventOrSportId($eventId = null, $sportId = null, $limit = null) {
		$dbRes = static::getDb()->select()
			->from(
				array(Webservice_Bet::$TABLE_PREFIX => Webservice_Bet::$TABLE),
				null
			)
			->join(
				array(Webservice_Type::$TABLE_PREFIX => Webservice_Type::$TABLE),
				Webservice_Type::$TABLE_PREFIX.'.typ_id = '.Webservice_Bet::$TABLE_PREFIX.'.typ_id',
				array(
					'typeId' => Webservice_Type::$TABLE_PREFIX.'.typ_id', 
					'typeName' => Webservice_Type::$TABLE_PREFIX.'.nazev'
				)
			)
			->join(
				array(static::$TABLE_PREFIX => static::$TABLE),
				static::$TABLE_PREFIX.'.udalost_id = '.Webservice_Bet::$TABLE_PREFIX.'.udalost_id',
				null
			)
			->join(
				array(static::$TYPE_EVENT_PREFIX => static::$TYPE_EVENT_TABLE),
				Webservice_Type::$TABLE_PREFIX.'.typ_id = '.static::$TYPE_EVENT_PREFIX.'.typ_id',
				null
			)
			->where(Webservice_Bet::$TABLE_PREFIX.'.live = 0')
			->where(Webservice_Bet::$TABLE_PREFIX.'.status = 0')
			->where(Webservice_Bet::$TABLE_PREFIX.'.risk_limit > '.Webservice_Bet::$TABLE_PREFIX.'.risk_limit_balance')
			->where(static::$TYPE_EVENT_PREFIX.'.is_binded = 1');

		if (!empty($eventId)) $dbRes = $dbRes->where(static::$TYPE_EVENT_PREFIX.'.udalost_id = ?', $eventId);
		if (!empty($sportId)) $dbRes = $dbRes->where(static::$TABLE_PREFIX.'.sport_id = ?', $sportId);

		$dbRes = $dbRes->group(array(Webservice_Type::$TABLE_PREFIX.'.typ_id'));
		$dbRes = $dbRes->order(array(Webservice_Type::$TABLE_PREFIX.'.typ_id ASC'));
		if (!empty($limit)) $dbRes = $dbRes->limit($limit);

		$dbRes = $dbRes->query()->fetchAll();

		$typeEvents = array();
		foreach($dbRes as $row) {
			$typeEvents[$row['typeId']] = $row;
		}
		return $typeEvents;
	}


	/**
	 * Returns all events with data about type binding
	 * @param int $eventId id of the event
	 * @return struct of structures representing the individual types. Each structure has following keys
	 * <ul>
	 * <li>typeId</li>
	 *   <ul>
	 *     <li>isBinded</li>
	 *     <li>order</li>
	 *     <li>isDefault</li>
	 *   </ul>
	 * </ul>
	 */
	public static function getTypeEventsByEventId($eventId) {
		$dbRes = static::getDb()->select()
			->from(
				array(Webservice_Type::$TABLE_PREFIX => Webservice_Type::$TABLE),
				array(
					'typeId' => 'typ_id',
				)
			)
			->join(
				array(static::$TYPE_EVENT_PREFIX => static::$TYPE_EVENT_TABLE),
				Webservice_Type::$TABLE_PREFIX.' .typ_id = '.static::$TYPE_EVENT_PREFIX.' . typ_id',
				array(
					'isDefault' => 'is_default',
					'order' => 'order'
				)
			)
			->where(static::$TYPE_EVENT_PREFIX.'.udalost_id = ?', $eventId)
			->where(static::$TYPE_EVENT_PREFIX.'.is_binded = 1')
			->query()->fetchAll();
		
		$typeEvents = array();
		foreach($dbRes as $row) {
			$typeEvents[$row['typeId']] = $row;
		}
		
		return $typeEvents;
	}
	
	
	
	public static function updateTypeEvent($valuesRaw, $applyToSport=null) {
		$db = static::getDb();
		if(empty($applyToSport)) {
			$eventIds = array($valuesRaw['eventId']);
		}
		else {
			$events = static::getDb()->select()
				->from(
					array(self::$TABLE_PREFIX => self::$TABLE),
					array('udalost_id')
				)
				->join(
					array(self::$TABLE_PREFIX.'1' => self::$TABLE),
					self::$TABLE_PREFIX.'.sport_id = '.self::$TABLE_PREFIX.'1'.'.sport_id',
					array()
				)
				->where(self::$TABLE_PREFIX.'1.udalost_id = ?', $valuesRaw['eventId'])
				->query()->fetchAll();

			$eventIds = array_map(function($arr) {return $arr['udalost_id'];}, $events);
		}

		unset($valuesRaw['eventId']);
		$values = array();
		foreach($valuesRaw as $key => $val) {
			$keyArr = explode('_', $key);
			$field = $keyArr[0];
			$typeId = $keyArr[1];
		
			$values[$typeId][$field] = $val;
		}	
		


		try {

			
			
			foreach($values as $typeId => $type) {
				$sqlVals = array();
				$sqlPlcHolders = array();
				$sqlValsType = array();

				foreach($eventIds as $eventId) {
					if(empty($type['order'])) {
						$type['order'] = 0;
					}
					$sqlValsType[] = $type['isBinded'];
					$sqlValsType[] = $type['isDefault'];
					$sqlValsType[] = $type['order'];
					$sqlValsType[] = $typeId;
					$sqlValsType[] = $eventId;
					$sqlPlcHolders[] = '(?,?,?,?,?)';
				}
				$sqlVals[] = $sqlValsType;
				$sqlValsType = array();


				$plHlds = implode(',',$sqlPlcHolders);
				$sqlStmnt = $db->prepare(
						'REPLACE INTO '.self::$TYPE_EVENT_TABLE.'(`is_binded`, `is_default`, `order`, `typ_id`, `udalost_id`)
						VALUES '.$plHlds.';'
						);
				$sqlValsArr = array();
				foreach($sqlVals as $sqlRowVals) {
					foreach ($sqlRowVals as $sqlColVal) {
						$sqlValsArr[] = $sqlColVal;
					}
				}
				
				$sqlStmnt->execute($sqlValsArr);

			}
			return true;
		}
		catch(Exception $e) {
			throw new Exception($e);
			return false;
		}
	}
}
