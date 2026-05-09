<?php

class It6_Session_Admin extends It6_Session_Abstract {

const STATUS_ANONYMOUS = 0;
const STATUS_AUTHENTICATED = 2;
const STATUS_JUST_AUTHENTICATED = 3;
const STATUS_TIMEOUT = 1;
const STATUS_ABORTED = 4;
const STATUS_BLOCKED = 5;
const STATUS_NEW_PASSWD = 6;
const STATUS_WEBSITE_OFF = 7;

const PARAM_WEBSITE_OFF = 'global.adminOff';

const UA_SALT = 'NrVsW0q4bxgD';

private static $status = self::STATUS_ANONYMOUS;
private static $sessionId = null;
private static $user = null;
private static $ipAddressPartCount = 2;
private static $ipAddress = null;
private static $startTime = null;
private static $message = null;
private static $mobile = null;

public static function getAppId() {
	return It6_Session_Abstract::APPID_ADMIN;
}

public static function start(&$db, &$dbGame = null, $mobile = 0) {
	if (!is_resource($db) && !is_object($db))
		return false;
	else {
		$GLOBALS['mobile_connect'] = $mobile;

	$GLOBALS['db_ses'] = &$db;
		if ($dbGame != null)
			$GLOBALS['db_ses_game'] = &$dbGame;
		//TODO: following line is bloody mess, get rid of that
		else if (substr_count($_SERVER["SERVER_NAME"], 'admin') > 0)
			$GLOBALS['db_ses_game'] = $db;

	session_start();

	// following code is for backward compatibility with old-style admin pages
	//if( $GLOBALS['ses_status'] == 0) {
	//	$GLOBALS['ses_admin_id'] = 0;
	//	$GLOBALS['ses_bookmaker_id'] = 0;
	//}
	//if (isset($GLOBALS['ses_admin_id'])) {
	//	//if(!isset($_SESSION['user_id']))
	//		$_SESSION['user_id'] = $GLOBALS['ses_admin_id'];
	//}
	//else if (isset($GLOBALS['ses_bookmaker_id'])) {
	//	//if (!isset($_SESSION['user_id']))
	//		$_SESSION['user_id'] = $GLOBALS['ses_bookmaker_id'];
	//}

		return true;
	}
}

public static function end(&$db = null) {
	if ($db != NULL)
		$GLOBALS['db_ses'] = &$db;
	session_write_close();
}

public static function getStatus() {
	return self::$status;
}

public static function isAuthenticated() {
	switch (self::$status) {
	case self::STATUS_AUTHENTICATED:
	case self::STATUS_JUST_AUTHENTICATED:
		return true;
	default:
		return false;
	};
}

public static function isAccessible() {
	switch (self::$status) {
	case self::STATUS_ANONYMOUS:
	case self::STATUS_AUTHENTICATED:
	case self::STATUS_JUST_AUTHENTICATED:
	case self::STATUS_NEW_PASSWD:
		return true;
	default:
		return false;
	};
}

public static function getUserData($key = null) {
	if (!self::isAuthenticated())
		return null;
	if (!is_array(self::$user))
		return null;
	if (isset($key))
		return (array_key_exists($key, self::$user) ? self::$user[$key] : null);
	else
		return self::$user;
}

public static function getStartTime() {
	return self::$startTime;
}

public static function getMessage() {
	return self::$message;
}

public static function setMessage($msg) {
	self::$message = $msg;
}

public static function abortUserSession($userId) {
	$db = Zend_Registry::get('zdb_admin');
	try {
		$db->update(
			'session',
			array('status' => self::STATUS_ABORTED, 'zprava' => 'Admin delete'),
			array(
				'status IN (?)' => array(self::STATUS_AUTHENTICATED, self::STATUS_JUST_AUTHENTICATED),
				'admin_id=?' => $userId
			)
		);
		return true;
	}
	catch (Exception $e) {
		throw new ExHandler($e->getMessage(), "admin_ex_db");
	}
}

// functions for session handling /////////////////////////

public static function open($save_path, $session_name) {
	return true;
}

public static function close() {
  unset($GLOBALS['db_ses']);
  return true;
}

private static function getIpAddress($partCount) {
	if (1 == preg_match('/^(\\d+)\\.(\\d+)\\.(\\d+)\\.(\\d+)$/', self::$ipAddress, $matches)) {
		$ip = '';
		for ($i = 1; $i < count($matches) && $i <= $partCount; ++$i) {
			if ($i > 1)
				$ip .= '.';
			$ip .= $matches[$i];
		}
		return $ip;
	}
	else
		return '';
}

private static function getUaHash() {
	static $uaHash = null;
	if (null == $uaHash)
		$uaHash = md5($_SERVER['HTTP_USER_AGENT'] . self::UA_SALT);
	return $uaHash;
}

private static function isWebsiteOff() {
	static $isOff = null;
	if (null == $isOff) {
		$rows = Zend_Registry::get('zdb_admin')->select()
			->from('parameter', 'value')
			->where('name=?', self::PARAM_WEBSITE_OFF)
			->query()
			->fetchAll();
		$isOff = (!empty($rows) && !empty($rows[0]['value']));
	}
	return $isOff;
}

private static function isChannelSecure() {
	return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on');
}

private static function login($username, $passwd) {
	try {
		$dbAdmin = Zend_Registry::get('zdb_admin');
		$dbWeb = Zend_Registry::get('zdb_game');
		$rows = It6_Models_Admin::readDataByUsername($username, $dbAdmin);

		if (empty($rows)) {
			self::$user = false;
			$authenticated = false;
		}
		else {
			self::$user = $rows[0];
			$authenticated = (It6_Models_Admin::passwdMatches($passwd, self::$user['passwd'])
				&& 0 == self::$user['access']  && self::$user['block'] <= MAX_LOGIN 
				//IT6: forced SSL
				&& self::isChannelSecure()
			);
		}
		if ($authenticated) {
			self::$status = self::STATUS_JUST_AUTHENTICATED;
			// update old session status if any (with given userId)
			$dbAdmin->update(
				'session',
				array('status' => self::STATUS_TIMEOUT),
				array(
					'status IN (?)' => array(self::STATUS_AUTHENTICATED, self::STATUS_JUST_AUTHENTICATED),
					'admin_id=?' => self::$user['id']
				)
			);
			// replace session (with given sessionId)
			$rows = $dbAdmin->select()
				->from('session', 'data')
				->where('ses_id=?', self::$sessionId)
				->query()
				->fetchAll();
			$data = ( empty($rows) ? '' : $rows[0]['data'] );
			$dbAdmin->delete( 'session', array('ses_id=?' => self::$sessionId) );
			$dbAdmin->insert('session', array(
				'data' => $data,
				'start' => It6_Date::dbNow(),
				'status' => self::$status,
				'zprava' => '',
				'admin_id' => self::$user['id'],
				'ses_id' => self::$sessionId,
				'ip' => self::getIpAddress(self::$ipAddressPartCount),
				'prohlizec' => self::getUaHash(),
				'time' => time()
			));
			// reset neuspesnych pokusu o prihlaseni
			$dbAdmin->update(
				'admin',
				array(
					'block' => 0,
					'block_ip' => '',
					'last_login' => It6_Date::dbNow()
				),
				array('admin_id=?' => self::$user['id'])
			);
			// logovani vnejsich refereru
			if (!empty($_GET['referer'])) {
				$url = parse_url($_GET['referer']);
				if (BASEDOMAIN != $url['host']) {
					$dbAdmin->insert(
						'referer',
						array(
							'admin_id' => self::$user['id'],
							'referer_url' => $url['host'],
							'date' => It6_Date::dbNow()
						)
					);
				}
			}
			if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
				$location = 'https://' . $_SERVER['HTTP_X_FORWARDED_HOST'] . (isset($_GET['uri']) ? $_GET['uri'] : '');
			else
				$location = HOST;
			$json = Zend_Json::encode(array('status' => 'OK', 'location' => $location));
		}
		else { // user not allowed to login
			$langId = (empty($_GET['lang_id']) ? 1 : intval($_GET['lang_id']));
			$transKey = 'auth_failed';
			if (false != self::$user) {
				if (MAX_LOGIN < ++self::$user['block']) {
					// prekrocen max. povoleny pocet neuspesnych prihlaseni
					$transKey = 'auth_failed_block';
				}
				$dbAdmin->update(
					'admin',
					array(
						'block' => self::$user['block'],
						'block_ip' => new Zend_Db_Expr( 'CONCAT(block_ip,' . $dbAdmin->quote($_SERVER["REMOTE_ADDR"] . ';') . ')' )
					),
					array('admin_id=?' => self::$user['id'])
				);
			}
			$rows = $dbWeb->select()
				->from('preklady', array('text', 'index_pole'))
				->where('index_pole=?', $transKey)
				->where('lang_id=?', $langId)
				->query()
				->fetchAll();
			if (!empty($rows) && !empty($rows[0]))
				$text = $rows[0]['text'];
			else
				$text = 'Neoprávněné přihlášení';
			$json = Zend_Json::encode(array('status' => 'ERR', 'message' => $text));
		}
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-length: ' . strlen($json));
		header('Content-type: application/json');
		echo $json;
	}
	catch (Exception $e) {
		//TODO: more handling?
		//throw new ExHandler($e->getMessage(), 'page_ex_db');
		throw $e;
	}
	return true;
}

public static function read($sessionId) {
	self::$sessionId = $sessionId;
	self::$ipAddress = $_SERVER['REMOTE_ADDR'];
	if (isset($_POST['pass']) && isset($_POST['nick'])) {
		self::login($_POST['nick'], $_POST['pass']);
		exit();
	}
	else if (!empty($_REQUEST['logoff'])) {
		// reset self::$user ?
		self::$status = self::STATUS_ANONYMOUS;
		return '';
	}
	else {
		if (self::isWebsiteOff()) {
			self::$status = self::STATUS_WEBSITE_OFF;
			return '';
		}
		$dbSess = Zend_Registry::get('zdb_admin');
		$dbSess->update(
			'session',
			array('status' => self::STATUS_TIMEOUT),
			array(
				'status NOT IN(?)' => array(self::STATUS_ANONYMOUS, self::STATUS_ABORTED, self::STATUS_TIMEOUT),
				'time<?' => (time() - SESMAX),
				'ses_id=?' => $sessionId,
				'prohlizec=?' => self::getUaHash()
			)
		);
		$rows = $dbSess->select()
			->from('session')
			->where('ses_id=?', $sessionId)
			->where('prohlizec=?', self::getUaHash())
			->query()
			->fetchAll();
		$session = (empty($rows) ? false : $rows[0]);
		//IT6: forced SSL
		if (false !== $session) {
			self::$status = $session['status'];
			if (self::isAuthenticated() && self::isChannelSecure()) {
			//if (false !== $session) {
				self::$startTime = $session['start'];
				self::$message = $session['zprava'];
				if (!empty($session['admin_id'])) {
					$dbAdmin = Zend_Registry::get('zdb_admin');
					$row = It6_Models_Admin::getData($session['admin_id'], $dbAdmin);
					if (!empty($row)) {
						self::$user = $row;
						if (0 == self::$user['access']) {
							self::$status = self::STATUS_AUTHENTICATED;
							return $session['data'];
						}
						else {
							self::$status = self::STATUS_BLOCKED;
							return '';
						}
					}
				}
			}
		}
		self::$status = self::STATUS_ANONYMOUS;
		return '';
	}
}

public static function write($sessionId, $data) {
	//IT6: forced SSL
	if (self::STATUS_TIMEOUT == self::$status || !self::isChannelSecure())
	//if (self::STATUS_TIMEOUT == self::$status)
		self::$status = self::STATUS_ANONYMOUS;
	if(isset(self::$status) && self::isAccessible()) {
		$dbSess = Zend_Registry::get('zdb_admin');
		try {
			if (empty(self::$startTime) || self::STATUS_JUST_AUTHENTICATED == self::$status)
				self::$startTime = It6_Date::dbNow();
			$dbSess->beginTransaction();
			$dbSess->delete('session', array('ses_id=?' => $sessionId));
			$dbSess->insert(
				'session',
				array(
					'ses_id' => $sessionId,
					'ip' => self::getIpAddress(self::$ipAddressPartCount),
					'prohlizec' => self::getUaHash(),
					'data' => $data,
					'time' => time(),
					'zprava' => self::$message,
					'status' => (self::STATUS_JUST_AUTHENTICATED == self::$status ? self::STATUS_AUTHENTICATED : self::$status),
					'start' => self::$startTime,
					'admin_id' => self::$user['id'],
					'mobile' => 0
				)
			);
			$dbSess->commit();
		}
		catch (Exception $e) {
			$dbSess->rollback();
			throw $e;
		}
	}
}

//TODO: jak je to s tim prohlizecem?
public static function destroy($sessionId) {
	Zend_Registry::get('zdb_admin')->delete(
		'session',
		array(
			'ses_id=?' => $sessionId,
			'prohlizec=?' => self::getUaHash()
		)
	);
}

public static function gc($maxlifetime) {
	return true;
}

} // class It6_Session_Admin
