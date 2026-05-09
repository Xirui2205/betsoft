<?php

/**
 * BetHandleRange related static methods. Region is nearly exactly country.
 * @author Pavel Klinger
 * @see Entities_BetHandleRange
 *
 */
class Webservice_BetHandleRange extends Webservice_AbstractWebService {
	
	public static $TABLE = "bet_handle_range";
	public static $TABLE_PREFIX = "bhr";
	public static $ENTITY_NAME = "Entities_BetHandleRange";
	public static $IDENTITY = "id";
	public static $TEAM_HAS_RANGE_TABLE = 'team_has_bet_handle_range';
	
	protected static $CONV = array(
		'id' => 'betHandleRangeId',
		'event_id' => 'eventId',
		'sport_id' => 'sportId',
		'typ_id' => 'typId',
		'from' => 'from',
		'to' => 'to'
	);
	
	protected static $TEAM_HAS_RANGE_CONV = array(
		'team_id'    => 'teamId',
		'bet_handle_range_id'  => 'betHandleRangeId',
		'number' => 'number'
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->joinLeft(
				array(
					Webservice_Event::getTable()),
					Webservice_Event::getIdentity().' = ' . static::getIdentity(),
					null);
	}

	/**
	 * Returns all betHandleRanges in the system
	 * @return array array of the region structures
	 * @see Entities_Region
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}
	
	/**
	 * Returns all betHandleRanges participating on given event.
	 * @param integer $eventId identifier of the event
	 * @return array array of the sport structures
	 * @see Entities_Sport
	 */
    public static function getByEvent($eventId) {
		//TODO implement me.
	}
	
	/**
	 * Find betHandleRange by given identifier.
	 * @param integer $betHandleRangeId identifier of the betHandleRange
	 * @return struct betHandleRange structure	 
	 * @see Entities_BetHandleRange	 
	 */
	public static function getById($betHandleRangeId, $extensions = null) {
		$entities = static::getDb()->select()
			->from(array('b' => static::$TABLE))
			->joinLeft(array('e' => Webservice_Event::$TABLE), 'e.'.Webservice_Event::$IDENTITY.' = b.event_id', array('sport_id'))
			->where('b.'.static::$IDENTITY.'=?', $betHandleRangeId)
			->query()->fetch();

		return static::toEntity($entities);
	}
	
	/**
	 * Insert new region. Value of the region identifier is ignored and new
	 * is generated. 
	 * @param struct $betHandleRange structure of the betHandleRange
	 * @return integer betHandleRange identifier of the created betHandleRange
	 * @see Entities_BetHandleRange	 
	 */
	public static function insert($betHandleRange) {
		
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		
		try {
			$id = parent::insert($betHandleRange);			
			
			$betHandleRange = new It6_ArrayWrapper($betHandleRange);
			
			static::insertManyToManyWithParameter(
				$db, 
				static::$TEAM_HAS_RANGE_TABLE,
				'bet_handle_range_id', 'betHandleRangeId', $id, 
				'team_id', 'teamId',
				'number', 'number', $betHandleRange->teams);
			

			It6_DbTransaction::commit($db);
				
			return $id;
		} catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not insert bet handle range.", 0, $e);
		}
	}
	
	/**
	 * Update region.  
	 * @param struct $betHandleRange structure of the region	 
	 * @return true on success
	 * @see Entities_BetHandleRange	 
	 */
	public static function update($betHandleRange) {
		
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		
		try {
			$id = parent::update($betHandleRange);
							
			
			$betHandleRange = new It6_ArrayWrapper($betHandleRange);
			
			static::updateManyToManyWithParameter(
				$db, 
				static::$TEAM_HAS_RANGE_TABLE,
				'bet_handle_range_id', 'betHandleRangeId', $betHandleRange->betHandleRangeId, 
				'team_id', 'teamId',
				'number', 'number', $betHandleRange->teams);
			

				It6_DbTransaction::commit($db);
				
			return $id;
		} catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update bet handle range.", 0, $e);
		}
	}
	
	/**
	 * Delete betHandleRange.  
	 * @param struct $betHandleRangeId idenetifier of the betHandleRange.
	 * @return true on success	 
	 * @see Entities_BetHandleRange	 
	 */
	public static function delete($betHandleId) {
		//FIXME Mely by se mazat i veci z TEAM_HAS_RANGE_TABLE
		return parent::delete($betHandleId);
	}
	
	public static function toEntity($betHandleRange, $columns = null) {
		$db = static::getDb();
		
		$ret = parent::toEntity($betHandleRange, $columns);
		
		try {
			$teams = $db->select()
				->from( array(static::$TEAM_HAS_RANGE_TABLE) )
				->where('bet_handle_range_id = ?', $ret->betHandleRangeId)
				->query()->fetchAll();
			
			$ret->teams = array();
				
			foreach ( $teams as $team ) {
				$entity = new Entities_BetHandleRangeTeam();
				$entity = static::mapDbArray2Entity($team, $entity, static::$TEAM_HAS_RANGE_CONV);
				$ret->teams[] = $team;
			}
			
			return $ret;
			
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("toEntity.", 0, $e);
		}

	}

}
