<?php

/**
 * Branch type related static methods.
 * @author Martin Bohal
 * @see Entities_BranchType
 */
class Webservice_BranchType extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_BranchType";
	public static $TABLE		= "branch_type";
	public static $TABLE_PREFIX	= "bt";
	public static $IDENTITY		= "id";

	public static $CONV = array(
		'id'	=> 'id',
		'name'	=> 'name',
	);



	protected static function getDb() {
		return static::getAdminDb();
	}



	/**
	 * Returns all branch types
	 * @return struc structure of the possible types of branch
	 */
	public static function getAll($extensions = null) {

		return parent::getAll($extensions);
	}



	/**
	 * Returns type with given Id
	 * @param array $typeId id of the type we need info about
	 * @return struc structure of the type with the given id
	 */
	public static function getById($typeId, $extensions = null){

		return parent::getById($typeId, $extensions);
	}
}
