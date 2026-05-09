<?php

/**
 * Branch type related static methods.
 * @author Martin Bohal
 * @see Entities_UserFinance
 */
class Webservice_UserFinance extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "UserFinance";
	public static $TABLE		= "uzivatel_im_data";
	public static $TABLE_PREFIX	= "uf";
	public static $IDENTITY		= "id";

	public static $CONV = array(
		'user_id'			=> 'id',
		'zustatek'			=> 'balance',
		'zetony'			=> 'chips',
		'dluh'				=> 'debit',
		'zustatek_bonus'	=> 'bonus'
	);



	protected static function getDb() {
		return static::getAdminDb();
	}


	/**
	 * Returns all user data
	 * $param @columns array of the desired columns
	 * @return struc structure of the users and their data
	 */
	public static function getAllColumns($columns, $extensions = null) {

		return parent::getAllColumns($columns, $extensions);
	}


	/**
	 * Returns all user data
	 * @return struc structure of the users and their data
	 */
	public static function getAll($extensions = null) {

		return parent::getAll($extensions);
	}



	/**
	 * Returns data for the user witht he give Id
	 * @param array $typeId id of the user we need info about
	 * @return struc structure of the user with the given id
	 */
	public static function getById($typeId, $extensions = null){

		return parent::getById($typeId, $extensions);
	}
}
