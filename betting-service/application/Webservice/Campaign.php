<?php
class Webservice_Campaign {

	const NAMESPACE_GET_PATH = 'It6_Campaign_Get_';
	const NAMESPACE_SPEND_PATH = 'It6_Campaign_Spend_';
	const NAMESPACE_EXCHANGE_PATH = 'It6_Campaign_Exchange_';
	const NAMESPACE_GET = 'get';
	const NAMESPACE_SPEND = 'spend';
	const NAMESPACE_EXCHANGE= 'exchange';

	public static $TABLE_GAME = "ticket_game_result";

	/**
	 * @param string $name
	 * @param string $namespace get or spend or exchange 
	 * @param struct $circumstances
	 * @return boolean
	 */
	public static function putOn($name, $namespace, $circumstances) {
		try {
			$fullName = static::getCampaignFullName($name, $namespace);
			
			if ( !Zend_Loader::isReadable(
					str_replace('_', DIRECTORY_SEPARATOR, $fullName) . '.php' ) )
	
				throw new It6_XmlRpc_Exception("Unknown campaign type: '$name'");
	
			return call_user_func(array($fullName, 'putOn'), $circumstances);
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception('putOn', 0, $e);
		}
	}

	/**	 
	 * @param string $name
	 * @param string $namespace get or spend or exchange
	 * @param struct $circumstances
	 * @return boolean
	 */
	public static function validate($name, $namespace, $circumstances) {
		try {
			$fullName = static::getCampaignFullName($name, $namespace);
			
			if ( !Zend_Loader::isReadable(
					str_replace('_', DIRECTORY_SEPARATOR, $fullName) . '.php' ) )
	
				throw new It6_XmlRpc_Exception("Unknown campaign type: '$name'");
	
			return call_user_func(array($fullName, 'validate'), $circumstances);
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception('validate', 0, $e);
		}
	}


	/**	 
	 * @param string $name
	 * @param string $namespace get or spend or exchange
	 * @param string $method
	 * @param struct $circumstances
	 * @return string returns parameter
	 */
	public static function get($name, $namespace, $method ,$circumstances) {

		try {
			$fullName = static::getCampaignFullName($name, $namespace);
			
			if ( !Zend_Loader::isReadable(
					str_replace('_', DIRECTORY_SEPARATOR, $fullName) . '.php' ) )

				throw new It6_XmlRpc_Exception("Unknown campaign type: '$name'");

			return call_user_func(array($fullName, 'get' . $method), $circumstances);
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception('validate', 0, $e);
		}
	}

	protected static function getCampaignFullName($name, $namespace) {
		switch ( $namespace ) {
			case static::NAMESPACE_GET      : return static::NAMESPACE_GET_PATH . $name;
			case static::NAMESPACE_SPEND    : return static::NAMESPACE_SPEND_PATH . $name;
			case static::NAMESPACE_EXCHANGE : return static::NAMESPACE_EXCHANGE_PATH . $name;
			default         : throw new It6_XmlRpc_Exception("Unknown campaign namespace: '$namespace'");
		}
	}
	
	/**
	 * 
	 * Returns valid preference sizes
	 * @param string $type ticket type one of ('simple','kombi','maxikombi','system')
	 * @param integer $betCount Number of bets on ticket
	 * @param float $stake Ticket stake
	 * @param float $rate Ticket rate
	 * @param integer $userId Identifier of the user
	 * @param $pointTicket
	 * @param integer|null $rateAdvance specifies if the ticket has a point rate advance and how big it is
	 * @return sturct key of structure is preference size, and value is another structure of tow keys: 'valid' true if this size is valid otherwise false  and 'cost' cost of the advance) 
	 */
	public static function getPreferenceRateValidSizes($type,$betCount,$stake,$rate,$userId, $pointTicket = false, $rateAdvance=null) {
		return static::get(
			'PreferenceRate',
			'spend',
			'ValidPreferenceSizes',
			array(
				'type' => $type,
				'betCount' => $betCount,
				'stake' => $stake,
				'rate' => $rate,
				'userId' => $userId,
				'pointTicket' => $pointTicket,
				'rateAdvance' => $rateAdvance
			)
		);
	}
	
	/**
	 *
	 * Check if point ticket with given parameters can be created
	 * @param string $type ticket type one of ('simple','kombi','maxikombi','system')
	 * @param integer $betCount Number of bets on ticket
	 * @param float $stakeInPoints Ticket stake in points
	 * @param array $rates Array of rates of all tips on the ticket
	 * @param integer $userId Identifier of the user
	 * @return boolean
	 */
	public static function validatePointTicket($type,$betCount,$stakeInPoints,$rates,$userId) {
		return static::validate(
			'CreatePointTicket',
			'spend',
			array(
				'type'          => $type,
				'betCount'      => $betCount,
				'stakeInPoints' => $stakeInPoints,
				'rates'         => $rates,
				'userId'        => $userId, 
			)
		);
	}
	
	public static function generateVisitHappyHour() {
		It6_Campaign_Get_HappyHourVisit::generate();		
	}

	/**
	 * Returns initial bonus amount for given ticket and user
	 * @param It6_Models_Ticket|struct $ticket Helper instance or ticket entity
	 * @param float $minRate
	 * @param float $minBets
	 * Zend_Db_Adapter $db
	 * @return float Amount of money bonus balance can be changed by
	 */
	public static function getTicketEntryBonusAmount($ticket, $minRate, $minBets, &$db = null) {
		if (!($ticket instanceof It6_Models_Ticket)) {
			$ticket = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
			$ticket->computeAggregates($db);
		}
		if ($ticket->isMaxicombinatorCompatible()) {
			$data = array();
			foreach ($ticket->combinations as $k => $comb) {
				if ($ticket->isCombinationUsed($k)) {
					foreach ($comb['data'] as $cData) {
						$data[] = array(
							'rate' => $cData['rate'],
							'betCount' => sizeof($cData['bets']),
							'stake' => $comb['stake'],
						);
					}
				}
			}
		}
		else {
			$data = array(array(
				'rate' => $ticket->rate,
				'betCount' => $ticket->betCount,
				'stake' => $ticket->stake,
			));
		}
		$bonus = 0.0;
		foreach ($data as $_data) {
			if (0 != $minRate && $_data['rate'] < $minRate)
				continue;
			if (0 != $minBets && $_data['betCount'] < $minBets)
				continue;
			$bonus += $_data['stake'];
		}
		return $bonus;
	}

