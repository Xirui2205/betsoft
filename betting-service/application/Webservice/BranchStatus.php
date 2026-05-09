<?php

/**
 * Branch type related static methods.
 * @author Martin Bohal
 * @see Entities_BranchType
 */
class Webservice_BranchStatus extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_BranchStatus";
	public static $TABLE		= "branch_status";
	public static $TABLE_PREFIX	= "bs";
	public static $IDENTITY		= "id";

	public static $CONV = array(
		'id'	=> 'id',
		'name'	=> 'name',
	);



	protected static function getDb() {
		return static::getAdminDb();
	}



	/**
	 * Returns all branch status types
	 * @return struc structure of the possible status types of branch
	 */
	public static function getAll($extensions = null) {

		return parent::getAll($extensions);
	}



	/**
	 * Returns status type with given Id
	 * @param array $typeId id of the status type we need info about
	 * @return struc structure of the status type with the given id
	 */
	public static function getById($typeId, $extensions = null){

		return parent::getById($typeId, $extensions);
	}
}
