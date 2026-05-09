<?php

/**
 * Stem related static methods.
 * @see Entities_Stem
 */
class Webservice_Stem extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_Stem";
	public static $TABLE		= "stem";
	public static $TABLE_PREFIX	= "st";
	public static $IDENTITY		= "id";

	public static $CONV = array(
			'id' => 'stemId',
			'name' => 'name',
			'st.admin_id' => 'adminId',
			'desc' => 'desc',
			'a.first_name' => 'adminFirstName',
			'a.surname' => 'adminLastName',
			'filename' => 'fileName',
			'sendmail' => 'sendMail'
	);
	
	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);
		
		$query
			->joinLeft(
				array('a' => 'admin'),
				static::$TABLE_PREFIX.'.admin_id = a.admin_id',
				null
			);
		return $query;
	}
	
	protected static function getDb() {
		return static::getAdminDb();
	}

	/**
	 * Returns all stams
	 * @return struc structure of the stams of branch
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}



	/**
	 * Returns stem with given Id
	 * @param array $stemId id of the stem we need info about
	 * @return struc structure of the stem with the given id
	 */
	public static function getById($stemId, $extensions = null){
		return parent::getById($stemId, $extensions);
	}
	
	/**
	 * returns stem by it's name
	 * @param string $stemName
	 * @param struc $extensions
	 */
	public static function getByName($stemName, $extensions = null) {
		return parent::getOneBy($stemName, 'name', $extensions);
	}
	
	/**
	 * Insert new stem. 
	 * @param struct $region structure of the region
	 * @return integer region identifier of the created region
	 * @see Entities_Stem
	 */
	public static function insert($stem) {
		return parent::insert($stem); 
	}
	
	/**
	 * Update stem.
	 * @param struct $stam structure of the stem
	 * @return true on success
	 * @see Entities_Stem
	 */
	public static function update($stem) {
		return parent::update($stem);
	}
	
	/**
	* Delete stem.
	* @param struct $stemId idenetifier of the stem.
	* @return true on success
	* @see Entities_Stem
	*/
	public static function delete($stemId) {
		return parent::delete($stemId);
	}
}
