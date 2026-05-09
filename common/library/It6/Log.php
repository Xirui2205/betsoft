<?php

require_once('It6/Php.php');

final class It6_Log {

	private static $logger = null;

	const TAG_DEFAULT = 'default';
	const TAG_PHP = 'php';
	const TAG_WEBSERVICE = 'webservice';
	const TAG_WEBSERVICE_INPUT = 'webservice-input';
	const TAG_ADMIN = 'admin';
	const TAG_WEB = 'web';
	const TAG_CRONJOB = 'cronjob';
	const TAG_USER_OPERATION = 'user-operation';
	const TAG_ADMIN_OPERATION = 'admin-operation';
	const TAG_LIVEBET_OPERATION = 'livebet-operation';
	const TAG_BOOKMAKER_OPERATION = 'bookmaker-operation';
	const TAG_BETRADAR_OPERATION = 'betradar-operation';
	const TAG_BETRADAR_BET_OPERATION = 'betradar-bet-operation';
	const TAG_BETRADAR_RATE_OPERATION = 'betradar-rate-operation';
	const TAG_BETRADAR_RESULT_OPERATION = 'betradar-result-operation';
	const TAG_BETRADAR_SCORE_OPERATION = 'betradar-score-operation';
	const TAG_BETRADAR_TEAM_OPERATION = 'betradar-team-operation';
	const TAG_DEPRECATED_OPERATION = 'deprecated-operation';
	const TAG_TRANSACTION = 'transaction';
	const TAG_TRANSACTION_IMPORT = 'transaction-import';
	const TAG_ALERT = 'alert';
	const TAG_CAMPAIGN = 'campaign';
	const TAG_DEMON = 'demon';
	const TAG_TICKET_APPROVAL = 'ticket-approval';
	const TAG_DEPOSIT = 'deposit'; // nestandardni udalosti souvisejici s vklady
	const TAG_MUZO_VERIFY = 'muzo-verify'; // povinny log pro overovani podpisu odpovedi ze systemu PayMUZO
	const TAG_MOBILEM_API = 'mobilem-api';
	const TAG_TICKET_STATISTICS = 'ticket-statistics';
	const TAG_SYNCHRONIZATION = 'synchronization';
	const TAG_USER_LIMIT_ACTUALISATION = 'user-limit-actualisation';

	/**
	 * Sets up log record.
	 * @param string $name Logging method name = log level (emerg, err, warn, info, notice, debug)
	 * @param array $arguments Arguments of logging method
	 *    0 ... message (required)
	 *    1 ... tag (required)
	 *    2 ... user data as associative array (implicit: betId, couponId, userId, hostId, adminId)
	 *    3 ... exception
	 *    4 ... file
	 *    5 ... line
	 */
	public static function __callStatic($name, $arguments) {
		if ( empty(self::$logger) )
			self::initialize();

		$message = $arguments[0];

		if ( !empty($arguments[1]) )
			self::$logger->setEventItem('tag', $arguments[1]);
		else
			self::$logger->setEventItem('tag', self::TAG_DEFAULT);

		$adminId = null;
		$hostId = null;
		if ( !empty($arguments[2]) ) {
			$argsArr = It6_ArrayWrapper::toNativeArray($arguments[2]);

			$args = self::argsToString($argsArr);
			self::$logger->setEventItem('args', $args);

			$betVal = self::findKeys(array('bet','betId','bet_id'), $argsArr);
			if (!empty($betVal))
				self::$logger->setEventItem('bet', $argsArr[$betVal]);
			else
				self::$logger->setEventItem('bet', null);

			$ticketVal = self::findKeys(array('ticket','ticketId','ticket_id'), $argsArr);
			if (!empty($ticketVal))
				self::$logger->setEventItem('ticket', $argsArr[$ticketVal]);
			else
				self::$logger->setEventItem('ticket', null);

			$couponVal = self::findKeys(array('coupon','couponId','coupon_id'), $argsArr);
			if (!empty($couponVal))
				self::$logger->setEventItem('coupon', $argsArr[$couponVal]);
			else
				self::$logger->setEventItem('coupon', null);

			$userVal = self::findKeys(array('user','userId','user_id'), $argsArr);
			if (!empty($userVal))
				self::$logger->setEventItem('user', $argsArr[$userVal]);
			else
				self::$logger->setEventItem('user', null);

			$hostVal = self::findKeys(array('host','hostId','host_id'), $argsArr);
			if (!empty($hostVal)) {
				$hostId = $argsArr[$hostVal];
				self::$logger->setEventItem('host', $hostId);
			}
			else
				self::$logger->setEventItem('host', null);

			$adminVal = self::findKeys(array('admin','adminId','admin_id'), $argsArr);
			if (!empty($adminVal))
				$adminId = $argsArr[$adminVal];
				
			if ( is_string($message) )
				$message = self::formatMessage($message, It6_ArrayWrapper::toNativeArray($arguments[2]));
		}
		else {
			self::$logger->setEventItem('args', null);
			self::$logger->setEventItem('bet', null);
			self::$logger->setEventItem('ticket', null);
			self::$logger->setEventItem('coupon', null);
			self::$logger->setEventItem('user', null);
		}
		if ( !empty($arguments[3]) ) {
			$exception = self::exceptionToString($arguments[3]);
			self::$logger->setEventItem('exception', $exception);
		}
		else {
			self::$logger->setEventItem('exception', null);
		}

		if ( !empty($arguments[4]) && !empty($arguments[5]) ) {
			$file = $arguments[4];
			self::$logger->setEventItem('file', $file);
			$line = $arguments[5];
			self::$logger->setEventItem('line', $line);
		}
		else {
			$array = debug_backtrace();
			self::$logger->setEventItem('file', $array[1]['file']);
			self::$logger->setEventItem('line', $array[1]['line']);
		}

		// May be this ip adress coudl't be part of the identity
		$ip = It6_Php::getRemoteAddr();
		self::$logger->setEventItem(
			'ip',
			!empty($ip) ? $ip : null);

		if ( Zend_Registry::isRegistered('acl') ) {
			$acl =Zend_Registry::get('acl');
			if (empty($adminId))
				$adminId = $acl->getIdentity(It6_Acl::IDNAME_ADMIN);
			if (empty($hostId))
				$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
		}
		self::$logger->setEventItem('admin', $adminId);
		self::$logger->setEventItem('host', $hostId);

		if ( Zend_Registry::isRegistered('user_id') ) {
			// This will not work for WS called remotely from web
			self::$logger->setEventItem('user', Zend_Registry::get('user_id'));
		}

		self::$logger->setEventItem('timestamp', time());
		self::$logger->$name($message);

	}

