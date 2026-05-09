<?php
class Webservice_Session {

	const TABLE_WEB_SESSION = 'session';
	const TABLE_ADMIN_SESSION = 'session';

	/**
	 * Clean up admin and web session
	 * @return boolean true on success
	 */
	public static function cleanUp() {
		$admindb = Webservice_AbstractWebService::getAdminDb();
		$sessiondb = Webservice_AbstractWebService::getSessionDb();
		It6_DbTransaction::begin($admindb);
		It6_DbTransaction::begin($sessiondb);
		try {
			$sessiondb->delete(
				self::TABLE_WEB_SESSION,
				array('status NOT IN (2,3,0)'));

			$sessiondb->update(
				self::TABLE_WEB_SESSION,
				array('status = 1'),
				array(
					'status IN (2,3)',
					'time < ?',It6_Date::nowAsTimestamp() - SESMAX ));

			$sessiondb->update(
				self::TABLE_ADMIN_SESSION,
				array('status = 1'),
				array(
					'status IN (2,3)',
					'time < ?',It6_Date::nowAsTimestamp() - SESMAX ));


			It6_DbTransaction::commit($admindb);
			It6_DbTransaction::commit($sessiondb);
			return true;
		} catch (Exception $e) {
			It6_DbTransaction::rollback($admindb);
			It6_DbTransaction::rollback($sessiondb);
			throw new It6_XmlRpc_Exception('Session::cleanUp', 0, $e);
		}
	}
	
	/**
	 * Returns session by livebetting sessionId. 
	 * @param string $sessionId
	 * @return struct
	 */
	public static function getByLiveSession($sessionId) {
		$sessiondb = Webservice_AbstractWebService::getSessionDb();

		$session = $sessiondb->select()
			->from(static::TABLE_WEB_SESSION)
			->where('livebetting_session = ?',$sessionId)
			->limit(1)
			->query()->fetchObject();

		return $session;
	}

	/**
	 * Generated live betting session
	 * @param string $sessId
	 * @return string 
	 */
	public static function generateLivebettingSession($sesId) {
		return md5('a.\\O6' . $sesId . $_SERVER['HTTP_USER_AGENT'] . '_`P9{l');
	}

	/**
	 * Updates web session timestamp by given live session to prevent expiration.
	 * @param string $liveSessionId
	 * @return boolean TRUE if session was updated
	 */
	public static function updateWebSessionTimestamp($liveSessionId) {
		$db = Webservice_AbstractWebService::getSessionDb();
		$n = $db->update(
			static::TABLE_WEB_SESSION,
			array('time' => time()),
			array('livebetting_session=?' => $liveSessionId)
		);
		return (0 < $n);
	}
} 