<?php

/**
 * Branch type related static methods.
 * @author Martin Bohal
 * @see Entities_BranchLocation
 */
class Webservice_BranchLocation extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_BranchLocation";
	public static $TABLE		= "branch_location";
	public static $TABLE_PREFIX	= "bl";
	public static $IDENTITY		= "id";

	public static $CONV = array(
		'id'		=> 'id',
		'name'		=> 'name',
		'latitude'	=> 'latitude',
		'longitude'	=> 'longitude',
	);

	protected static function getDb() {
		return static::getAdminDb();
	}


	/**
	 * Returns all branch types
	 * @return struct object of the branch structures
	 * @see Entities_Currency
	 */
	public static function getAll($extensions = null) {

		return parent::getAll($extensions);
	}



	/**
	 * Returns location with given Id
	 * @param array $locationId id of the location we need info about
	 * @return struc structure of the location with the given id
	 */
	public static function getById($typeId, $extensions = null){

		return parent::getById($typeId, $extensions);
	}
}
