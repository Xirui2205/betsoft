<?php
/**
 * Language related static methods
 * @author Pavel Klinger
 * @see Entities_Language
 *
 */
class Webservice_Language extends Webservice_AbstractWebService  {

	public static $TABLE 				= "jazyky";
	public static $TABLE_PREFIX			= "lang";
	public static $ENTITY_NAME			= "Entities_Language";
	public static $IDENTITY				= "lang_id";

	public static $CONV = array(
		'lang_id'			=> 'languageId',
		'iso'				=> 'iso',
		//FIXME:
		//Commented out by Martin as I do not know what they are and what they should be called.When someone needs them they can uncomment them and come up with some sense making name.
		//Same thing has to be done in Entities_Language
		//'pozice'			=> '',
		'alt_text'			=> 'name',
		'zobrazeno'			=> 'isShown',
		'lc_local'			=> 'lcLocal',
		'iso_homepage'		=> 'homepageIso',
		'kontakt'			=> 'contact',
		'flag_oblast_id'	=> 'flagId'
	);


	/**
	 * Returns all languages in the system.
	 * @return array array of the bookmaker structures
	 * @see Entities_Language
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Returns all active languages in the system.
	 * @return array array of the bookmaker structures
	 * @see Entities_Language
	 */
	public static function getAllActive($extensions = null) {
		return parent::getAllWhere(array('zobrazeno' => '1'),$extensions);
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
	 * All users using this language.
	 * @return array array of user structures
	 * @see Entities_User
	 */
	public static function getAllUsers($extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

}
