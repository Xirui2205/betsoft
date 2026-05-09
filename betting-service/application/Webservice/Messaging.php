<?php
/**
 * Language related static methods
 * @author Pavel Klinger
 * @deprecated
 * @see Entities_Messaging
 * 
 */
class Webservice_Language extends Webservice_AbstractWebService  {
	
	/**
	 * Returns all languages in the system.
	 * @return array array of the bookmaker structures
	 * @see Entities_Language
	 */
	public static function getAll($extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * Find language by given identifier.
	 * @param integer $languageId identifier of the language
	 * @return struct language structure	 
	 * @see Entities_Language	 
	 */
	public static function getById($languageId, $extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}		
	
/**
	 * Insert new host. Value of the host identifier is ignored and new
	 * is generated. 
	 * @param struct $host structure of the host
	 * @return integer host identifier of the created host
	 * @see Entities_Host	 
	 */
	public static function insert($host) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * Update host.  
	 * @param struct $host structure of the host	 
	 * @return true on success
	 * @see Entities_Host	 
	 */
	public static function update($host) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * Delete host.  
	 * @param struct $hostId idenetifier of the host.
	 * @return true on success	 
	 * @see Entities_Host	 
	 */
	public static function delete($hostId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * All users using this language.
	 * @return array array of user structures  
	 * @see Entities_User
	 */
	public static function getAllUsers() {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

}