<?php

/**
 * BetType related static methods.
 * @author Pavel Klinger
 * @see Entities_BetType
 *
 */
class Webservice_BetType extends Webservice_AbstractWebService  {		
	
	public static $TABLE = "typ";
	public static $TABLE_PREFIX = "bt";
	public static $ENTITY_NAME = "Entities_BetType";
	public static $IDENTITY = "typ_id";
	private static $TRANSLATE = false;
	protected static $CONV = array(
		'bt.typ_id' => 'betTypeId',
		'(COALESCE(p.text, bt.nazev))' => 'name'
	);

	protected static function defaultJoins($query) {
		if (static::$TRANSLATE)
			return parent::defaultJoins($query)->joinLeft(
				array('p' => 'preklady'),
				static::$TABLE_PREFIX . '.nazev = p.index_pole AND p.lang_id=1',
				null
			);
		else
			return parent::defaultJoins($query)->joinLeft(
				array('p' => 'preklady'),
				'0=1',
				null
			);
	}

	/**
	 * Returns all bet types in the system
	 * @return array array of the region structures
	 * @see Entities_BetType
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Returns all bet types in the system with
	 * @param integer $langId ID of target language; if empty, no translation is performed (1=cs,2=en,16=sk)
	 * @return array array of the region structures
	 * @see Entities_BetType
	 */
	public static function getAllTranslated($langId, $extensions = null) {
		if (empty($langId))
			return static::getAll($extensions);
		$backup = static::$TRANSLATE;
		static::$TRANSLATE = true;
		$result = parent::getAll($extensions);
		static::$TRANSLATE = $backup;
		return $result;
	}

	/**
	 * Find be type by given identifier.
	 * @param integer $betTypeId identifier of the team
	 * @return struct bet type structure	 
	 * @see Entities_BetType	 
	 */
	public static function getById($betTypeId, $extensions = null) {
		return parent::getById($betTypeId, $extensions);
	}
	
	/**
	 * Insert new bet type.
	 * is generated. 
	 * @param struct $betType structure of the team
	 * @return integer bet type identifier of the created team
	 * @see Entities_BetType	 
	 */
	public static function insert($betType) {
		return parent::insert($betType);
	}
	
	/**
	 * Update bet type.  
	 * @param struct $bet structure of the region	 
	 * @return true on success
	 * @see Entities_BetType	 
	 */
	public static function update($bet) {
		return parent::update($bet);
	}
	
	/**
	 * Delete team.  
	 * @param struct $teamId idenetifier of the team.
	 * @return true on success	 
	 * @see Entities_Team	 
	 */
	public static function delete($teamId) {
		return parent::delete($teamId);
	}		
	
	/**
	 * Get all bet types for which there are active bets 
	 * @return struct of the types
	 * @see Entities_BetType
	 */
	public static function getActive($eventIds=null, $sportId=null) {
		$db = static::getDb();
		$where = "s.status NOT IN (3, 1) AND bc.bet_count > 0";
		
		if(!empty($eventIds))
			$where .= $db->quoteInto(" AND s.udalost_id IN (?)", $eventIds);
		if(!empty($sportId))
			$where .= $db->quoteInto(" AND u.sport_id = ?", $sportId);

		$types = $db->query(
			"SELECT
				".static::$TABLE_PREFIX.".typ_id,
				COALESCE(p.text, ".static::$TABLE_PREFIX.".nazev) AS name
			FROM sazky s
			JOIN typ ".static::$TABLE_PREFIX."
				ON ".static::$TABLE_PREFIX.".typ_id = s.typ_id
			JOIN bet_column bc
				ON bc.sazka_id = s.sazka_id
			JOIN udalost u
				ON u.udalost_id = s.udalost_id
			LEFT JOIN preklady p
				ON ".static::$TABLE_PREFIX.".nazev = p.index_pole AND p.lang_id=1
			WHERE
				".$where."
			GROUP BY ".static::$TABLE_PREFIX.".typ_id;"
		);

		$ret = array();
		while($row = $types->fetch()) {
			$arr = array(
					static::$CONV['bt.typ_id'] => $row['typ_id'],
					'name' => $row['name']
			);
			$ret[] = new It6_ArrayWrapper($arr);
		}
	
		return $ret;
	}
}