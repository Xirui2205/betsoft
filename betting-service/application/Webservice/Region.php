<?php

/**
 * Region related static methods. Region is nearly exactly country.
 * @author Pavel Klinger
 * @see Entities_Region
 *
 */
class Webservice_Region extends Webservice_AbstractWebService  {

	public static $TABLE = "oblast";
	public static $TABLE_PREFIX = "rg";
	public static $IDENTITY = "oblast_id";

	public static $CONV = array (
		'oblast_id' => 'regionId',
		'iso' => 'handle',
		//TODO Add multi languiages support
		'TRANSLATE(rg.nazev,1)' => 'name', // 1 .. CZ
		'pozice' => 'navigationOrder',
		'betradar_oblast_id' => 'betradarId',
		'img' => 'flagPath'
		
	);

	/**
	 * Returns all regions in the system
	 * @return array array of the region structures
	 * @see Entities_Region
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Retrieves (ID => name) pairs ordered by name, name can be translated.
	 * @see Webservice_AbstractWebService::getTableOptions()
	 * @return array (region ID => region name) Ordered by name
	 */
	public static function getAllOptions($langId = null, $where = null) {
		return parent::getTableOptions(static::$TABLE, static::$IDENTITY, 'nazev', $langId);
	}

	/**
	 * Returns all regions where can be done bets now.
	 * @return array array of the sport structures
	 * @see Entities_Sport
	 */
	public static function getRelevant($extensions = null) {
		//TODO extensions
		
		try {
			$select = static::defaultQuery( static::getDb()->select() )
				->join(
					array('ev' => Webservice_Event::$TABLE),
					'rg.oblast_id = ev.oblast_id AND ev.zobrazeno = 1',
					null);
			$select = Webservice_Event::getRelevantQuery($select)
				->group('rg.oblast_id')
				->having('COUNT(ev.udalost_id) > 0');
	
			return static::fetchAllEntities($select->query());
		}
		catch ( Exception $e) {
			throw new It6_XmlRpc_Exception('getRelevant', 0, $e);
		}
	}
	
	/**
	 * Find region by given identifier.
	 * @param integer $regionId identifier of the region
	 * @return struct region structure	 
	 * @see Entities_Region	 
	 */
	public static function getById($regionId, $extensions = null) {
		return parent::getById($regionId, $extensions);
	}
	
	/**
	 * Insert new region. Value of the region identifier is ignored and new
	 * is generated. 
	 * @param struct $region structure of the region
	 * @return integer region identifier of the created region
	 * @see Entities_Region	 
	 */
	public static function insert($region) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * Update region.  
	 * @param struct $region structure of the region	 
	 * @return true on success
	 * @see Entities_Region	 
	 */
	public static function update($region) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * Delete region.  
	 * @param struct $regionId idenetifier of the region.
	 * @return true on success	 
	 * @see Entities_Region	 
	 */
	public static function delete($regionId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * Find region by given handle
	 * @param string $handle
	 * @return struct region structure
	 * @see Entities_Region
	 * @see Entities_Region#handle
	 */
	public static function getByHandle($handle, $extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * Returns all events related to the given region
	 * @param int $regionId identifier of the region
	 * @return array array of event structures
	 * @see Entities_Event
	 */
	public static function getAllEvents($regionId, $extensions = null) {
		return Webservice_Event::getAllWhere(array(
			'oblast_id = ?' => $regionId), $extensions);
	}
	
	/**
	 * Returns all events related to the given region
	 * where can be done bets now.	 
	 * @param int $regionId identifier of the region
	 * @return array array of event structures
	 * @see Entities_Event
	 */
	public static function getRelevantEvents($regionId, $extensions = null) {
		try {
			//TODO implement $extensions
			$select = Webservice_Event::getRelevantQuery(
					Webservice_Event::defaultQuery( Webservice_Event::getDb()->select() ))
				->where('oblast_id = ?', $regionId)
				->where('zobrazeno = 1')
				->group('bt.udalost_id')
				->having('COUNT(bt.sazka_id) > 0');

			return static::fetchAllEntities($select->query());
		}
		catch ( Exception $e) {
			throw new It6_XmlRpc_Exception('getRelevantEvents', 0, $e);
		}
	}

	/**
	 * Returns all events related to the given region and sport
	 * @param int $regionId identifier of the region
	 * @param int $sportId identifier of the sport
	 * @return array array of event structures
	 * @see Entities_Event
	 */
	public static function getEventsBySport($regionId, $sportId, $extensions = null) {
		return Webservice_Sport::getEventsInRegion($sportId, $regionId, $extensions);
	}
	
	/**
	 * Returns all events related to the given sport and region
	 * where can be done bets now.	 
	 * @param int $sportId identifier of the sport
	 * @param int $regionId identifier of the region
	 * @return array array of event structures
	 * @see Entities_Event
	 */
	public static function getRelevantEventsBySport($regionId, $sportId) {
		return Webservice_Sport::getRelevantEventsInRegion($sportId, $regionId);
	}


}
