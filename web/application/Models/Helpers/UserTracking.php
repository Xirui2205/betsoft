<?php
/**
 * class Models_Helpers_UserTracking
 * User tracking data wrapper
 */

class Models_Helpers_UserTracking {

	const COOKIE_PERSISTENTID = 'PKSy6pf3XJ';
	const DBTABLE = 'user_tracking';

	public $id = null;
	public $timestamp = null;
	public $persistentId = null;
	public $sessionId = null;
	public $remoteIp;
	public $xforward;
	public $userAgent;
	public $userId;
	public $nick;

	/**
	 * Initializes members from environment and user data.
	 */
	public function __construct($userId, $userNick) {
		$this->id = null;
		$this->timestamp = time();
		$this->sessionId = session_id();
		$this->persistentId = $this->getPersistentIdCookie();
		if (!$this->persistentId)
			$this->generatePersistentId();
		$this->remoteIp = It6_Php::getRemoteAddr();
		$this->xforward = self::getXForward();
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		$this->userId = $userId;
		$this->nick = $userNick;
	}

	/**
	 * Generates new random persistent ID.
	 * @return generated values
	 */
	public function generatePersistentId() {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$maxChar = strlen($chars) - 1;
		$this->persistentId = "{$this->userId}:{$this->nick}:";
		for ($i = 0; $i < 32; ++$i)
			$this->persistentId .= $chars[rand(0, $maxChar)];
		$this->persistentId = md5($this->persistentId);
		return $this->persistentId;
	}

	/**
	 * @returns value of current request 'X-Forward' header or NULL if header is not present
	 */
	public static function getXForward() {
		$headers = getallheaders();
		foreach ($headers as $name => $value) {
			if (0 == strcasecmp('X-Forward', $name))
				return $value;
		}
		return null;
	}

	/**
	 * Inserts new user tracking into DB.
	 * @returns true on success
	 */
	public function writeDb() {
		$db = Zend_Registry::get('db');
		$data = array(
			'ts' => It6_Date::timestampToDb($this->timestamp),
			'persistent_id' => $this->persistentId,
			'session_id' => $this->sessionId,
			'remote_ip' => $this->remoteIp,
			'xforward' => $this->xforward,
			'useragent' => $this->userAgent,
			'user_id' => $this->userId,
			'nick' => $this->nick,
		);
		$rows = $db->insert(self::DBTABLE, $data);
		if (1 == $rows) {
			$this->id = $db->lastInsertId();
			return $this->id;
		}
		else
			return false;
	}

	/**
	 * Update user tracking data in DB.
	 * @returns true on success
	 */
	public function updateDb() {
		$db = Zend_Registry::get('db');
		$data = array(
			'ts' => Models_Helpers_Help::GetTimestampDB($this->timestamp),
			'persistent_id' => $this->persistentId,
			'session_id' => $this->sessionId,
			'remote_ip' => $this->remoteIp,
			'xforward' => $this->xforward,
			'useragent' => $this->userAgent,
			'user_id' => $this->userId,
			'nick' => $this->nick,
		);
		$rows = $db->update(self::DBTABLE, $data, 'user_id='.$this->id);
		if (1 == $rows)
			return true;
		else
			return false;
	}

	/**
	 * Reads user tracking data from DB.
	 * @param $id user ID
	 * @returns true if user was found and read
	 */
	public function readDb($id) {
		$db = Zend_Registry::get('db');
		$rows = $db->select()
		->from( self::DBTABLE, array( 'ut_id', 'ts', 'persistent_id', 'session_id', 'remote_ip', 'xforward',
			'useragent', 'user_id', 'nick') )
		->where('ut_id = ?', $id)
		->query()->fetchAll();
		if (empty($rows))
			return false;
		else
			return $this->readArray($rows[0]);
	}

	/**
	 * Reads user data from array.
	 * @param $data associative array, keys are database columns
	 * @returns true if user was found and read
	 */
	public function readArray(array $data) {
		$this->id = $data['ut_id'];
		$this->timestamp = ( is_numeric($data['ts']) ? $data['ts'] : It6_Date::toTimestamp($data['ts']) );
		$this->persistentId = $data['persistent_id'];
		$this->sessionId = $data['session_id'];
		$this->remoteIp = $data['remote_ip'];
		$this->xforward = $data['xforward'];
		$this->userAgent = $data['useragent'];
		$this->userId = $data['user_id'];
		$this->nick = $data['nick'];
		return $this;
	}

	public static function setPersistentIdCookieValue($value, $expires = 0) {
		return setcookie(self::COOKIE_PERSISTENTID, $value, $expires, '/', WEBHOST, true);
	}

	public function setPersistentIdCookie() {
		return self::setPersistentIdCookieValue($this->persistentId, time() + 20 * 365 * 24 * 60 * 60);
	}

	public static function getPersistentIdCookie() {
		if (!empty($_COOKIE[self::COOKIE_PERSISTENTID]))
			return $_COOKIE[self::COOKIE_PERSISTENTID];
		else
			return false;
	}

	public static function deletePersistentIdCookie() {
		return self::setPersistentIdCookieValue('', time() - 24 * 3600);
	}

}