	public static function errorHandler($errno,$err,$file,$line) {

		switch ($errno) {
			case E_STRICT:
			case E_NOTICE:
			case E_USER_NOTICE:
				self::warn($err, self::TAG_PHP, null, null, $file, $line);
				break;
			case E_WARNING:
			case E_USER_WARNING:
				self::err($err, self::TAG_PHP, null, null, $file, $line);
				break;
			case E_ERROR:
			case E_USER_ERROR:
				self::crit($err, self::TAG_PHP, null, null, $file, $line);
				break;
			default:
				self::crit($err, self::TAG_PHP, null, null, $file, $line);
				break;
		}

		return true;
	}

	public static function fatalErrorHandler() {
		$error = error_get_last();

		if ( empty( $error ) ) return;
			//self::emerg('Parse error', self::TAG_PHP);

		switch ( $error['type'] ) {
			case E_ERROR:
				self::emerg($error['message'], self::TAG_PHP, null, null, $error['file'], $error['line']);
				break;
			case E_CORE_ERROR:
				self::emerg($error['message'], self::TAG_PHP, null, null, $error['file'], $error['line']);
				break;
			case E_COMPILE_ERROR:
				self::emerg($error['message'], self::TAG_PHP, null, null, $error['file'], $error['line']);
				break;
			default:
				self::err($error['message'], self::TAG_PHP, null, null, $error['file'], $error['line']);
				break;
		}
	}

	protected static function registerErrorHandlers() {
		set_error_handler(array('It6_Log', 'errorHandler'));
		ini_set('display_errors', 0);
		register_shutdown_function(array('It6_Log', 'fatalErrorHandler'));
	}

	public static function initialize() {
		global $LOG_CONFIG;
		self::$logger = Zend_Log::factory($LOG_CONFIG);
		self::registerErrorHandlers();
	}

	private static function argsToString($args) {
		return Zend_Json::encode($args);
	}

	public static function exceptionToString($exception) {
		return $exception->getMessage() . "\nStack trace:\n" . $exception->getTraceAsString();
	}

	private static function formatMessage($message, $args) {
		$formatter = new Zend_Log_Formatter_Simple($message);
		return $formatter->format($args);
	}

	private static function findKeys($names, $argsArr) {
		$out = '';
		foreach ($names as $b) {
			if (array_key_exists($b,$argsArr))
				$out = $b;
		}
		return $out;
	}
}
