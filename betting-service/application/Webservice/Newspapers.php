<?php

/**
 * Stem related static methods.
 * @see Entities_Newspapers
 */
class Webservice_Newspapers extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_Newspapers";
	public static $TABLE		= "newspapers";
	public static $TABLE_PREFIX	= "n";
	public static $IDENTITY		= "id";

	public static $CONV = array(
			'id' 			=> 'newspaperId',
			'valid_to'		=> 'validTo',
			'filename' 		=> 'fileName'
	);
	
	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);
		
		return $query;
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
	public static function insert($values) {

		$values["validTo"] = It6_Date::toDbAsDate($values['validTo']);

		$adapter = new Zend_File_Transfer_Adapter_Http();
     
	    $adapter->setDestination(ROOT . 'tmp');
	     
	    if (!$adapter->receive()) {
	        $messages = $adapter->getMessages();
	        return false;
	    }
	    else {
	    	$fullPath = $adapter->getFileName();
	    	$fileName = $adapter->getFileName(null, false);
	    	$values["fileName"] = $fileName;
			$ftp = new It6_FtpSync_Client();
			$res = $ftp->upload(array($fullPath => "/pdf/newspapers/$fileName"), false);
	    }

	    It6_GlobalCache_Invalidator::invalidateFooterFrame();

		return parent::insert($values); 
	}

	public static function getActualFilePath(){
		$db = self::getMainDb();

		$select = $db->select()
					->from(self::$TABLE, array("filename"))
					->where("valid_to <= ?", It6_Date::dbNowAsDate())
					->order("valid_to DESC")
					->limit(1);

		$row = $select->query()->fetch();

		return $row["filename"];
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
	public static function delete($newspaperId) {

		$newspaper = self::getById($newspaperId);
		$ftp = new It6_FtpSync_Client();
		$res = $ftp->delete("/pdf/newspapers/" . $newspaper->fileName);

		return parent::delete($newspaperId);
	}
}
