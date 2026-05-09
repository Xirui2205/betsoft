<?php
/**
 * UserNote related static methods
 * @author Martin Bohal
 * @see Entities_UserNote
 *
 */
class Webservice_UserNote extends Webservice_AbstractWebService  {

	public static $TABLE 				= "uzivatel_poznamka";
	public static $TABLE_PREFIX			= "up";
	public static $ENTITY_NAME			= "Entities_UserNote";
	public static $IDENTITY				= "nl_id";

	public static $CONV = array(
		'user_id'			=> 'userId',
		'text'				=> 'text',
		'admin_id'			=> 'adminId',
		'datum'				=> 'date',
		'nl_id'				=> 'noteId',
	);



	/**
	 * Returns all notes for the given user.
	 * @return array array of the note structures
	 * @see Entities_UserNote
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}



	/**
	 * Find note by given identifier.
	 * @param integer $noteId identifier of the note
	 * @return struct the note structure
	 * @see Entities_UserNote
	 */
	public static function getById($noteId, $extensions = null) {
		return parent::getById($noteId, $extensions);
	}



	/**
	 * Delete note.
	 * @param struct $noteId idenetifier of the note.
	 * @return true on success
	 * @see Entities_UserNote
	 */
	public static function delete($noteId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}



	/**
	 * Get all notes belonging the the specified user
	 * @ param integer $userId identifier of the user we need notes for
	 * @return array array of user note structures
	 * @see Entities_UserNote
	 */
	public static function getByUserId($userId, $extensions = null) {
		return parent::getAllWhere(array('userId' => $userId), $extensions);
	}

}
