<?php

/**
 * Zipo codes related static methods.
 * @author Tomas Polz
 *
 */
class Webservice_ZipCode extends Webservice_AbstractWebService  {

	public static $TABLE = "town_zip_codes";
	public static $TABLE_PREFIX = "z";
	public static $ENTITY_NAME = "Entities_ZipCode";
	public static $IDENTITY = "id";
	protected static $CONV = array(
		'id' => 'zipCodeId',
		'zip_code' => 'zipCode',
		'town' => 'town',
		'town_part' => 'townPart'
	);

	/**
	 * Returns all zip codes in the system
	 * @return array array of the region structures
	 * @see Entities_Region
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}	 
	
	/**
	 * Find zip code by given identifier.
	 * @param integer $teamId identifier of the zip code
	 * @return struct team structure
	 * @see Entities_Team
	 */
	public static function getById($zipCodeId, $extensions = null) {
		return parent::getById($zipCodeId, $extensions);
	}
	
	/**
	 * get towns by zip_code
	 */
	public static function getTownsByZipCode($zipCode, $extensions){
		$zipCode = str_replace(" ", "", $zipCode);
		$towns =  parent::getAllWhere(array('zipCode = ?' => $zipCode), $extensions);
		
		foreach ($towns as $town){
        	$townId = trim((trim($town['town']) == trim($town['townPart'])) ? $town['townPart'] : $town['town'] . " - " . $town['townPart']);
        	if (!isset($array[$townId]))
                	$array[$townId] = array('town' => $townId);
        }

        asort($array);
        return $array;
	}

	/**
	 * Insert new region. Value of the region identifier is ignored and new
	 * is generated.
	 * @param struct $team structure of the team
	 * @return integer team identifier of the created team
	 * @see Entities_Team
	 */
	public static function insert($zipCodeId) {
		return parent::insert($zipCodeId);
	}

	/**
	 * Update region.
	 * @param struct $team structure of the region
	 * @return true on success
	 * @see Entities_Team
	 */
	public static function update($zipCodeId) {
		return parent::update($zipCodeId);
	}

	/**
	 * Delete team.
	 * @param struct $teamId idenetifier of the team.
	 * @return true on success
	 * @see Entities_Team
	 */
	public static function delete($zipCodeId) {
		return parent::delete($zipCodeId);
	}

}
