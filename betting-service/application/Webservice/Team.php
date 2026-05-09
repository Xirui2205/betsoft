<?php

/**
 * Team related static methods. Region is nearly exactly country.
 * @author Pavel Klinger
 * @see Entities_Region
 *
 */
class Webservice_Team extends Webservice_AbstractWebService  {

	public static $TABLE = "team";
	public static $TABLE_PREFIX = "t";
	public static $ENTITY_NAME = "Entities_Team";
	public static $IDENTITY = "id";
	protected static $CONV = array(
		'id' => 'teamId',
		'betradar_id' => 'betradarId',
		's.sport_id' => 'sportId',
		's.nazev' => 'sportName',
		'name' => 'name',
		'short_name' => 'shortName'
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)->join(
			array('s' => 'sport'),
			static::$TABLE_PREFIX . '.sport_id = s.sport_id',
			null);
	}

	/**
	 * Returns all teams in the system
	 * @return array array of the region structures
	 * @see Entities_Region
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Returns all teams with given sport
	 * @param integer $sportId identifier of the sport
	 * @return array array of the region structures
	 * @see Entities_Region
	 */
	public static function getBySport($sportId, $extensions = null) {
		return parent::getAllWhere(array('sport_id = ?' => $sportId), $extensions);
	}

	/**
	 * Returns all teams participating on given event.
	 * @param integer $eventId identifier of the event
	 * @return array array of the sport structures
	 * @see Entities_Sport
	 */
    public static function getByEvent($eventId, $extensions = null) {
		//TODO implement me.
	}

	/**
	 * Find team by given identifier.
	 * @param integer $teamId identifier of the team
	 * @return struct team structure
	 * @see Entities_Team
	 */
	public static function getById($teamId, $extensions = null) {
		return parent::getById($teamId, $extensions);
	}

	/**
	 * Insert new region. Value of the region identifier is ignored and new
	 * is generated.
	 * @param struct $team structure of the team
	 * @return integer team identifier of the created team
	 * @see Entities_Team
	 */
	public static function insert($team) {
		return parent::insert($team);
	}

	/**
	 * Update region.
	 * @param struct $team structure of the region
	 * @return true on success
	 * @see Entities_Team
	 */
	public static function update($team) {
		return parent::update($team);
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

}
