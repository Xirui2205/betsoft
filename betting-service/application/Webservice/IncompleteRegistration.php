<?php
/**
 * Transaction related static methods.
 * @author Pavel Klinger
 * @see Entities_User
 *
 */
class Webservice_IncompleteRegistration extends Webservice_AbstractWebService  {

	const AGE_LIMIT = 18;

	public static $TABLE						= "incomplete_registration";
	public static $TABLE_PREFIX					= "usr";
	public static $ENTITY_NAME					= "Entities_IncompleteRegistration";
	public static $IDENTITY						= "session_id";
	public static $ADMIN_DB						= "vic_admin";
	public static $MAIN_DB						= "vic_main";


	protected static $CONV = array(
		'usr.session_id'		=> 'sessionId',
		'jmeno'					=> 'firstName',
		'prijmeni'				=> 'lastName',
		'citizen_id'			=> 'citizenId',
		'nick'					=> 'username',
		'heslo'					=> 'password',
		'datum_narozeni'		=> 'birthDate',
		'usr.email'				=> 'email',
		'usr.area_code'			=> 'areaCode',
		'telefon'				=> 'phone',
		'datum_registrace'		=> 'registrationTime',
		'ulice'					=> 'street',
		'misto'					=> 'town',
		'psc'					=> 'zip',
		'zeme_id'				=> 'countryId',
		'newsletter'			=> 'sendNewsletter'

	);


	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);
		$query->joinLeft(
			array(Webservice_AreaCode::$TABLE_PREFIX => Webservice_AreaCode::$TABLE),
			self::$TABLE_PREFIX.'.area_code = '.Webservice_AreaCode::$TABLE_PREFIX.'.id',
			null);
		return $query;
	}


	/**
	 * Returns all users in the system.
	 * @return struct users structure
	 * @see Entities_User
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}


	/**
	 * Returns all users in the system with params and ordered
	 * @param struct $where
	 * @param struct $order
	 * @return struct users structure
	 * @see Entities_User
	 */
	public static function getAllWhereOrder($where, $order, $extensions = null) {
		return parent::getAllWhereOrder($where, $order, $extensions);
	}


	public static function getIncompleteReg($sessionId) {
		try {
			$ret = static::getDb()->select()
				->from(array('b' => self::$TABLE))
				->where('b.session_id = ?', $sessionId)
				->order(array('datum_narozeni DESC'))
				->limit(1)
				->query()->fetchAll();

			return isset($ret[0]) ? $ret[0] : null;
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}


	/**
	 * Delete incomplete registration after succes complete registration.
	 * @param integer $sessionId identifier of the user
	 * @return boolean
	 * @see Entities_User
	 */
	public static function deleteIncompleteRegistration($sessionId) {
		try {
			static::getDb()->delete(self::$TABLE, array('session_id = ?' => $sessionId));
		} catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception($e);
		}
	}


	public static function insert($incomplete_user) {

		$incomplete_user['email'] = trim($incomplete_user['email']);
		$incomplete_user['username'] = trim($incomplete_user['username']);

		$curMonth = date('n');
		$curDay = date('j');
		$curYear = date('Y');
		$curDateTmpStmp	= mktime(0, 0, 0, $curMonth, $curDay, $curYear);

		if (strtotime('-'.static::AGE_LIMIT.' year', It6_Date::nowAsTimestamp()) <= It6_Date::fromDbAsTimestamp($incomplete_user['birthDate'])) {
			throw new It6_XmlRpc_Exception('User is younger than '.static::AGE_LIMIT.' years.');
		}

		if ( empty($incomplete_user['citizenId']) )
			throw new It6_XmlRpc_Exception("Missing citizen id.");

		if ( empty($incomplete_user['phone']) )
			throw new It6_XmlRpc_Exception("Missing phone.");

		$db = static::getDb();
		$data = static::fromEntity($incomplete_user);

		$now = It6_Date::dbNow();
		$data['datum_registrace'] = $now;
		$sessionID = Zend_Session::getId();
		$incompleteRegistration = self::getIncompleteReg($sessionID);

		if (empty($incompleteRegistration)) $db->insert(self::$TABLE, $data);
		else $db->update(self::$TABLE, $data, array("session_id = ?" => $sessionID));
	}


	public static function toEntity($incomplete_user, $columns = null) {
		$db = static::getDb();
	}
}