	/**
	 * Increment daily balance for initial bonus
	 * @param integer|struct $user User ID or user entity
	 * @param It6_Models_Ticket $ticket
	 * boolean $cancelTicket If TRUE ticket will be substracted from balance (default FALSE is for addition)
	 * integer|string|NULL $time For identification of day to which balance belongs to, NULL for today, integer for UNIX timestamp, string as local datetime
	 * @return boolean TRUE if balance was changed
	 */
	public static function addTicketToEntryBonusBalance($user, $ticket, $cancelTicket = false, $time = null) {
		if (!empty($ticket->cash))
			return false;
		if (is_array($user) && 1 == count($user))
			$user = array_shift($user);
		if (is_numeric($user)) {
			$userId = $user;
			$user = Webservice_User::getById($userId);
			if (empty($user))
				throw new It6_XmlRpc_Exception('User not found. userId=' . $userId);
		}
		$version = $user['ebVersion'];
		if (1 != $version)
			throw new It6_XmlRpc_Exception('Unsupported entry bonus version: ' . $version);
		$from = $user['ebFrom'];
		if (empty($from))
			return false;
		$dbAdmin = Webservice_AbstractWebService::getAdminDb();
		$params = array(
			It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_RATE . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_BETS . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version",
		);
		$params = It6_Models_Parameter::getDataByName($params, $dbAdmin);
		$minRate = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_RATE . ".$version"])
			? 0.0
			: floatval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_RATE . ".$version"]['value'])
		);
		$minBets = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_BETS . ".$version"])
			? 0
			: intval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_BETS . ".$version"]['value'])
		);
		$applicableTime = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version"])
			? 0
			: intval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version"]['value'])
		);
		$maxPerDay = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version"])
			? 0.0
			: floatval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version"]['value'])
		);
		$db = Webservice_AbstractWebService::getMainDb();
		if (0 != $maxPerDay)
			$maxPerDay = It6_Models_Currency::convertAmountToCurrency($user['currencyId'], $maxPerDay, $db);
		$bonus = floatval( self::getTicketEntryBonusAmount($ticket, $minRate, $minBets, $db) );
		if (0 == $bonus)
			return false;
		if ($cancelTicket)
			$bonus *= -1;
		if (!isset($time))
			$time = time();
		$tm = It6_Date::toTimestruct($time);
		$tm['tm_sec'] = $tm['tm_min'] = $tm['tm_hour'] = 0;
		$t = It6_Date::toTimestamp($tm);
		$dbTime = It6_Date::timestampToDb($t);
		It6_DbTransaction::begin($db);
		try {
			$n = $db->update(
				'uzivatel_eb_balance',
				array('balance' => new Zend_Db_Expr("balance+$bonus")),
				array(
					'user_id=?' => $user['userId'],
					'balance_day=?' => $dbTime
				)
			);
			if (0 == $n) { // nothing updated
				$db->insert(
					'uzivatel_eb_balance',
					array(
						'user_id' => $user['userId'],
						'balance_day' => $dbTime,
						'balance' => $bonus,
					)
				);
			}
			$balance = static::getUserEntryBonusBalanceImpl($user, $applicableTime, $maxPerDay);
			$db->update(
				'uzivatel',
				array('entry_bonus_balance' => $balance),
				array('user_id=?' => $user['userId'])
			);
			It6_DbTransaction::commit($db);
			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			throw $e;
		}
	}

	/**
	 * Examine bonus time constraints
	 * @param integer $applicableTime Days for bonus
	 * @param string $dbFrom Database datetime of start of bonus time period
	 * @param boolean $bonusPeriodPassed Will be set to TRUE if bonus time period is over, to TRUE otherwise
	 * @return array (from_timestamp, to_timestamp) Interval of bonus time period
	 */
	private static function getEntryBonusInterval($applicableTime, $dbFrom, &$bonusPeriodPassed = null) {
		$tm = It6_Date::dbDatetimeToTimestruct($dbFrom);
		$tm['tm_sec'] = $tm['tm_min'] = $tm['tm_hour'] = 0;
		$from = It6_Date::toTimestamp($tm);
		$bonusPeriodPassed = false;
		if (0 == $applicableTime)
			$to = time();
		else {
			$tm['tm_sec'] = 59;
			$tm['tm_min'] = 59;
			$tm['tm_hour'] = 23;
			$tm['tm_mday'] += 30 * $applicableTime - 1; // avg_days_per_month * count_of_months (minus one day because we added day in hour+minute+second)
			$to = It6_Date::toTimestamp($tm);
			$bonusPeriodPassed = ($to < time());
		}
		return array($from, $to);
	}

	/**
	 * Computes actual entry bonus balance for user
	 * @param struct $user User entity
	 * @param integer $applicableTime Days for bonus
	 * @param float $maxPerDay Maximal amount that can be used from daily balance (in user currency)
	 * @param boolean $bonusPeriodPassed Returned flag indicating bonus time period is over
	 * @return float Current entry bonus balance for given user
	 */
	private static function getUserEntryBonusBalanceImpl($user, $applicableTime, $maxPerDay, &$bonusPeriodPassed = false) {
		list($from, $to) = static::getEntryBonusInterval($applicableTime, $user['ebFrom'], $bonusPeriodPassed);
		$from = It6_Date::timestampToDb($from);
		$to = It6_Date::timestampToDb($to);
		$stmt = Webservice_AbstractWebService::getMainDb()->select()->from('uzivatel_eb_balance', array('balance'))
			->where('user_id=?', $user['userId'])
			->where('balance_day>=?', $from)
			->where('balance_day<=?', $to)
			->query();
		$balance = 0.0;
		while ($row = $stmt->fetch()) {
			$dayBalance = floatval($row['balance']);
			if (!empty($maxPerDay) && $dayBalance > $maxPerDay)
				$dayBalance = $maxPerDay;
			$balance += $dayBalance;
		}
		return $balance;
	}

	/**
	 * Retrieves current entry bonus balance for given user
	 * @param struct $user
	 * @throws It6_XmlRpc_Exception
	 * @return float|boolean FALSE if entry bonus period did not start, balance otherwise
	 */
	public static function getUserEntryBonusBalance($user) {
		if (is_array($user) && 1 == count($user))
			$user = array_shift($user);
		if (is_numeric($user)) {
			$userId = $user;
			$user = Webservice_User::getById($userId);
			if (empty($user))
				throw new It6_XmlRpc_Exception('User not found. userId=' . $userId);
		}
		$version = $user['ebVersion'];
		if (1 != $version)
			throw new It6_XmlRpc_Exception('Unsupported entry bonus version: ' . $version);
		$from = $user['ebFrom'];
		if (empty($from))
			return false;
		$dbAdmin = Webservice_AbstractWebService::getAdminDb();
		$params = array(
			It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version",
		);
		$params = It6_Models_Parameter::getDataByName($params, $dbAdmin);
		$applicableTime = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version"])
			? 0
			: intval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version"]['value'])
		);
		$maxPerDay = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version"])
			? 0.0
			: floatval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version"]['value'])
		);
		if (0 != $maxPerDay) {
			$db = Webservice_AbstractWebService::getMainDb();
			$maxPerDay = It6_Models_Currency::convertAmountToCurrency($user['currencyId'], $maxPerDay, $db);
		}
		return static::getUserEntryBonusBalanceImpl($user, $applicableTime, $maxPerDay);
	}

	/**
	 * Computes bonus amount and checks if all entry bomus requirements are met
	 * @param integer|struct $user User ID or entity (will be updated to fetched entity if ID was passed)
	 * @param integer|string $day
	 * @param float $bonus Returned bonus amount (only when should be applied, can be zero)
	 * @param boolean $recheck Set to TRUE if only previous bonus application should be rechecked
	 * @return boolean TRUE if apply, FALSE to not apply, NULL if check was performed
	 */
	private static function checkEntryBonus(&$user, $day, &$bonus, $recheck = false) {
		if (is_array($user) && 1 == count($user))
			$user = array_shift($user);
		if (is_numeric($user)) {
			$userId = $user;
			$user = Webservice_User::getById($userId);
			if (empty($user))
				throw new It6_XmlRpc_Exception('User not found. userId=' . $userId);
		}
		if (!isset($user['ebFrom'])) // bonus time period hasn't started
			return null;
		if ($recheck) {
			if (!isset($user['ebApplied'])) // bonus was not applied before, nothing to check
				return null;
		}
		else {
			if (isset($user['ebApplied'])) // bonus was applied before, nothing to check
				return null;
		}
		$version = $user['ebVersion'];
		if (1 != $version)
			throw new It6_XmlRpc_Exception('Unsupported entry bonus version: ' . $version);

		$db = Webservice_AbstractWebService::getMainDb();
		$dbAdmin = Webservice_AbstractWebService::getAdminDb();
		$params = array(
			It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_TOTAL_STAKES_RATIO . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_TOTAL . ".$version",
		);
		$params = It6_Models_Parameter::getDataByName($params, $dbAdmin);
		$applicableTime = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version"])
			? 0
			: intval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version"]['value'])
		);
		$maxPerDay = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version"])
			? 0.0
			: floatval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY . ".$version"]['value'])
		);
		if (0 != $maxPerDay)
			$maxPerDay = It6_Models_Currency::convertAmountToCurrency($user['currencyId'], $maxPerDay, $db);
		$minTotalRatio = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_TOTAL_STAKES_RATIO . ".$version"])
			? 0.0
			: floatval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_TOTAL_STAKES_RATIO . ".$version"]['value'])
		);
		$maxTotal = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_TOTAL . ".$version"])
			? 0.0
			: floatval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_TOTAL . ".$version"]['value'])
		);
		if (0 != $maxTotal)
			$maxTotal = It6_Models_Currency::convertAmountToCurrency($user['currencyId'], $maxTotal, $db);

		
		$bonusPeriodPassed = false; // if time period for entry bonus has passed
		$balance = static::getUserEntryBonusBalanceImpl($user, $applicableTime, $maxPerDay, $bonusPeriodPassed);
		$applyBonus = false;
		$bonus = $user['ebAmount'];
		if (0 != $maxTotal && $bonus > $maxTotal)
			$bonus = $maxTotal;
		if (0 != $minTotalRatio) {
			$minTotal = $bonus * $minTotalRatio;
			if ($balance < $minTotal) {
				if ($bonusPeriodPassed) {
					$bonus = 0.0;
					$applyBonus = true;
				}
			}
			else
				$applyBonus = true;
		}
		return $applyBonus;
	}

	/**
	 * @param integer|struct $user User ID or user entity
	 * integer|string $day Use tickets from the day:
	 *                             $day days before today (one day is default) for integer
	 *                             from given local date for string
	 * @return boolean TRUE if initial bonus was applied (money were transfered), FALSE if not (applied earlier or not met requirements)
	 */
	public static function applyEntryBonus($user, $day = null) {
		$bonus = null;
		$applyBonus = self::checkEntryBonus($user, $day, $bonus, false);
		if (true === $applyBonus) {
			$db = Webservice_AbstractWebService::getMainDb();
			It6_DbTransaction::begin($db);
			try{
				if (0 != $bonus) {
					Webservice_Transaction::make(array(
						'value' => $bonus,
						'userId' => $user['userId'],
						'typeName' => 'user.bonus.entry',
						'currencyId' => $user['currencyId'],
						'hostId' => It6_Models_Host::ID_INTERNET,
						'adminId' => It6_Models_Admin::ID_INTERNET,
					));
					It6_Log::info(
						'Entry bonus was applied. userId=%userId% amount=%amount%',
						It6_Log::TAG_CRONJOB,
						array('userId' => $user['userId'], 'amount' => $bonus)
					);
				}
				$db->update(
					'uzivatel',
					array(
						'entry_bonus_applied' => $bonus,
						'entry_bonus_applied_at' => It6_Date::dbNow(),
					),
					array('user_id=?' => $user['userId'])
				);
				It6_DbTransaction::commit($db);
			}
			catch (Exception $e) {
				It6_DbTransaction::rollback($db);
				throw new It6_XmlRpc_Exception('Cannot apply entry bonus', 0, $e);
			}
			try {
				if (0 != $bonus) {
					$job = array(
						'type' => It6_Models_CronJobType::getTypeIdByName(It6_Models_CronJobType::TYPENAME_EMAIL),
						'params' => array(
							'user_id' => $user['userId'],
							'amount' => $bonus,
							It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'EntryBonusApplied'
						),
					);
				}
				else {
					$job = array(
						'type' => It6_Models_CronJobType::getTypeIdByName(It6_Models_CronJobType::TYPENAME_EMAIL),
						'params' => array(
							'user_id' => $user['userId'],
							'amount' => $bonus,
							It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'EntryBonusTimeout'
						),
					);
				}
				Webservice_CronJob::insert($job);
			}
			catch (Exception $e) {
				It6_Log::err(
					'Entry bonus mail notification not sent.',
					It6_Log::TAG_CAMPAIGN,
					array('userId' => $user['userId']),
					$e
				);
			}
		}
		return ($applyBonus && $bonus > 0);
	}

	public static function applyBirthdayBonus($user, $day = null) {
		$bonus = Webservice_Parameter::getGlobalParameter(It6_Models_Parameter::NAME_BIRTHDAY_BONUS_AMOUNT);

		$db = Webservice_AbstractWebService::getMainDb();
		It6_DbTransaction::begin($db);
		try{
			if (0 != $bonus) {
				Webservice_Transaction::make(array(
					'value' => $bonus,
					'userId' => $user['userId'],
					'typeName' => 'user.bonus.entry',
					'currencyId' => $user['currencyId'],
					'hostId' => It6_Models_Host::ID_INTERNET,
					'adminId' => It6_Models_Admin::ID_INTERNET,
					'notes' => "Narozeninový bonus"
				));
				It6_Log::info(
					'Birthday bonus was applied. userId=%userId% amount=%amount%',
					It6_Log::TAG_CRONJOB,
					array('userId' => $user['userId'], 'amount' => $bonus)
				);
			}
			
			It6_DbTransaction::commit($db);
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception('Cannot apply birthday bonus', 0, $e);
		}
		
	}

	public static function applyRegistrationBonus($user, $day = null) {
		$bonus = Webservice_Parameter::getGlobalParameter(It6_Models_Parameter::NAME_REGISTRATION_BONUS_AMOUNT);

		$db = Webservice_AbstractWebService::getMainDb();
		It6_DbTransaction::begin($db);
		try{
			if (0 != $bonus) {
				Webservice_Transaction::make(array(
					'value' => $bonus,
					'userId' => $user['userId'],
					'typeName' => 'user.bonus.entry',
					'currencyId' => $user['currencyId'],
					'hostId' => It6_Models_Host::ID_INTERNET,
					'adminId' => It6_Models_Admin::ID_INTERNET,
					'notes'	=> "Registrační bonus"
				));
				It6_Log::info(
					'Registration bonus was applied. userId=%userId% amount=%amount%',
					It6_Log::TAG_CRONJOB,
					array('userId' => $user['userId'], 'amount' => $bonus)
				);
			}
			
			It6_DbTransaction::commit($db);
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception('Cannot apply registration bonus', 0, $e);
		}
		
	}

	/**
	 * @param integer|struct $user User ID or user entity
	 * integer|string $day Use tickets from the day:
	 *                             $day days before today (one day is default) for integer
	 *                             from given local date for string
	 * @return boolean TRUE if initial bonus was removed (money were transfered), FALSE if not (still meets bonus requirements or was not applied earlier)
	 */
	public static function removeAppliedEntryBonus($user, $day = null) {
		$bonus = null;
		$applyBonus = self::checkEntryBonus($user, $day, $bonus, true);
		$appliedBonus = floatval($user['ebApplied']);
		if ( (false === $applyBonus || (true === $applyBonus && 0 == $bonus)) && 0 < $appliedBonus) {
			$db = Webservice_AbstractWebService::getMainDb();
			It6_DbTransaction::begin($db);
			try{
				Webservice_Transaction::make(array(
					'value' => -$appliedBonus,
					'userId' => $user['userId'],
					'typeName' => 'user.bonus.entry-cancel',
					'currencyId' => $user['currencyId'],
					'hostId' => It6_Models_Host::ID_INTERNET,
					'adminId' => It6_Models_Admin::ID_INTERNET,
				));
				It6_Log::info(
					'Entry bonus was removed. userId=%userId% amount=%amount%',
					It6_Log::TAG_CRONJOB,
					array('userId' => $user['userId'], 'amount' => $appliedBonus)
				);
				$data = array();
				if (false === $applyBonus) {
					$data = array(
						'entry_bonus_applied' => null,
						'entry_bonus_applied_at' => null,
					);
				}
				else
					$data = array('entry_bonus_applied' => 0);
				$db->update(
					'uzivatel',
					array('entry_bonus_applied' => (false === $applyBonus ? null : 0)),
					array('user_id=?' => $user['userId'])
				);
				It6_DbTransaction::commit($db);
			}
			catch (Exception $e) {
				It6_DbTransaction::rollback($db);
				throw new It6_XmlRpc_Exception('Cannot remove entry bonus', 0, $e);
			}
			return true;
		}
		return false;
	}

	/**
	 * 
	 * Enter description here ...
	 * @param integer|struct $user
	 * @return struct
	 *    from ... local date | empty (if empty, no other fields will be returned)
	 *    version
	 *    currencyId ... user currency ID
	 *    currencyName ... user currency name
	 *    base ... first stake
	 *    balance ... money spent for bonus
	 *    target ... required spent money to get bonus
	 *    applied ... NULL | 0.0 | 0.0 = still running | time over without bonus | time over with bonus
	 *    appliedAt ... NULL | local datetime
	 *    daysLeft ... FALSE | integer days to the end of bonus time (FALSE if bonus was already applied)
	 */
	public static function getEntryBonusInfo($user) {
		if (is_array($user) && 1 == count($user))
			$user = array_shift($user);
		if (is_numeric($user)) {
			$userId = $user;
			$user = Webservice_User::getById($userId);
			if (empty($user))
				throw new It6_XmlRpc_Exception('User not found. userId=' . $userId);
		}
		if (!isset($user['ebFrom'])) // bonus time period hasn't started
			return array('from' => null);
		$version = $user['ebVersion'];
		if (1 != $version)
			throw new It6_XmlRpc_Exception('Unsupported entry bonus version: ' . $version);
		$params = array(
			It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_TOTAL_STAKES_RATIO . ".$version",
			It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_TOTAL . ".$version",
		);
		$params = It6_Models_Parameter::getDataByName($params, $dbAdmin);
		$applicableTime = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version"])
			? 0
			: intval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_APPLICABLE_TIME . ".$version"]['value'])
		);
		$minTotalRatio = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_TOTAL_STAKES_RATIO . ".$version"])
			? 0.0
			: floatval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MIN_TOTAL_STAKES_RATIO . ".$version"]['value'])
		);
		$maxTotal = (
			empty($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_TOTAL . ".$version"])
			? 0.0
			: floatval($params[It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_TOTAL . ".$version"]['value'])
		);
		if (0 != $maxTotal)
			$maxTotal = It6_Models_Currency::convertAmountToCurrency($user['currencyId'], $maxTotal, $db);
		$bonus = ($user['ebAmount'] > $maxTotal ? $maxTotal : $user['ebAmount']);
		$target = $bonus * $minTotalRatio;
		$balance = floatval($user['ebBalance']);
		$balance = ($balance > $target ? $target : $balance);
		if (isset($user['ebApplied']))
			$daysLeft = false;
		else {
			$bonusPeriodPassed = false;
			list($from, $to) = self::getEntryBonusInterval($applicableTime, $user['ebFrom'], $bonusPeriodPassed);
			list($todayFrom, $todayTo) = It6_Date::todayToDbInterval(0, 0, true);
			$daysLeft = floor( ($to - $todayFrom) / (24 * 3600) ) + 1;
			if ($bonusPeriodPassed || $daysLeft <= 0)
				$daysLeft = 0;
		}
		return array(
			'version' => $user['ebVersion'],
			'from' => It6_Date::fromDbAsDate($user['ebFrom']),
			'currencyId' => $user['currencyId'],
			'currencyName' => $user['currencyName'],
			'base' => $user['ebBase'],
			'bonus' => $bonus,
			'balance' => $balance,
			'target' => $target,
			'applied' => $user['ebApplied'],
			'appliedAt' => It6_Date::fromDb($user['ebAppliedAt']),
			'daysLeft' => $daysLeft,
			'username' => $user['username'],
			'to'		=> $to
		);
	}

	/**
	 * Resets and re-computes all daily and total balance of entry bonus for given user.
	 * @param integer $userId
	 * @throws It6_XmlRpc_Exception
	 * @return boolean TRUE if balances was updated, FALSE otherwise
	 */
	public static function recomputeUserEntryBonusBalances($userId) {
		$user = Webservice_User::getById($userId);
		if (empty($user))
			throw new It6_XmlRpc_Exception('User not found. userId=' . $userId);
		if (!isset($user['ebFrom'])) // bonus time period hasn't started
			return false;
		$version = $user['ebVersion'];
		if (1 != $version)
			throw new It6_XmlRpc_Exception('Unsupported entry bonus version: ' . $version);
		$db = Webservice_AbstractWebService::getMainDb();
		$db->delete('uzivatel_eb_balance', array('user_id=?' => $userId));
		$db->update('uzivatel', array('entry_bonus_balance' => 0), array('user_id=?' => $userId));
		$tickets = Webservice_User::getPaidOutTickets($userId);
		foreach ($tickets as $ticket) {
			static::addTicketToEntryBonusBalance($user, $ticket, false, It6_Date::dbDatetimeToTimestamp($ticket['createdTime']));
		}
		return true;
	}

	/**
	 * Returns complete column translation table
	 * for ticket game structure querying.
	 * Fields in structure:
	 *    <ul>
	 *      <li>gameId</li>
	 *      <li>gameName</li>
	 *      <li>gameValidTo</li>
	 *      <li>gameValidFrom</li>
	 *      <li>handlerClass</li>
	 *      <li>eventDependent</li>
	 *      <li>gameVisible</li>
	 *    </ul>
	 * @return struct
	 */
	protected static function getTicketGameColumns() {
		return array(
			'gameId' => 'id',
			'gameName' => 'name',
			'gameValidFrom' => 'valid_from',
			'gameValidTo' => 'valid_to',
			'handlerClass' => 'handler_class',
			'eventDependent' => 'event_dependent',
			'gameVisible' => 'visible',
		);
	}

	/**
	 * Retrieves data of "ticket game" campaign
	 * @param integer $gameId Database ID of ticket game campaign
	 * @return struct Data of ticket game campaign, structure is described in getTicketGameColumns()
	 * @see getTicketGameColumns()
	 */
	public static function getTicketGame($gameId) {
		static $cache = array();
		if (!array_key_exists($gameId, $cache)) {
			$db = Webservice_AbstractWebService::getMainDb();
			$rows = $db->select()
				->from(
					'ticket_game_campaign',
					static::getTicketGameColumns()
				)
				->where('id=?', $gameId)
				->query()
				->fetchAll();
			$cache[$gameId] = (empty($rows) ? null : $rows[0]);
		}
		return $cache[$gameId];
	}

	/**
	 * Retrieves data of all game in campaign
	 * @return struct Data of all game campaign, structure is described in getTicketGameColumns()
	 * @see getTicketGameColumns()
	 */
	public static function getAllTicketGame() {
		$db = Webservice_AbstractWebService::getMainDb();
		$rows = $db->select()
			->from(
				'ticket_game_campaign',
				static::getTicketGameColumns()
			)
			->query()->fetchAll();
		return $rows;
	}

	/**
	* Retrieves data of "ticket game" campaigns that are valid in specified time
	* @param NULL|integer|string $validTime If value is empty then time validity of game
	*                            will be checked against current time. 
	*                            Integer is used for UNIX timestamp and string for DB datetime
	*                            to be checked against.
	* @return array List of structures, same structures as in Webservice_Campaign::getTicketGameColumns()
	* @see getTicketGameColumns()
	*/
	public static function getValidTicketGames($validTime = null) {
		$db = Webservice_AbstractWebService::getMainDb();
		if (empty($validTime)) {
			$dbValidTime = It6_Date::dbNow();
		}
		else if (is_numeric($validTime)) {
			$dbValidTime = It6_Date::timestampToDb(intval($validTime));
		}
		else {
			$dbValidTime = $validTime;
		}
		$rows = $db->select()
			->from(
				'ticket_game_campaign',
				static::getTicketGameColumns()
			)
			->where('valid_from<=?', $dbValidTime)
			->where('valid_to>=?', $dbValidTime)
			->query()
			->fetchAll();
		return $rows;
	}

	/**
	 * Calls game handler class, and stores ticket game data if needed
	 * @param It6_Models_Ticket $ticket
	 * @param struct|It6_Campaign_TicketGame_HandlerClass $game
	 *        Structure with game data
	 *        (see Webservice_Campaign::getTicketGameColumns() for game structure description)
	 *        of handler class instance.
	 * @return boolean TRUE if ticket was used in game, FALSE otherwise
	 * @see getTicketGameColumns()
	 */
	public static function useTicketInGame($ticket, $game) {
		if ($game instanceof It6_Campaign_TicketGame_HandlerClass) {
			$gameHandler = $game;
			$game = $gameHandler->getGame();
		}
		else {
			if (empty($game['handlerClass'])) {
				return false;
			}
			$class = $game['handlerClass'];
			$gameHandler = new $class($game);
		}
		$db = Webservice_AbstractWebService::getMainDb();
		$evaluation = $gameHandler->evaluateTicket($ticket, $db);
		if (false !== $evaluation) {
			$db->insert(
				static::$TABLE_GAME,
				array(
					'game_id' => $game['gameId'],
					'ticket_id' => $ticket->id,
					'evaluation' => $evaluation,
				)
			);
			It6_Log::info(
				'Ticket #%ticketId% used in ticket game "%gameName%" with evaluation: %evaluation%',
				It6_Log::TAG_CAMPAIGN,
				array(
					'ticketId' => $ticket->id,
					'gameId' => $game['gameId'],
					'gameName' => $game['gameName'],
					'evaluation' => $evaluation,
				)
			);
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Removes ticket from game
	 * @param It6_Models_Ticket|array|integer $ticket Ticket model instance or ticket ID(s) 
	 * @param struct|NULL $game Structure with game data, see Webservice_Campaign::getTicketGameColumns() for game structure description.
	 *                    If NULL is passed, ticket is removed from all games
	 * @return integer|NULL|array If single ticket was passed, list of game IDs is returned.
	 *                            If list of ticket IDs was passed, map (ticket ID => list of game IDs) will be returned.
	 * @see getTicketGameColumns()
	 */
	public static function removeTicketFromGame($ticket, $game = null) {
		$db = Webservice_AbstractWebService::getMainDb();
		if ($ticket instanceof  It6_Models_Ticket) {
			$ticketIds = $ticket->id;
		}
		else {
			$ticketIds = $ticket; // should be one or list of IDs
		}
		$select = $db->select()
			->from(static::$TABLE_GAME, array(
				'gameId' => 'game_id',
				'ticketId' => 'ticket_id',
			))
			->where('ticket_id IN (?)', $ticketIds);
		if (isset($game)) {
			$select->where('game_id=?', $game['gameId']);
		}
		$rows = $select->query()->fetchAll();
		$ticketGames = array();
		$where = array();
		foreach ($rows as $row) {
			$ticketId = $row['ticketId'];
			$gameId = $row['gameId'];
			$ticketGames[$ticketId][] = $gameId;
			$where[] = "(game_id=$gameId AND ticket_id=$ticketId)";
		}
		if (!empty($ticketGames)) {
			$db->delete(static::$TABLE_GAME, implode(' OR ', $where));
		}
		if (is_array($ticketIds)) {
			foreach ($ticketIds as $ticketId) {
				if (!isset($ticketGames[$ticketId])) {
					$ticketGames[$ticketId] = array();
				}
			}
			return $ticketGames;
		}
		else {
			return (isset($ticketGames[$ticketIds]) ? $ticketGames[$ticketIds] : array());
		}
	}

	/**
	 * Get data of best tickets in given ticket game 
	 * @param integer $gameId
	 * @param integer $count Count of top tickets to retrieve
	 * @return array List of ticket data ordered from the best to the worst
	 */
	public static function getTicketGameTopTickets($gameId, $count) {
		$db = Webservice_AbstractWebService::getMainDb();

		$tm = localtime(time(), true);
		$start = mktime(
				0,
				0 ,
				0,
				$tm['tm_mon'] + 1 ,
				1,
				$tm['tm_year'] + 1900
		);
		$start = It6_Date::timestampToDb($start);

		return $db->select()
			->from(
				array('g' => static::$TABLE_GAME),
				array('evaluation' => 'evaluation')
			)
			->join(
				array('t' => 'ticket'),
				'g.ticket_id=t.ticket_id',
				array(
					'ticketId' => 'ticket_id',
					'rate' => 'rate_real',
					'won' => 'win_real',
				)
			)
			->join(
				array('u' => 'uzivatel'),
				't.user_id=u.user_id',
				array('nick' => 'nick')
			)
			->join(
				array('c' => 'mena'),
				'u.mena_id=c.mena_id',
				array('currencyName' => 'mena_text')
			)
			->where('g.game_id = ?', $gameId)
			->where('g.cancelled = 0')
			->where('t.zalozen > ?', $start)
			->order('g.evaluation DESC')
			->limit($count)
			->query()
			->fetchAll();
	}

	/**
	 * Get data of n-th best ticket in ticket game
	 * @param integer $gameId
	 * @param integer $rank The best has rank 1
	 * @return struct|NULL Complete ticket entity or NULL if there was no ticket with given rank
	 */
	public static function getTicketGameTicketByRank($gameId, $rank, $langId = null) {
		$tm = localtime(time(), true);
		$start = mktime(
				0,
				0 ,
				0,
				$tm['tm_mon'] + 1 ,
				1,
				$tm['tm_year'] + 1900
		);
		$start = It6_Date::timestampToDb($start);

		$db = Webservice_AbstractWebService::getMainDb();
		$rows = $db->select()
			->from(
				array('r' => static::$TABLE_GAME),
				array('ticketId' => 'ticket_id')
			)
			->join(
				array('g' => 'ticket_game_campaign'),
				'g.id=r.game_id',
				array('gameName' => 'name')
			)
			->join(
				array('t' => 'ticket'),
				'r.ticket_id=t.ticket_id',
				array(
					'ticketId' => 'ticket_id',
					'rate' => 'rate_real',
					'won' => 'win_real',
				)
			)
			->where('r.game_id=?', $gameId)
			->where('t.zalozen > ?', $start)
			->order('r.evaluation DESC')
			->limit(1, intval($rank) - 1)
			->query()
			->fetchAll();
		if (empty($rows)) {
			return null;
		}
		$row = $rows[0];
		$ticket = Webservice_Ticket::getByIdAndUserComplete($row['ticketId'], null, $langId, true);
		$ticket = $ticket[0];
		$result = array(
			'gameName' => $row['gameName'],
			'ticket' => $ticket,
		);
		return $result;
	}

	/**
	 * Get data of n-th ticket in ticket game
	 * @param integer $gameId
	 * @return struct|NULL Complete ticket entity or NULL if there was no ticket with given rank
	 */
	public static function getTicketsByGame($gameId, $dateFrom = null, $dateTo = null, $orders = null, $cancelled = null, $allowed = null, $handle = null) {

		if (count($orders) > 1) {
			foreach ($orders as $order) {
				if (strpos($order, '_') !== false) {
					$order_sql = explode('_', $order);
				}
			}
		} else {
			$order_sql = explode(':', $orders[0]);
		}

		if ( empty($dateFrom) ) $dateFrom = '2000-01-01';
		if ( empty($dateTo) ) $dateTo = It6_Date::dbNowAsDate();

		$fnFixDate = function($d) {
			if (1 == preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $d, $matches)) {
				return It6_Date::timestampToDate(mktime(0,0,0,$matches[2],$matches[3],$matches[1]));
			} else {
				return $d;
			}
		};
		$dateFrom = $fnFixDate($dateFrom);
		$dateTo = $fnFixDate($dateTo);
		list($dateFrom, $dateTo) = It6_Date::dateToDbInterval($dateFrom, $dateTo);

		$db = Webservice_AbstractWebService::getMainDb();
		$select = $db->select()
		->from(
			array('r' => static::$TABLE_GAME),
			array('ticketId' => 'ticket_id', 'evaluation' => 'evaluation', 'cancelled' => 'cancelled')
		)
		->join(
			array('t' => 'ticket'),
			'r.ticket_id = t.ticket_id',
			array(
				'handle' => 'handle', 'cash' => 'castka', 'rate' => 'rate_real', 
				'win' => 'win_real', 'userId' => 'user_id', 'created' => 'zalozen' 
			)
		)
		->join(
			array('u' => 'uzivatel'),
			'u.user_id = t.user_id',
			array('firstname' => 'jmeno', 'lastname' => 'prijmeni', 'nick' => 'nick')
		)
		->where('r.game_id = ?', $gameId)
		->where('zalozen >= ?', $dateFrom)
		->where('zalozen < ?', $dateTo);

		if ($cancelled == 1 && $allowed == 1) $select = $select->where('cancelled = 1 OR cancelled = 0');
		else if ($cancelled == 1 && $allowed == 0) $select = $select->where('cancelled = 1');
		else if ($cancelled == 0 && $allowed == 1) $select = $select->where('cancelled = 0');
		else $select = $select->where('cancelled IS NULL');

		if (!empty($handle)) $select = $select->where('t.handle = ?', $handle);

		$select = $select->order($order_sql[0].' '.$order_sql[1]);

		// $log = new Zend_Log(new Zend_Log_Writer_Firebug());
		// $log->info($select->__tostring());

		$rows = $select->query()->fetchAll();

		if (empty($rows)) return null;
		else return $rows;
	}

	/**
	 * Cancelled ticket on Id
	 * @param ticket id, game id
	 * @return true/false
	 */
	public static function cancellTicket($ticket, $game) {
		$db = Webservice_AbstractWebService::getMainDb();
		It6_DbTransaction::begin();
		try {
			$db->update(static::$TABLE_GAME, array('cancelled' => 1), array('ticket_id = (?)' => $ticket));
			It6_DbTransaction::commit();
			It6_Log::info(
				"Ticket '%ticket_id%' were cancelled from game (cancelled = '%val%').",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticket_id' => $ticket, 'val' => 1)
			);
			It6_Campaign_TicketGame_HandlerClass::invalidateCache($game, $ticket);
			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback();
			It6_Log::errr(
				"Error canclelling ticket 'ticket_id%'.  (cancelled = '%val%')",
				It6_Log::TAG_ADMIN_OPERATION,
				array('users_id' => $ticket, 'val' => 1)
			);
			throw new It6_XmlRpc_Exception('Campaign::cancellTicket', 0, $e);
			return false;
		}
	}

	/**
	 * Allow ticket on Id
	 * @param ticket id, game id
	 * @return true/false
	 */
	public static function allowTicket($ticket, $game) {
		$db = Webservice_AbstractWebService::getMainDb();
		It6_DbTransaction::begin();
		try {
			$db->update(static::$TABLE_GAME, array('cancelled' => 0), array('ticket_id = (?)' => $ticket));
			It6_DbTransaction::commit();
			It6_Log::info(
				"Ticket '%ticket_id%' were allowed (cancelled = '%val%').",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticket_id' => $ticket, 'val' => 0)
			);
			It6_Campaign_TicketGame_HandlerClass::invalidateCache($game, $ticket);
			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback();
			It6_Log::errr(
				"Error allowing ticket '%ticket_id%'. (cancelled = '%val%')",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticket_id'=> $ticket, 'zak' => 1)
			);
			throw new It6_XmlRpc_Exception('Campaign::allowTicket', 0, $e);
			return false;
		}
	}

	/**
	 * Retrieves all ticket game data for ticket games that have
	 * event dependency flag set.
	 * @param integer|NULL $langId If set, texts are translated using given language ID
	 * @return array List of ticket game structures
	 * @see getTicketGameColumns()
	 */
	public static function getEventDependentTicketGames($langId = null) {
		$db = Webservice_AbstractWebService::getMainDb();
		$columns = static::getTicketGameColumns();
		if ($langId) {
			$columns['gameName'] = new Zend_Db_Expr('COALESCE(p.text, g.name)');
		}
		$select = $db->select()
			->from(
				array('g' => 'ticket_game_campaign'),
				$columns
			)
			->where('g.event_dependent<>0');
		if ($langId) {
			$select->joinLeft(
				array('p' => 'preklady'),
				'p.index_pole=g.name AND p.lang_id=' . intval($langId),
				array()
			);
		}
		return $select->query()->fetchAll();
	}

	/**
	 * Retrieves event-ticket_game dependencies for given event(s) or game(s)
	 * @param integer|array $id One or list of event or game IDs
	 * @param boolean $isEventId TRUE if $id is event ID, FALSE if $id is ticket game ID
	 * @param integer $langId Language for translation of returned texts.
	 * @return array If $isEvent is TRUE, for one $id is list of game structs returned,
	 *               for list in $id map (event ID => list of game structs) is returned.
	 *               If $isEvent is FALSE, for one $id is list of event structs returned,
	 *               for list in $id map (game ID => list of event structs) is returned.
	 *               Returned structures have fields: id, name. Names are translated.
	 */
	public static function getEventTicketGameDependency($id, $isEventId, $langId) {
		$db = Webservice_AbstractWebService::getMainDb();
		$select = $db->select()
			->from(
				array('e' => 'ticket_game_event'),
				array('gameId' => 'game_id', 'eventId' => 'udalost_id')
			);
		if ($isEventId) {
			$groupBy = 'eventId';
			$grouped = 'gameId';
			$select->join(
					array('g' => 'ticket_game_campaign'),
					'e.game_id=g.id',
					array()
				)
				->joinLeft(
					array('p' => 'preklady'),
					'p.index_pole=g.name AND p.lang_id=' . intval($langId),
					array(
						'name' => new Zend_Db_Expr('COALESCE(p.text, g.name)'),
					)
				)
				->where('e.udalost_id IN (?)', $id);
		}
		else {
			$groupBy = 'gameId';
			$grouped = 'eventId';
			$select->join(
					array('u' => 'udalost'),
					'e.udalost_id=u.udalost_id',
					array()
				)
				->joinLeft(
					array('p' => 'preklady'),
					'p.index_pole=u.nazev AND p.lang_id=' . intval($langId),
					array(
						'name' => new Zend_Db_Expr('COALESCE(p.text, u.nazev)'),
					)
				)
				->where('e.game_id IN (?)', $id);
		}
		$rows = $select->query()->fetchAll();
		$result = array();
		foreach ($rows as $row) {
			$result[$row[$groupBy]][] = array(
				'id' => $row[$grouped],
				'name' => $row['name'],
			);
		}
		if (is_array($id)) {
			foreach ($id as $_id) {
				if (!isset($result[$_id])) {
					$result[$_id] = array();
				}
			}
			return $result;
		}
		else {
			return (isset($result[$id]) ? $result[$id] : array());
		}
	}

	/**
	 * Sets event-ticket_game associations, old associations
	 * won't be preserved
	 * @param array $ids Map (event ID => list of game IDs) or map (game ID => list of event IDs)
	 * @param boolean $groupedByEvent If TRUE then $ids keys are event IDs,
	 *                                otherwise $ids keys are game IDs.
	 * @return boolean TRUE on success, FALSE on error
	 */
	public static function setEventTicketGameDependency($ids, $groupedByEvent) {
		if (empty($ids)) {
			return true;
		}
		$db = Webservice_AbstractWebService::getMainDb();
		It6_DbTransaction::begin($db);
		try {
			if ($groupedByEvent) {
				$groupBy = 'udalost_id';
				$grouped = 'game_id';
			}
			else {
				$groupBy = 'game_id';
				$grouped = 'udalost_id';
			}
			$db->delete(
				'ticket_game_event',
				array("$groupBy IN (?)" => array_keys($ids))
			);
			foreach ($ids as $groupId => $groupedIds) {
				foreach ($groupedIds as $groupedId) {
					$db->insert(
						'ticket_game_event',
						array(
							$groupBy => $groupId,
							$grouped => $groupedId,
						)
					);
				}
			}
			It6_DbTransaction::commit($db);
			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			It6_Log::err(
				'Could not set events/ticket games dependency',
				It6_Log::TAG_CAMPAIGN,
				array(
					'grouping' => ($groupedByEvent ? 'event -> games' : 'game -> events'),
					'dependecies' => $eventGameIds,
				)
			);
			return false;
		}
	}

	/**
	 * Re-evaluates tickets for given ticket game from scratch.
	 * @param integer $gameId
	 * @return array|boolean List of tickets that was used in game or FALSE on error
	 */
	public static function reevaluateTicketGame($gameId) {
		$game = static::getTicketGame($gameId);
		if (empty($game)) {
			It6_Log::warn('Unknown ticket game ID', It6_Log::TAG_CAMPAIGN, array('gameId' => $gameId));
			return false;
		}
		$handlerClass = $game['handlerClass'];
		$handler = new $handlerClass($game);
		$db = Webservice_AbstractWebService::getMainDb();
		It6_DbTransaction::begin($db);
		try {
			$ticketIds = $handler->getTicketsForReevaluation($db);
			if (false === $ticketIds) {
				It6_Log::err(
					'Cannot retrieve ticket IDs for ticket game',
					It6_Log::TAG_CAMPAIGN,
					array('gameId' => $gameId, 'gameName' => $game['gameName'])
				);
				return false;
			}
			$db->delete(
				static::$TABLE_GAME,
				array('game_id=?' => $gameId)
			);
			$usedTicketIds = array();
			if (!empty($ticketIds)) {
				foreach ($ticketIds as $ticketId) {
					$ticket = It6_Models_TicketFactory::newTicket(
						$ticketId, It6_Models_Ticket::DATA_ADMIN_TICKET, false, true
					);
					$used = static::useTicketInGame($ticket, $handler);
					if ($used) {
						$usedTicketIds[] = $ticketId;
					}
				}
			}
			It6_DbTransaction::commit($db);
			It6_Log::info(
				'Ticket game was reevaluated',
				It6_Log::TAG_CAMPAIGN, 
				array(
					'gameId' => $gameId,
					'gameName' => $game['gameName'],
					'ticketCountInGame' => count($usedTicketIds),
				)
			);
			return $usedTicketIds;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			throw $e;
		}
	}
}
