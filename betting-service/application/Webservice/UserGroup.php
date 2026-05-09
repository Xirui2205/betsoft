<?php

/**
 * Transaction related static methods.
 * @author Pavel Klinger
 * @see Entities_UserGroup
 *
 */
class Webservice_UserGroup extends Webservice_AbstractWebService  {
	
	/**
	 * Returns all user groups in the system.
	 * @return array array of the ticket structures
	 * @see Entities_Sport
	 */
	public static function getAll($extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Find user group by given identifier.
	 * @param integer $userGroupId identifier of the user group
	 * @return struct user group structure	 
	 * @see Entities_UserGroup	
	 */
	public static function getById($userGroupId, $extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}	
	
    public static function getUsers($extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}			
	
}