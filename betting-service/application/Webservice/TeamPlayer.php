<?php

/**
 * Team players related static methods.
 * @author Tomas Polz
 *
 */
class Webservice_TeamPlayer extends Webservice_AbstractWebService  {

	public static $TABLE = "team_player";
	public static $TABLE_PREFIX = "tp";
	public static $ENTITY_NAME = "Entities_TeamPlayer";
	public static $IDENTITY = "id";
	protected static $CONV = array(
		'id' => 'teamPlayerId',
		'team_id' => 'teamId',
		'name' => 'name',
	);

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
	public static function getByTeam($teamId, $extensions = null) {
		return parent::getAllWhere(array('team_id = ?' => $teamId), $extensions);
	}

	public static  function getByBet($betId, $extensions = null) {

		self::$CONV['s.sazka_id'] = 'sazkaId';
		return parent::getAllWhere(array('sazkaId = ?' => $betId), $extensions, array(
				array(array('t' => 'team'), static::$TABLE_PREFIX . '.team_id = t.id',null),
				array(array('s' => 'team_sazky'), 't.betradar_id = s.betradar_team_id',null)
				));
	}
	
	public static function getByEvent($eventId, $extensions = null){
		self::$CONV['u.udalost_id'] = 'udalostId';
		return parent::getAllWhere(array('udalostId = ?' => $eventId), $extensions, array(
				array(array('t' => 'team'), static::$TABLE_PREFIX . '.team_id = t.id',null),
				array(array('s' => 'sport'), 't.sport_id = s.sport_id',null),
				array(array('u' => 'udalost'), 'u.sport_id = s.sport_id',null),
		));
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
