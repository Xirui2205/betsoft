<?php

/**
 * Transaction related static methods.
 * @author Pavel Klinger
 * @see Entities_User
 *
 */
class Webservice_User extends Webservice_AbstractWebService  {

	const AGE_LIMIT = 18;
	
	public static $TABLE						= "uzivatel";
	public static $TABLE_PREFIX					= "usr";
	public static $TABLE_USER_HAS_PARAMETER		= "user_has_parameter";
	public static $TABLE_USER_LIMIT				= "limity_user";
	public static $USER_IM_DATA_TABLE			= "uzivatel_im_data";
	public static $TABLE_SEARCH					= "user_search";
	public static $USER_IM_DATA_TABLE_PREFIX	= "uid";
	public static $TABLE_USER_BANK_ACC			= "user_bank_account";
	public static $TABLE_USER_BANK_ACC_PREFIX	= "uba";
	public static $TABLE_BANK					= "bank";
	public static $TABLE_BANK_PREFIX			= "bnk";
	public static $TABLE_CLIENT_CARD_FEED       = "client_card_number_feed";
	public static $ENTITY_NAME					= "Entities_User";
	public static $IDENTITY						= "user_id";
	public static $ADMIN_DB						= "vic_admin";
	public static $MAIN_DB						= "vic_main";
	public static $TABLE_BRANCH					= "branch";
	public static $TABLE_BRANCH_PREFIX			= "br";
	public static $TABLE_LOGIN_ATTEMPTS			= "uzivatel_block";
	public static $TABLE_AREACODE_PREFIX		= "ac"; // pro tabulku zeme
	
	protected static $CONV = array(
		'usr.user_id'			=> 'userId',
		'usr.handle'			=> 'userHandle',
		'jmeno'					=> 'firstName',
		'prijmeni'				=> 'lastName',
		"nick"					=> 'username',
		'heslo'					=> 'password',
		'pohlavi'				=> 'sex',
		'datum_narozeni'		=> 'birthDate',
		'usr.email'				=> 'email',
		'telefon'				=> 'phone',
		'ulice'					=> 'street',
		'misto'					=> 'town',
		'zeme_id'				=> 'countryId',
		'psc'					=> 'zip',
		'cur.mena_id'			=> 'currencyId',
		'cur.mena_text'			=> 'currencyName',
		'usr.lang_id'			=> 'languageId',
		'branch_id'				=> 'branchId',
		'posledni_prihlaseni'	=> 'lastLoginTime',
		'vyprseni_session'		=> 'sessionTimeout',
		'datum_registrace'		=> 'registrationTime',
		'datum_aktivace'		=> 'activationTime',
		'newsletter'			=> 'sendNewsletter',
		'anonymous'				=> 'anonymous',
		'e_testovaci'			=> 'isTesting',
		'vyber_status'			=> 'canWithdraw',
		'vyhernost'				=> 'winRatio',
		'zakazany'				=> 'isForbiden',
		'block'					=> 'block',
		'block_ip'				=> 'blockIp',
		'block_time'			=> 'blockTime',
		'max_bet'				=> 'maxBet',
		'self_excluded_until'	=> 'selfExcludedUntil',
		'ucet_status'			=> 'accountStatus',
		'finance_rating'		=> 'financeRating',
		'book_info'				=> 'noteBookmaker',
		'watched'				=> 'watched',
		'anonymous'				=> 'anonymous',
		'client_card_number'	=> 'clientCardNumber',
		'agreement_number'      => 'agreementNumber',
		'created_by_admin_id'	=> 'createdByAdminId',
		'allowed_by_admin_id'	=> 'allowedByAdminId',
		'citizen_id'			=> 'citizenId',
		'handle'				=> 'handle',
		'entry_bonus_base'		=> 'ebBase',
		'entry_bonus_amount'	=> 'ebAmount',
		'entry_bonus_from'		=> 'ebFrom',
		'entry_bonus_balance'	=> 'ebBalance',
		'entry_bonus_applied'	=> 'ebApplied',
		'entry_bonus_applied_at'=> 'ebAppliedAt',
		'entry_bonus_version'	=> 'ebVersion',
		'usr.area_code'			=> 'areaCode',
		'ac.predvolba'			=> 'areaCodeName',
		'address'				=> 'address',
		'lat'					=> 'lat',
		'lng'					=> 'lng',
		
		'cur.mena_text'			=> 'currencyName',
		'ctr.nazev'				=> 'countryName',
		'lang.alt_text'			=> 'languageName',
		'uid.zustatek'			=> 'balance',
		'uid.zetony'			=> 'chips',
		'uid.dluh'				=> 'debit',
		'uba.account_number'	=> 'accountNumber',
		'uba.account_prefix'	=> 'accountPrefix',
		'uba.bank_id'			=> 'bankId',
		'bnk.bank_name'			=> 'bankName',
		'bnk.bank_code'			=> 'bankCode',
		
		'br.handle'				=> 'branchHandle',
		'br.name'				=> 'branchName',
		'ban_note'				=> 'banNote',
		'pa.balance'            => 'mainPointsBalance',
		'can_print_agreement'   => 'canPrintAgreement'
/*		'individualni_max_vklad' => 'individual_max_vklad',
		'zakazany' => 'banned',
		'misto' => 'misto',
		'block' => 'block',
		'block_ip' => 'block_ip',
		'vyhernost_game' => 'vyhernost_game',
		'bet_stats_win' => 'bet_stats_win',
		'bet_stats_lose_acc' => 'bet_stats_lose_acc',
		'bet_stats_lose_book' => 'bet_stats_lose_book',
		'bet_total' => 'bet_total',
		'win_ticket' => 'win_ticket',
		'lose_ticket' => 'lose_ticket',
		'delete_ticket' => 'delete_ticket',
		'num_bet_ticket' => 'num_bet_ticket',
		'bet_total2' => 'bet_total2',
		'ticket_num' => 'ticket_num',
		'e_testovaci' => 'e_testovaci',
		'block_play' => 'block_play',
		'castka_m' => 'castka_m',
		'castka_w' => 'castka_w',
*/
	);

	const WATCHED_NOT = 0;
	const WATCHED_ALL = 1;

	protected static function defaultJoins($query) {

		$query = parent::defaultJoins($query);
		$query
			->join(
				array(static::$USER_IM_DATA_TABLE_PREFIX => static::$USER_IM_DATA_TABLE),
				static::$USER_IM_DATA_TABLE_PREFIX.'.user_id = '.self::$TABLE_PREFIX.'.user_id',
				null)
			->join(
				array('pa' => Webservice_PointsTransaction::$TABLE_POINT_ACCOUNT),
				'pa.user_id = '.self::$TABLE_PREFIX.'.user_id AND point_type_id = '.Webservice_PointsType::DEFAULT_POINT_TYPE_ID,
				null)
			->joinLeft(
				array(Webservice_Currency::$TABLE_PREFIX => Webservice_Currency::$TABLE),
				Webservice_Currency::$TABLE_PREFIX.'.mena_id = '.self::$TABLE_PREFIX.'.mena_id',
				null)
			->joinLeft(
				array(Webservice_Country::$TABLE_PREFIX => Webservice_Country::$TABLE),
				Webservice_Country::$TABLE_PREFIX.'.zeme_id = '.self::$TABLE_PREFIX.'.zeme_id',
				null)
			->joinLeft(
				array(Webservice_Language::$TABLE_PREFIX => Webservice_Language::$TABLE),
				Webservice_Language::$TABLE_PREFIX.'.lang_id = '.self::$TABLE_PREFIX.'.lang_id',
				null)
			->joinLeft(
				array(self::$TABLE_USER_BANK_ACC_PREFIX => self::$TABLE_USER_BANK_ACC),
				self::$TABLE_USER_BANK_ACC_PREFIX.'.user_id = '.self::$TABLE_PREFIX.'.user_id
				AND '.self::$TABLE_USER_BANK_ACC_PREFIX.'.is_current = 1',
				null)
			->joinLeft(
				array(self::$TABLE_BANK_PREFIX => self::$TABLE_BANK),
				self::$TABLE_USER_BANK_ACC_PREFIX.'.bank_id = '.self::$TABLE_BANK_PREFIX.'.bank_id',
				null)
				// předvolba, bylo:
			/*->joinLeft(
					array(Webservice_AreaCode::$TABLE_PREFIX => Webservice_AreaCode::$TABLE),
					self::$TABLE_PREFIX.'.area_code = '.Webservice_AreaCode::$TABLE_PREFIX.'.id',
					null)*/
			// předvolba, nově (s tabulkou zeme):
			->joinLeft(
					array(self::$TABLE_AREACODE_PREFIX => Webservice_Country::$TABLE),
					self::$TABLE_PREFIX.'.area_code = './*Webservice_Country::$TABLE_PREFIX.*/self::$TABLE_AREACODE_PREFIX.'.zeme_id',
					null)
			->joinLeft(
				array(self::$TABLE_BRANCH_PREFIX => self::$ADMIN_DB.'.'.self::$TABLE_BRANCH),
				self::$TABLE_BRANCH_PREFIX.'.id = '.self::$MAIN_DB.'.'.self::$TABLE_PREFIX.'.branch_id',
				null);

		return $query;
	}

	/**
	 * 
	 * Generates agreement number.
	 */
	public static function generateAgreementNumber() {
		$prefix = '251';
		mt_srand();
		$db = static::getDb();
		do {
			$ret = mt_rand(0,999999);
			$ret = str_pad($ret, 6, "0", STR_PAD_LEFT);
			$ret2 = mt_rand(0,9999);
			$ret2 = str_pad($ret2, 4, "0", STR_PAD_LEFT);
			$ret = $prefix.$ret.$ret2;
			$exists = $db->select()
				->from(static::$TABLE,'agreement_number')
				->where('agreement_number = ?', $ret)
				->query()->fetch();
		} while ( $exists );
		
		return $ret;
	}
	
	/**
	 * Generates client cart nunmber
	 * @return string new client cart number.
	 */
	public static function generateClientCardNumber() {
		$prefix = '251';
		mt_srand();
		$db = static::getDb();
		do {
			$ret = mt_rand(0,999999);
			$ret = str_pad($ret, 6, "0", STR_PAD_LEFT);
			$ret2 = mt_rand(0,9999);
			$ret2 = str_pad($ret2, 4, "0", STR_PAD_LEFT);
			$ret = $prefix.$ret.$ret2;
			$exists = $db->select()
				->from(static::$TABLE_CLIENT_CARD_FEED,'id')
				->where('id = ?', $ret)
				->query()->fetch();
		} while ( $exists );
		
		return $ret;
	}
	
	/** 
	 * Generates given count of card numbers
	 * @param integer $count
	 * @param integer $set
	 * @return boolean true on success
	 */
	public static function generateClientCardNumbersToFeed($count,$set = null) {
		$db = static::getDb();
		$ids = array();
		It6_DbTransaction::begin($db);
		try {
			for ( $i = 0; $i < $count; ++$i) {
				$id = static::generateClientCardNumber();
				$ids[] = $id;
				$db->insert(
					static::$TABLE_CLIENT_CARD_FEED,
					array(
						'id' => $id,
						'generated' => It6_Date::dbNow(),
						'admin_id' => Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN),
						'set' => $set
					)
				);
				
			}
			
			It6_Log::info(
				'%count$ client card numbers generated to the feed.',
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'count' => $count,
					'ids' => $ids,
				)
			);
			
			It6_DbTransaction::commit($db);
			return true;
		}
		catch( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::err(
				'Client card generation failed: %message%',
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'count' => $count,
					'lastId' => $ids,
					'message' => $e->getMessage()
				)
			);
			It6_Log::err($e);
			throw new It6_XmlRpc_Exception('Client card generation failed.',$e);
		}
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

	/**
	 * Returns all banned users.
	 * @return struct users structure
	 * @see Entities_User
	 */
	public static function getBanned($extensions = null) {
		return static::getAllWhere(array('zakazany <> ?' => 0), $extensions);
	}

	/**
	 * Find user by given identifier.
	 * @param integer $userId identifier of the user
	 * @return struct user structure
	 * @see Entities_User
	 */
	public static function getById($userId, $extensions = null) {
		return parent::getById($userId, $extensions);
	}

	/**
	* Find user by given user handle.
	* @param string $handle User's handle
	* @return struct user structure
	* @see Entities_User
	*/
	public static function getByHandle($handle, $extensions = null) {
		return static::getOneBy($handle, 'usr.handle', $extensions);
	}

	/**
	* Find user by given client card identifier.
	* @param string $clientCardNumber number of the user's card
	* @return struct user structure
	* @see Entities_User
	*/
	public static function getByClientCardNumber($clientCardNumber, $extensions = null) {
		$fixed = str_pad($clientCardNumber, 15, '0', STR_PAD_LEFT);
		//TODO: after fixing all faultly shortened values in DB (12 chars instead of 15 chars) use following commented line instead of the lines after
		//return static::getOneBy($fixed, 'client_card_number', $extensions);
		$fixed2 = str_pad($clientCardNumber, 12, '0', STR_PAD_LEFT);
		$fixed1Sql = static::getDb()->quote($fixed);
		$fixed2Sql = static::getDb()->quote($fixed2);
		$user = static::getAllWhere(array("(client_card_number IN ($fixed1Sql,$fixed2Sql,LEFT($fixed1Sql,12)))"), $extensions);		
		if (count($user) > 1)
			return null;
		if (!empty($user)) {
			$user = $user[0];
			//$user['clientCardNumber'] = $clientCardNumber;
			return $user;
		}
		else
			return null;
	}

	/**
	 * Find user by given logginname.
	 * @param string $loginName nick of the user
	 * @return struct user structure
	 * @see Entities_User
	 */
	public static function getByLoginName($loginName, $extensions = null) {
		return static::getOneBy($loginName, 'nick', $extensions);
	}

	/**
	 * Get user by login name or card number
	 * status: 0: wrong input, 1: ok, 2: forbidden user, 3: user not activated
	 * @param string|int $loginNameOrCardNumber
	 * @return array status (int), type(cardNumber|loginName|NULL), user (array), message (string)
	 */
	public static function getByLoginNameOrCardNumber($loginNameOrCardNumber) {
		$db = static::getDb();
		$status = 0;
		$type = null;
		$user = null;
		$message = It6_Models_Translator::translate('branch_login_wrong_input', 1, $db);
		if (!empty($loginNameOrCardNumber) && (is_int($loginNameOrCardNumber)
				|| is_string($loginNameOrCardNumber))) {
			if (preg_match('/^[0-9]{10,}$/', $loginNameOrCardNumber) === 1) {
				$type = 'cardNumber';
				$message = It6_Models_Translator::translate('branch_login_wrong_card_number', 1, $db);
				$user = self::getByClientCardNumber($loginNameOrCardNumber);
			} else {
				$type = 'loginName';
				$message = It6_Models_Translator::translate('branch_login_wrong_login_name', 1, $db);
				$user = self::getByLoginName($loginNameOrCardNumber);
			}
		}
		if ($user) {
			$user = It6_ArrayWrapper::toNativeArray($user);
			if ($user['isForbiden'] == 1) {
				$status = 2;
				$message = It6_Models_Translator::translate('branch_login_forbidden_user', 1, $db);
			} elseif (is_null($user['activationTime'])) {
				$status = 3;
				$message = It6_Models_Translator::translate('branch_login_user_not_activated', 1, $db);
			} else {
				$status = 1;
				$message = '';
			}
		}
		return array(
			'status' => $status,
			'type' => $type,
			'user' => $user,
			'message' => $message
		);
	}

	/**
	* Find users by given branch identifier.
	* @param array $branchId identifier of the user
	* @return struct user structure
	* @see Entities_User
	*/
	public static function getByBranchId($branchId, $extensions = null) {
		return static::getAllWhere(array('branchId = ?' => $branchId), $extensions);
	}

	/**
	 * Get anonymous user for branch
	 * (This method checks anonymous user count that it is exactly one.)
	 * @param integer $branchId
	 * @returns struct User entity
	 * @see Entities_User
	 */
	public static function getAnonymousByBranchId($branchId) {
		$users = static::getAllWhere(array('branchId = ?' => $branchId, 'anonymous = ?' => 1));
		$n = count($users);
		if (0 == $n)
			throw new It6_XmlRpc_Exception('No anonymous user found: branchId=' . $branchId);
		else if (1 < $n)
			throw new It6_XmlRpc_Exception('Found more anonymous users: branchId=' . $branchId);
		else
			return $users[0];
	}
        
	/**
	* Changes user branch.
	* @param array $userIds array of user ids.
	* @param integer $branchId identifier of the branch
	* @return bool user structure
	* @see Entities_User
	*/
	public static function setUserBranchId($userIds, $branchId) {
		$db = static::getDb();

		try {
			$data = array('branch_id' => $branchId);
			$where = array();
			$where['user_id IN (?)'] = $userIds;
			$res = $db->update(self::$TABLE, $data, $where);
			return $res;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("setUserBranchId.", 0, $e);
		}
	}

	/**
	 * Insert new user. Value of the user identifier is ignored and new
	 * is generated.
	 * @param struct $user structure of the user
	 * @return integer|bool user identifier of the created user or false on error
	 * @see Entities_User
	 */
	public static function insert($user) {

		$user['email'] = trim($user['email']);
		$user['username'] = self::getDefaultUsername();
		$user['password'] = It6_Models_User::cryptPassword($user['password']);
		
		if (empty($user['anonymous'])) {
			if (isset($user['citizenId'])) {
				$user['citizenId'] = trim($user['citizenId']);
			}
			$user['street'] = trim($user['street']);
			$user['phone'] = trim($user['phone']);
			$user['town'] = trim($user['town']);
			$user['zip'] = trim($user['zip']);
		}
		
		$curMonth = date('n');
		$curDay = date('j');
		$curYear = date('Y');
		$curDateTmpStmp	= mktime(0, 0, 0, $curMonth, $curDay, $curYear);
		
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$user = new It6_ArrayWrapper($user);

			$data = static::fromEntity($user);
			$now = It6_Date::dbNow();

			if ( !empty($data['client_card_number']) ) {
				static::_setClientCardNumber($data['client_card_number']);
			}

			unset($data['user_id']);
			unset($data['datum_aktivace']);
			$data['datum_registrace'] = $now; 
			$data['agreement_number'] = static::generateAgreementNumber();
			unset($data['account_number']);
			unset($data['account_prefix']);
			unset($data['bank_id']);
			$data['can_print_agreement'] = 1;
			/*if (empty($data['branch_id'])) {
				$data['branch_id'] = Zend_Registry::get('acl')->getIdentity(It6_Acl_Admin::IDNAME_BRANCH);
			}*/

			if ( !empty($data['clientCardNumber']) ) {
				static::_setClientCardNumber($data['clientCardNumber']);
			}

			// assign default params
			$def = self::getUserDefaultParams();
			foreach ($def as $k => $v) {
				if(!isset($data[$k]))
					$data[$k] = $v;
			}
			$db->insert(self::$TABLE, $data);
			$uid = $db->lastInsertId();

			$handle = It6_NineDigitHandle2::makeHandle($uid, $db);
			$db->update(self::$TABLE, array('handle' => $handle), array('user_id=?' => $uid));

			if (!empty($bankAccount)) {
				$bankAccount['userId'] = $uid;
				Webservice_UserBankAccount::insert($bankAccount);
			}

			$deposit = array(
				'user_id' => $uid,
				'zustatek' => 0,
				'zetony' =>  0,
				'dluh' => 0,
				'zustatek_bonus' => 0
			);
			$db->insert(static::$USER_IM_DATA_TABLE, $deposit);

			Webservice_PointsTransaction::createPointAccounts($uid);

			Webservice_Campaign::putOn('UserActivation', Webservice_Campaign::NAMESPACE_GET, array('userId' => $uid));

			It6_Log::info(
				'New user created.',
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'userId' => $uid,
					//'adminId' => Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN)
				)
			);

			It6_DbTransaction::commit($db);

			// dříve aktivační mail cronem, nyní rovnou v kódu po registraci
			/*if (empty($user['anonymous'])) {
				//send email to user
				$params = array(
					It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'NewUserRegistration',
					'user_id'		=> $uid
				);
				$cronJob = array(
					'type' 		=> 1,
					'date'		=> It6_Date::dbNow(),
					'params'	=> $params
				);
				Zend_Registry::get('ws')->CronJob->insert($cronJob);
			}*/

			return $uid;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not insert user.", 0, $e);
		}
	}

	/**
	 * 
	 * Information that agreement has been printed
	 * @param integer $userId
	 * @return true on success
	 */
	public static function agreementPrinted($userId) {
		$db = static::getDb();
		try {
			It6_DbTransaction::begin($db);
			
			$db->update(
				self::$TABLE,
				array( 'can_print_agreement' => 0 ),
				array('user_id = ?' => $userId)
			);
			
			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update user data.", 0, $e);
			return false;
		}
		
	}

	/**
	 * Update user.
	 * @param struct $user structure of the user
	 * @return true on success
	 * @see Entities_User
	 */
	public static function update($user) {
		$db = static::getDb();

		try {
			It6_DbTransaction::begin($db);

			if(!empty($user[self::$CONV['uba.account_number']])) {
				$rows = $db->select()
					->from(self::$TABLE_USER_BANK_ACC, array(
						'id' => 'account_id', 'prefix' => 'account_prefix',
						'number' => 'account_number', 'bank' => 'bank_id',
						'current' => 'is_current'
					))
					->where('user_id = ?', $user['userId'])
					->query()
					->fetchAll();
				$existingIds = array();
				$reuseId = false; // false..no, true..already used, integer..reuse this id
				if (!empty($rows)) {
					foreach ($rows as $row) {
						if ($row['prefix'] == $user['accountPrefix']
							&& $row['number'] == $user['accountNumber']
							&& $row['bank'] == $user['bankId']) {

							if ($row['current'])
								$reuseId = true;
							else if (true !== $reuseId)
								$reuseId = $row['id'];
						}
						$existingIds[] = $row['id'];
					}
				}
				if (true !== $reuseId && !empty($existingIds)) {
					$db->update(
						self::$TABLE_USER_BANK_ACC,
						array( 'is_current' => new Zend_Db_Expr('account_id='.intval($reuseId)) ),
						array('account_id IN (?)' => $existingIds)
					);
				}
				if (false === $reuseId) {
					$userBank = array(
						//'account_id'		=> '',
						'user_id'			=> $user['userId'],
						'account_prefix'	=> $user['accountPrefix'],
						'account_number'	=> $user['accountNumber'],
						'bank_id'			=> $user['bankId'],
						'is_current'		=> '1'
					);
					if($userBank['account_prefix'] == '')
						$userBank['account_prefix'] = null;
					$db->insert(self::$TABLE_USER_BANK_ACC, $userBank);
				}
			}

			unset(
				$user['accountPrefix'],
				$user['accountNumber'],
				$user['bankId']
			);
			
			
			$oldUser = static::getById($user['userId']);
			if ( empty($oldUser['agreementNumber']) ) {
				$user['agreementNumber'] = static::generateAgreementNumber();
			}
			else if ( !empty($user['agreementNumber']) && 
				ltrim($oldUser['agreementNumber'],0) != ltrim($user['agreementNumber'],0) ) {
				throw new Exception('It is not allowed to change agreement number.');
			}
			else {
				unset($user['agreementNumber']);
			}
			
			if ( !empty($user['clientCardNumber'])
				&&  ltrim($oldUser['clientCardNumber'], '0') != ltrim($user['clientCardNumber'], '0')
			) {
				// branch ID of client card is admin's responsibility here
				static::_setClientCardNumber($user['clientCardNumber']);
			}
			else {
				unset($user['clientCardNumber']);
			}
			
			// remove blocked ips
			if (!empty($user['removeBlockedIPs'])) {
				self::removeBlockedIps($user['userId'], $user['removeBlockedIPs']);
			}
			
			parent::update($user);

			It6_DbTransaction::commit($db);
			return true;
		}

		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update user data.", 0, $e);
			return false;
		}
	}

	/**
	 * Note that if user had another client card number before, old card would remain
	 * marked as used.
	 * @param string $number New client card number
	 * @throws Exception
	 */
	protected static function _setClientCardNumber($number) {
		$now = It6_Date::dbNow();
		$db = static::getDb();
		$feed = $db->select()
			->from(static::$TABLE_CLIENT_CARD_FEED)
			->where('id = ?',$number)
			->query()->fetch();
		if ( empty($feed) )
			throw new Exception('Unknown client card number.');
		if ( !empty($feed['used']) )
			throw new Exception('Client card number already used.');

		$branchId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_BRANCH);

		if ( $branchId != It6_Models_Branch::ID_INTERNET
				&& $feed['branch_id'] != $branchId ) {
			throw new Exception('Wrong branch id for this client card number.');
		}
			
		$db->update(
			static::$TABLE_CLIENT_CARD_FEED,
			array(
				'used' => $now,
			),
			array(
				'id = ?' => $number
			)
		);
	}
	
	/**
	 * Delete user.
	 * @param integer $userId idenetifier of the user.
	 * @return true on success
	 * @see Entities_User
	 */
	public static function delete($userId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns defult user params.
	 * @return struct of user params.
	 * @see Entities_User
	 */
	protected static function getUserDefaultParams() {
		$data = array(
			'mena_id' => 2, //EUR
			'lang_id' => 2, //en
			'posledni_prihlaseni' => '0000-00-00 00:00:00',
			'vyprseni_session' => '0000-00-00 00:00:00',
			'datum_registrace' => '0000-00-00 00:00:00',
			'newsletter' => 0,
			'vyber_status' => 1,
			'individualni_max_vklad' => 0,
			'zakazany' => 0,
			'misto' => '',
			'ucet_status' => 1,
			'block' => 0,
			'block_ip' => 0,
			'vyhernost_game' => 0,
			'bet_stats_win' => 0,
			'bet_stats_lose_acc' => 0,
			'bet_stats_lose_book' => 0,
			'bet_total' => 0,
			'win_ticket' => 0,
			'lose_ticket' => 0,
			'delete_ticket' => 0,
			'num_bet_ticket' => 0,
			'bet_total2' => 0,
			'ticket_num' => 0,
			'finance_rating' => 6,
			'max_bet' => 99999999,
			'self_excluded_until' => '0000-00-00 00:00:00',
			'e_testovaci' => 'ne',
			'block_play' => 0,
			'castka_m' => 0,
			'castka_w' => 0
		);
		return $data;
	}


	/**
	 * Allow internet using for user. (agreement)
	 * @param integer $userId
	 * @param integer $branchId
	 * @param integer $adminId
	 * @param string $agreementNumber
	 * @return boolean true on success
	 */
	public static function allowInternet($userId, $branchId, $adminId, $agreementNumber) {
		//TODO kontrola povinnych udaju
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$data = $db->select()->from(
					static::$TABLE, 
					array(
						'datum_aktivace',
						'agreement_number',
						'client_card_number'
					)
				)
				->where(static::$IDENTITY . '= ?', $userId)
				->query()->fetch();

			if ( empty($data) )
				throw new Exception('Unknown user');
			if ( !empty($data['datum_aktivace']) )
				throw new Exception('Internet already allowed.');
			if (empty($data['client_card_number']) )
				throw new Exception('User has no client card number.');
			if ( ltrim($data['agreement_number'],'0') != ltrim($agreementNumber,'0') )
				throw new Exception('Wrong agreement number');
			

			$data['datum_aktivace'] = It6_Date::dbNow();
			$data['branch_id'] = $branchId;
			$data['allowed_by_admin_id'] = $adminId;
			$data['can_print_agreement'] = 0;
			unset($data['client_card_number']);
			unset($data['agreement_number']);			
			
			$db->update(
					static::$TABLE,
					$data,
					array( static::$IDENTITY . '= ?' => $userId)
				);
			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not allow internet", 0, $e);
		}
	}
	
	/**
	 * Block user's client card
	 * @param unknown_type $userId
	 */
	public static function blockClientCard($userId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$user = $db->select()
				->from(static::$TABLE,'client_card_number')
				->where(static::$IDENTITY . '= ?', $userId)
				->query()->fetch();
			
			if ( empty($user['client_card_number']) )
				throw new Exception('User has no current client card.');
			
			$db->update(
				static::$TABLE,
				array(
					'client_card_number' => null
				),
				array( static::$IDENTITY . '= ?' => $userId)
			);
			$db->update(
				static::$TABLE_CLIENT_CARD_FEED,
				array(
					'blocked' => It6_Date::dbNow()
				),
				array( 'id = ?' => $user['client_card_number'])
			);
			It6_DbTransaction::commit($db);
		}
		catch(Excpetion $e) {
			It6_DbTransaction::rollback($db);
			throw It6_XmlRpc_Exception('Can not block client card',$e);	
		}
	}

	/**
	 * Change credit card number.
	 * @param integer $userId
	 * @param string $newClientCardNumber
	 * @deprecated
	 * @return boolean true on success
	 */
	public static function changeClientCard($userId, $newClientCardNumber) {
		throw new It6_XmlRpc_Exception ( 'This method is deprecated.' );
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$db->update(
				static::$TABLE,
				array( 'client_card_number' => $newClientCardNumber),
				array( static::$IDENTITY . '= ?' => $userId)
			);

			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not change client card. (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	/**
	 * Returns all tickets of the user.
	 * @param int $userId Identifier of the given user
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getAllTickets($userId, $extensions = null) {
		return Webservice_Ticket::getAllWhere(array(
			'userId = ?' => $userId), $extensions);
	}

	/**
	 * Returns all opened (not canceled and not payed off) tickets of the user.
	 * @param int $userId Identifier of the user
	 * @param date $dateFrom null is -infinity
	 * @param date $dateTo null is infinity
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getOpenedTickets($userId, $dateFrom = null, $dateTo = null, $extensions = null) {
		//TODO este vyfiltrovat prohrane tikety
		$filter = array(
			'userId = ?' => $userId,
			'canceled = ?' => 0,
			'paidOut = ?' => 0);
		if ( !empty($dateFrom) ) $filter['createdTime >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['createdTime <= ?'] = $dateTo;
		return Webservice_Ticket::getAllWhere($filter, $extensions);
	}

	/**
	 * Returns all canceled tickets of the given user.
	 * @param int $userId Identifier of the user
	 * @param date $dateFrom null is -infinity
	 * @param date $dateTo null is infinity
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getCanceledTickets($userId, $dateFrom, $dateTo, $extensions = null) {
		$filter = array(
			'userId = ?' => $userId,
			'canceled <> ?' => 0);
		if ( !empty($dateFrom) ) $filter['createdTime >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['createdTime <= ?'] = $dateTo;
		return Webservice_Ticket::getAllWhere($filter, $extensions);
	}

	/**
	 * Returns all payed of tickets of the given user.
	 * @param int $userId Identifier of the user
	 * @param date $dateFrom null is -infinity
	 * @param date $dateTo null is infinity
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getPaidOutTickets($userId, $dateFrom = null, $dateTo = null, $extensions = null) {
		$filter = array(
			'userId = ?' => $userId,
			'paidOut <> ?' => 0);
		if ( !empty($dateFrom) ) $filter['paidOutTime >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['paidOutTime <= ?'] = $dateTo;
		return Webservice_Ticket::getAllWhere($filter, $extensions);
	}

	/**
	 * Returns all transactions related to the given user.
	 * @param integer $userId identifier of the user
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAllTransactions($userId, $extensions = null) {
		$filter = array(
			'userId = ?' => $userId,
			'accountType = ?' => Webservice_TransactionType::ACCOUNT_TYPE_USER);
		return Webservice_Transaction::getAllWhereOrder($filter, array('time DESC','transactionId DESC'), $extensions);
	}

//commented out by Martin. should not be needed. Now its handled by ws extensions and Webservice_PointTransaction
	/**
	 * Returns filtered transactions related to the given user.
	 * @param int $userId identifier of the user
	 * @param string $dateFrom null is -infinity
	 * @param string $dateTo null is infinity
	 * @param string $type name of the transaction type, null is all types
	 * @return Array array of the transaction struct.
	 */
/*
	public static function getTransactions($userId, $dateFrom, $dateTo, $type = null, $extensions = null) {
		$filter = array(
			'userId = ?' => $userId,
			'accountType = ?' => Webservice_TransactionType::ACCOUNT_TYPE_USER);
		if ( !empty($dateFrom) ) $filter['time >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['time <= ?'] = $dateTo;
		if ( !empty($type) ) {
			$typeId = Webservice_TransactionType::getByName($type);
			if ( empty($typeId) )
				throw new It6_XmlRpc_Exception("Unknown transaction type name: '$type'", 0, $e);

			$filter['typeId = ?'] = $typeId['transactionTypeId'];
		}

		return Webservice_Transaction::getAllWhereOrder($filter,array('time DESC','transactionId DESC'), $extensions);
	}
*/

	/**
	 * Returns sum of filtered transactions related to the given user.
	 * @param integer $userId identifier of the host
	 * @param string $dateFrom null is -infinity
	 * @param string $dateTo null is infinity
	 * @param string $type name of the transaction type, null is all types
	 * @return float sum of transaction values.
	 */
	public static function getTransactionsSum($userId, $dateFrom = null, $dateTo = null, $type = null, $okTime = true, $ignoreCancelStatus = true) {

		try {
			$db = static::getMainDb();

			$select = $db->select()
				->from(
						array('ft' => Webservice_Transaction::$TABLE),
						array('sum' => 'Sum(value)'))
				->join(
					array('tt' => Webservice_TransactionType::$TABLE),
					'tt.id = ft.type_id',
					null)				
				->where('IF(ISNULL(ft.account_type),tt.account_type,ft.account_type) = ?'
					, Webservice_TransactionType::ACCOUNT_TYPE_USER);
			
			if (!empty($userId))
				$select->where('user_id = ?', $userId);

			if ( !empty($type) ) {
				if ( !is_array($type) )
					$type = array($type);

				$typeIds = array();
				foreach ( $type as $tp ) {
					$typeId = Webservice_TransactionType::getByName($tp);
					if ( empty($typeId) )
						throw new Exception("Unknown transaction type name: '$tp'");
					$typeIds[] = $typeId['transactionTypeId'];
				}

				if ( !empty($typeIds) ) {
					$select->where('ft.type_id IN (?)', $typeIds);
				}
			}

			if ($okTime) {
				if ( !empty($dateFrom) )
					$select->where('okTime >= ?',$dateFrom);

				if ( !empty($dateTo) )
					$select->where('okTime < ?',$dateTo);

				$select->where('status = ?', Webservice_Transaction::STATUS_OK);


				}
			else {
				if ( !empty($dateFrom) )
					$select->where('time >= ?',$dateFrom);

				if ( !empty($dateTo) )
					$select->where('time < ?',$dateTo);

				if ($ignoreCancelStatus)
					$select->where("status NOT IN ('canceled', 'no-deposit')");

			}
			$ret = $select->query()->fetch();

			return empty($ret['sum']) ? 0.0 : $ret['sum'];
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("getTransactionSum: '$type'", 0, $e);
		}
	}

	/**
	 * Returns all transactions related to the given user.
	 * @param integer $userId identifier of the user
	 * @param integer $pointType identifuer of the point type
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAllPointsTransactions($userId, $extensions = null) {
		$filter = array('userId = ?' => $userId);
		return Webservice_PointsTransaction::getAllWhereOrder($filter,array('time DESC'), $extensions);
	}

//commented out by Martin. should not be needed. Now its handled by ws extensions and Webservice_PointTransaction
	/**
	 *  Returns filtered transactions related to the given user.
	 * @param int $userId identifier of the user
	 * @param date $dateFrom null is -infinity
	 * @param date $dateTo null is infinity
	 * @param int $type identifier of the transaction type, null is all types
	 * @return Array array of the points transaction struct.
	 */
/*
	public static function getPointsTransactions($userId, $dateFrom, $dateTo, $type = null, $extensions = null) {
		$filter = array('userId = ?' => $userId);
		if ( !empty($dateFrom) ) $filter['time >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['time <= ?'] = $dateTo;
		if ( !empty($type) ) $filter['typeId = ?'] = $type;

		return Webservice_PointsTransaction::getAllWhereOrder($filter, array('time DESC'), $extensions);
	}
*/

	/**
	 * Returns user's win ratio: (realWin - amount)/amount.
	 * @param int $userId identifier of the user
	 * @param date|NULL $dateFrom DB datetime of pay out for start of interval, NULL for unspecified
	 * @param date|NULL $dateTo DB datetime of pay out for end of interval, NULL for unspecified
	 * @return float|boolean|NULL FALSE on invalid arguments or error, NULL on unknown result (eg. no user records), win ratio otherwise
	 */
	public static function getWinRatio($userId, $dateFrom = null, $dateTo = null) {
		$data = static::getWinRatioData($userId, $dateFrom, $dateTo);
		return (is_array($data) ? $data['winRatio'] : $data);
	}

	/**
	 * Returns user's win ratio: (realWin - amount)/amount
	 * and total stake.
	 * @param int $userId identifier of the user
	 * @param date|NULL $dateFrom DB datetime of pay out for start of interval, NULL for unspecified
	 * @param date|NULL $dateTo DB datetime of pay out for end of interval, NULL for unspecified
	 * @return struct|boolean FALSE on invalid arguments or error
	 *                        otherwise structure with fields: winRatio, stake (winRatio can be NULL, if it is unknown)
	 */
	public static function getWinRatioData($userId, $dateFrom = null, $dateTo = null) {
		if (empty($userId) || !is_numeric($userId))
			return false;
		$db = static::getDb();
		$where = array(
			'user_id=' . intval($userId),
			'vyplacen=1',
		);
		if (isset($dateFrom))
			$where[] = 'vyplacen_date >=' . $db->quote($dateFrom);
		if (isset($dateTo))
			$where[] = 'vyplacen_date <= ' . $db->quote($dateTo);
		$where = ' WHERE ' . implode(' AND ', $where);
	
		$sql = 'SELECT SUM(win_real - castka) AS realWinNetto, SUM(castka) AS stake'
			. ' FROM ' . Webservice_Ticket::$TABLE
			. $where
			. ' GROUP BY user_id';
		$row = $db->query($sql)->fetchAll();
		if (empty($row))
			return null;
		$row = array_shift($row);
		$stake = $row['stake'];
		if (0 == $stake) {
			return array(
				'winRatio' => null,
				'stake' => 0.0,
			);
		}
		$realWinNetto = $row['realWinNetto'];
		return array(
			'winRatio' => $realWinNetto / $stake,
			'stake' => $stake,
		);
	}

	/**
	 *  Returns true if user is allowed to withdraw
	 * @param int $userId identifier of the user
	 * @return bool
	 */
	public static function canWithdraw($userId) {
		$user = parent::getById($userId, NULL);
		if (!empty($user))
			return $user['canWithdraw'];
		else return false;
	}

	public static function toEntity($user, $columns = null) {
		$db = static::getDb();

		try {
			$ret = parent::toEntity($user, $columns);
			if (!empty($ret['anonymous']))
				$ret['username'] = 'anonymous';

			if ( empty($columns) || in_array('points', $columns) ) {
				$points = array();
				$points = $db->select()
					->from(Webservice_PointsTransaction::$TABLE_POINT_ACCOUNT, null)
					->columns(array(
						'pointTypeId' => 'point_type_id',
						'balance' => 'balance',
						'balanceGet' => 'balance_get',
						'balanceSpend' => 'balance_spend',
						'balanceExchange' => 'balance_exchange' ))
					->where('user_id = ?', $ret->userId)
					->query()->fetchAll();

				foreach ( $points as &$point ) {
				        $point['balance'] = floatval($point['balance']);
					$point['balanceGet'] = floatval($point['balanceGet']);
					$point['balanceSpend'] = floatval($point['balanceSpend']);
					$point['balanceExchange'] = floatval($point['balanceExchange']);
					$point['balanceLimit'] = Webservice_PointsTransaction::getValueLimits($ret->userId, $point['pointTypeId']);
					$point['balanceDayLimit'] = floatval(Webservice_Parameter::getUserParameter(Webservice_PointsTransaction::PARAM_BALANCE_LIMIT_DAY_INCOME,$ret->userId));
					$point['balanceTotalLimit'] = floatval(Webservice_Parameter::getUserParameter(Webservice_PointsTransaction::PARAM_BALANCE_LIMIT_TOTAL,$ret->userId));
					$point['preferenceRateLimits'] = Webservice_Campaign::get('PreferenceRate','spend','Limits',array('userId' => $ret->userId));
					 
				}
				
				$ret->points = $points;
			}

		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('User::toEntity',0,$e);
		}

		return $ret;
	}



	/**
	 * Returns users according to the fulltext search.
	 * @param mixed $searchTerm term to be searched by fulltext
	 * @return array of the users struct.
	 */
	public static function getAllFulltext($searchTerm) {


		$columns = array();
		foreach(self::$CONV as $dbName => $varName) {
			if(!strpos($dbName, '.'))
				$dbName = self::$TABLE_PREFIX.'.'.$dbName;

			$columns[$varName] = $dbName;
		}


		$users = self::getDb()->select()
			->from(
				array('us' => self::$TABLE_SEARCH),
				$columns)
			->join(
				array(self::$TABLE_PREFIX => self::$TABLE),
				self::$TABLE_PREFIX.'.user_id = us.user_id',
				null)
			->join(
				array(static::$USER_IM_DATA_TABLE_PREFIX => static::$USER_IM_DATA_TABLE),
				static::$USER_IM_DATA_TABLE_PREFIX.'.user_id = '.self::$TABLE_PREFIX.'.user_id',
				null)
			->joinLeft(
				array(Webservice_Currency::$TABLE_PREFIX => Webservice_Currency::$TABLE),
				Webservice_Currency::$TABLE_PREFIX.'.mena_id = '.self::$TABLE_PREFIX.'.mena_id',
				null)
			->joinLeft(
				array(Webservice_Country::$TABLE_PREFIX => Webservice_Country::$TABLE),
				Webservice_Country::$TABLE_PREFIX.'.zeme_id = '.self::$TABLE_PREFIX.'.zeme_id',
				null)
			->joinLeft(
				array(Webservice_Language::$TABLE_PREFIX => Webservice_Language::$TABLE),
				Webservice_Language::$TABLE_PREFIX.'.lang_id = '.self::$TABLE_PREFIX.'.lang_id',
				null)
			->where("MATCH(us.jmeno, us.prijmeni, us.nick, us.email, us.ulice, us.telefon, us.mobil, us.info, us.misto) AGAINST(?)", $searchTerm)
			->query()->fetchAll();

		return $users;
	}


	/**
	 * Allows users based on their Id
	 * @param array $userIDs array of userIds
	 * @return true/false
	 */
	public static function allowUsers(array $userIDs, $sendEmail = 0, $sendSMS = 0) {
		$db = self::getDb();
		It6_DbTransaction::begin();
		try {

			$db->update(self::$TABLE, array('zakazany' => 0), array('user_id IN (?)' => $userIDs));

			It6_DbTransaction::commit();

			$usrIds = implode(',', array_keys($userIDs));
			$userData = array();
			$userData = It6_ArrayWrapper::toNativeArray(static::getAllWhereColumns(
				array("userId IN ($usrIds)"),
				array('userId', 'email', 'phone', 'username')
			));

			if ( count($userData) > 0 ) {
				foreach ($userData as $user) {
					if ( $sendEmail != 0 && isset($user['email']) ) {
						It6_Log::info("send email: ".$user['email'],It6_Log::TAG_ADMIN_OPERATION);
						static::userRoleStatusEmailNotifications(trim($user['email']), trim($user['username']), "unblocking");
					}

					if ( $sendSMS != 0 && isset($user['phone']) ) {
						It6_Log::info("send SMS: ".$user['phone'],It6_Log::TAG_ADMIN_OPERATION);
					    static::userRoleStatusSmsNotifications($user['phone'], trim($user['username']), "unblocking");
					}
				}
			}

			It6_Log::info(
				"Users '%users_id%' were allowed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('users_id'=>  implode(',',$userIDs),'zak' => 0)
			);
			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback();
			throw new It6_XmlRpc_Exception('User::toEntity',0,$e);
			It6_Log::errr(
				"Error allowing users '%users_id%'.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('users_id'=>  implode(',',$userIDs), 'zak' => 1)
			);
			return false;
		}
	}

	/**
	 * Bans users based on their Id
	 * @param array $userIDs array of userIds
	 * @param int $sendEmail
	 * @param int $sendSMS
	 * @return true/false
	 */
	public static function banUsers(array $userIDs, $sendEmail = 0, $sendSMS = 0) {
		$db = self::getDb();
		It6_DbTransaction::begin();
		try {

			$db->update(self::$TABLE, array('zakazany' => 1), array('user_id IN (?)' => $userIDs));

			It6_DbTransaction::commit();

			$usrIds = implode(',', array_keys($userIDs));
			$userData = array();
			$userData = It6_ArrayWrapper::toNativeArray(static::getAllWhereColumns(
				array("userId IN ($usrIds)"),
				array('userId', 'email', 'phone', 'username')
			));

			if ( count($userData) > 0 ) {
				foreach ($userData as $user) {
					if ( $sendEmail != 0 && isset($user['email']) ) {
						It6_Log::info("send email: ".$user['email'],It6_Log::TAG_ADMIN_OPERATION);
						static::userRoleStatusEmailNotifications(trim($user['email']), $user['username'],  "blocking");
					}

					if ( $sendSMS != 0 && isset($user['phone']) ) {
						It6_Log::info("send SMS: ".$user['phone'],It6_Log::TAG_ADMIN_OPERATION);
						static::userRoleStatusSmsNotifications($user['phone'], $user['username'], "blocking");
					}
				}
			}

			It6_Log::info(
				"Users '%users_id%' were banned.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('users_id'=>  implode(',',$userIDs),'zak' => 1)
			);

			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback();
			It6_Log::errr(
				"Error banning users '%users_id%'.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('users_id'=>  implode(',',$userIDs), 'zak' => 1)
			);
			throw new It6_XmlRpc_Exception('User::toEntity',0,$e);
			return false;
		}
	}

	/**
	 * Send Email
	 * @param string $email user email
	 * @param string $nick user nick name
	 * @param string $banAction user banning action
	 */
	protected static function userRoleStatusEmailNotifications($email, $nick, $banAction) {
		$langId = Zend_Registry::get('translate')->getCurrentLangId();
		$aEmailSubject = It6_ArrayWrapper::toNativeArray(array(It6_Models_Translator::get('email_'.$banAction.'_users_subject', $langId, $db)));
		$aEmailMsg = It6_ArrayWrapper::toNativeArray(array(It6_Models_Translator::get('email_'.$banAction.'_users_text', $langId, $db)));

	    $mail = new Zend_Mail('UTF-8');
	    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
	    $mail->addTo(trim($email));
		$mail->setSubject($aEmailSubject[0]);
		$mail->setBodyText(str_replace("%value%", $nick, $aEmailMsg[0]));
		
		try {
			if ($mail->send()) It6_Log::info('Email was sent to adress: '.$email);
		}
		catch (Exception $e) {
			It6_Log::err('Email not be sent. Exception:'.$e->getMessage());
		}
	}

	/**
	 * Send SMS
	 * @param string $phone user phone
	 * @param string $nick user nick name
	 * @param string $banAction user banning action
	 */
	protected static function userRoleStatusSmsNotifications($phone, $nick, $banAction) {
		$langId = Zend_Registry::get('translate')->getCurrentLangId();
		$aSmsMsg = It6_ArrayWrapper::toNativeArray(array(It6_Models_Translator::get('sms_'.$banAction.'_users_text', $langId, $db)));

		$text = sprintf(str_replace("%value%", $nick, $aSmsMsg[0]));
        $plainSms = new It6_Sms_PlainSms();
		try {
			if ( $plainSms->setToNumber($phone)->setText($text,It6_Sms_PlainSms::SMS_TYPE_BAN_UNBAN_USER)->sendSms() ) {
				It6_Log::info('SMS sent successfully to number: ' . $phone, It6_Log::TAG_MOBILEM_API, $res);
			}
		}
		catch (Exception $e) {
			It6_Log::err('SMS not sent. Exception:'.$e->getMessage());
		}
	}

	/**
	 * Sends authorization SMS
	 * @param integer $userId
	 * @return string
	 */
	public static function sendAuthorizationSms($userId) {
		global $SEND_AUTORIZATION_SMS_TEXT;

		mt_srand();
		$code = mt_rand(10000, 99999);

		$user = static::getById($userId);
		$phone = $user->phone . '';
		if (strlen($phone) == 9)
			$phone = MOBILEM_API_DEFAULT_PREFIX . $phone;

		$text = $SEND_AUTORIZATION_SMS_TEXT[$user->languageId];
		$text = str_replace('%code%', $code, $text);

		$plainSms = new It6_Sms_PlainSms();
		$smsId = $plainSms->setToNumber($phone)
				->setText($text, It6_Sms_PlainSms::SMS_TYPE_AUTHORIZATION)
				->sendSms();
		if ($smsId) {
			It6_Log::info('Authorization was sms sent successfully.', It6_Log::TAG_MOBILEM_API, $res);
		} else {
			It6_Log::err('Authorization was not sent.', It6_Log::TAG_MOBILEM_API, $res);
		}

		return $code;
	}

	/**
	 * Resets user's day individual risk limits.
	 */
	public static function cleanDayIndividualRiskLimits() {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE_USER_LIMIT,
				array('vycerpal' => 0),
				'1');
			It6_DbTransaction::commit($db);
		}
	 	catch ( Exception $e ) {
	 		It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not clean individual day risk limits:", 0, $e);
		}
		return true;
	}

	/**
	 * Resets user's duplicate tickets.
	 */
	public static function cleanDayDuplicateTickets() {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->query("DELETE FROM tickethash_ticket WHERE 1");
			$db->query("DELETE FROM tickethash WHERE 1");
			It6_DbTransaction::commit($db);
		}
	 	catch ( Exception $e ) {
	 		It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not clean duplicate tickets:", 0, $e);
		}
		return true;
	}

	/**
	 * User withdraw cash in branch. Makes all ncessery transactions.
	 * @param integer $transactionId
	 * @return amount to withdraw in the central currency
	 */
	public static function withdrawCash($transactionId) {
		$db = Webservice_Transaction::getDb();
		It6_DbTransaction::begin($db);
		try {
			$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);

			$transaction = Webservice_Transaction::getOneWhere(array(
				'transactionId = ?' => $transactionId,
				'hostId = ?' => $hostId,
				'status = ?' => Webservice_Transaction::STATUS_PRE_DEPOSIT,
				'typeName = ?' => Webservice_TransactionType::NAME_USER_WITHDRAW_CASH));

			if ( empty($transaction) )
				throw new Exception('No withdraw confirmed for this user.');

			$id = $transaction['transactionId'];
			Webservice_Transaction::deposit($id, true);

			Webservice_Transaction::make(array(
				'userId' => $transaction['userId'],
				'hostId' => $hostId,
				'value' => $transaction['value'],
				'currencyId' => $transaction['currencyId'],
				'typeName' => Webservice_TransactionType::NAME_BRANCH_USER_WITHDRAW_CASH
				));

			$ret = Webservice_Currency::exchangeToSystem(
						$transaction['currencyId'],
						$transaction['value']);

			It6_DbTransaction::commit($db);

			return $ret;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("withdrawCash.", 0, $e);
		}
	}

	/**
	 * Deposit user's cash to it's account in the branch
	 * @param integer $userId
	 * @param float $amount money (in the central currency) to deposit
	 * @return struct info about made deposit
	 * @throws It6_XmlRpc_Exception on failure
	 */
	public static function depositCash($userId, $amount) {
		$db = Webservice_Transaction::getDb();
		It6_DbTransaction::begin($db);
		$ret = array();
		try {

			$depositUser = Webservice_User::getById($userId);
			if ( $depositUser['anonymous'] )  
				throw new Exception("User can not be annonymous.");
			else if ( empty($depositUser['activationTime']) )  
				throw new Exception("User is not activated.");

			$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);

			Webservice_Transaction::make(array(
				'userId' => $userId,
				'hostId' => $hostId,
				'value' => $amount,
				'currencyId' => Webservice_Currency::getSystemId(),
				'typeName' => Webservice_TransactionType::NAME_USER_DEPOSIT_CASH
				));

			Webservice_Transaction::make(array(
				'userId' => $userId,
				'hostId' => $hostId,
				'value' => $amount,
				'currencyId' => Webservice_Currency::getSystemId(),
				'typeName' => Webservice_TransactionType::NAME_BRANCH_USER_DEPOSIT_CASH
				));
			
			$crc = array(
				'userId'    => $userId,
				'hostId'    => $hostId,
				'ticketId'  => null
			);
			
			if ( Webservice_Campaign::validate('BranchVisit', Webservice_Campaign::NAMESPACE_GET, $crc) ) {
				$ret['pointBranchVisit'] = Webservice_Campaign::get(
						'BranchVisit',
						Webservice_Campaign::NAMESPACE_GET,
						'Value',
						$crc
				);
			}
			Webservice_Campaign::putOn(
					'BranchVisit',
					Webservice_Campaign::NAMESPACE_GET,
					$crc);

			It6_DbTransaction::commit($db);

			return $ret;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("User cash deposit has failed.", 0, $e);
		}
	}

	/**
	 * Gets all waiting user cash withdraws
	 * @param integer $userId
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getWithdraws($userId) {
		$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);
		return Webservice_Transaction::getAllWhere(array(
			'hostId = ?' => $hostId,
			'userId = ?' => $userId,
			'typeName = ?' => Webservice_TransactionType::NAME_USER_WITHDRAW_CASH,
			'status = ?' => Webservice_Transaction::STATUS_PRE_DEPOSIT));
	}

	/**
	 *
	 * Return user balance in the given time
	 * @param integer $userId
	 * @param string $time
	 * @param boolean eliminateCancelTransaction
	 * @return float
	 */
	public static function getBalance($userId, $time, $eliminateCancelTransactions = false) {
		try {
			$db = static::getMainDb();
				$ret = $db->select()
					->from(array('bl' => Webservice_Transaction::$BALANCE_LOG_TABLE), array('end_balance'))
					->join(
						array('ft' => Webservice_Transaction::$TABLE),
						'ft.transaction_id=bl.transaction_id AND ft.user_id='.intval($userId),
						array()
					)
					->join(array('tt' => Webservice_TransactionType::$TABLE), 'tt.id = ft.type_id', array())
					->where('COALESCE(ft.account_type,tt.account_type) = ?', Webservice_TransactionType::ACCOUNT_TYPE_USER)
					->where('bl.time<?', $time)
					->order(array('bl.time DESC', 'bl.transaction_id DESC'))
					->limit(1)
					->query()->fetch();

			if ( $eliminateCancelTransactions ) {

					$cancels = $db->select()
						->from(
							array('ft' => Webservice_Transaction::$TABLE),
							array('value' => 'Sum(ft.value)')
						)
						->join(
							array('ftc' => Webservice_Transaction::$TABLE),
							'ft.canceled_transaction_id = ftc.transaction_id',
							array())
						->join(
							array('tt' => Webservice_TransactionType::$TABLE),
							'tt.id = ftc.type_id',
							array())
						->where('COALESCE(ftc.account_type,tt.account_type) = ?', Webservice_TransactionType::ACCOUNT_TYPE_USER)
						->where('ftc.user_id = ?', $userId)
						->where('ft.okTime > ?', $time)
						->where('ftc.okTime < ?', $time)
						->limit(1)
						->query()->fetch();

					if ( empty($ret) ) $ret = array('end_balance' => 0.0);
					$ret['end_balance'] = (float)$ret['end_balance'] + (float)$cancels['value'];
				}

			return empty($ret) ? 0.0 : $ret['end_balance'];
		} catch ( Exception $e) {
			throw new It6_XmlRpc_Exception("User::getBalance.", 0, $e);
		}
	}

	/**
	 * Update user's password (no validation)
	 * @param boolean $byId Determines if parameter $user is ID (TRUE) or username (FALSE)
	 * @param string $user Username or user id of user whose password to update
	 * @param string $newPassword New password value, if empty value is given, value is generated
	 * @param boolean $notifyByEmail TRUE if user should be informed by email about new password
	 * @param string $oldPassword [optional] If set then password is updated only if current password is matched and different than new password
	 * @return boolean TRUE if password was updated (no change throws exception)
	 */
	public static function setPassword($byId, $user, $newPassword, $notifyByEmail, $oldPassword = null) {
		if (isset($oldPassword) && $newPassword == $oldPassword)
			throw new It6_XmlRpc_Exception('New password is same as the old one');
		$db = static::getMainDb();
		$userId = null;
		try {
			if (!$byId) {
				$rows = $db->select()->from(static::$TABLE, array('id' => 'user_id'))
					->where('nick=?', $user)->query()->fetchAll();
				if (!empty($rows))
					$userId = $rows[0]['id'];
			}
			else
				$userId = $user;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('Cannot retrieve user id', 0, $e);
		}
		if (empty($userId))
			throw new It6_XmlRpc_Exception('Unknown user');
		try {
			if (empty($newPassword))
				$newPassword = It6_Models_User::generatePassword(false);
			$where = array('user_id=?' => $userId);
			if (isset($oldPassword))
				$where['heslo=?'] = It6_Models_User::cryptPassword($oldPassword);
			$updated = $db->update(
				static::$TABLE,
				array('heslo' => It6_Models_User::cryptPassword($newPassword)),
				$where
			);
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('Password update failed', 0, $e);
		}
		if (empty($updated))
			throw new It6_XmlRpc_Exception('Password was not updated');
		if ($notifyByEmail) {
			try {
				$params = array(
					It6_Cron_Job_Email::PARAM_EMAIL_TYPE	=> 'PasswordChanged',
					'user_id'		=> $userId,
					'new_password'	=> $newPassword
				);
				$email = array(
					'type' 		=> 1,
					'date'		=> It6_Date::dbNow(),
					'params'	=> $params
				);
				Webservice_CronJob::insert($email);
			}
			catch (Exception $e) {
				throw new It6_XmlRpc_Exception('Password updated but notification email not sent');
			}
		}
		return true;
	}
	
	/**
	 * Ban user with note
	 * @param integer $userId
	 * @param string $note
	 * @return true on success
	 */
	public function banWithNote($userId, $note) {
		$db = self::getDb();
		It6_DbTransaction::begin();
		try {

			$db->update(
				self::$TABLE,
				array(
					'zakazany' => 1,
					'ban_note' => $note
				),
				array('user_id = ?' => $userId)
			);

			It6_DbTransaction::commit();
			It6_Log::info(
				"User '%user_id%' were banned.  (note = '%note%')",
				It6_Log::TAG_ADMIN_OPERATION,
				array('users_id'=>  $userId, 'note' => $note)
			);
			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback();
			It6_Log::err(
				"Error ban with note '%user_id%'.  (note = '%note%')",
				It6_Log::TAG_ADMIN_OPERATION,
				array('users_id'=>  $userId, 'note' => $note)
			);
			throw new It6_XmlRpc_Exception('User::banWithNote',0,$e);
		}
	}

	/**
	 * Sets user's initial bonus base (amount of money from which initial bonus will be computed).
	 * Value will be limited to maximal
	 * Start datetime of user's bonus time period will be updated if it is NULL.
	 * @param integer|struct $user User ID or user entity
	 * @param float $base Amount of money in user currency
	 * @param boolean $onlyIfNotSetAlready Update base only if base was not set before
	 * @param boolean $absolute If TRUE (default) set base to given $base, otherwise add $base to actual DB value.
	 * @return boolean FALSE=not set because was already set, TRUE=was set
	 */
	public static function setEntryBonusBase($userId, $base, $onlyIfNotSetAlready, $absolute = true) {
		$user = Webservice_User::getById($userId);
		if (empty($user))
			throw new It6_XmlRpc_Exception('User not found. userId=' . $userId);
		if (isset($user['ebFrom'])) // bonus time period hasn't started
			return false;
		$version = $user['ebVersion'];
		if (1 != $version)
			throw new It6_XmlRpc_Exception('Unsupported entry bonus version: ' . $version);

		$db = self::getDb();
		$dbAdmin = Webservice_AbstractWebService::getAdminDb();
		try {
			$maxTotal = It6_Models_Parameter::getDataByName(It6_Models_Parameter::NAME_ENTRY_BONUS_MAX_TOTAL . ".$version", $dbAdmin);
			$maxTotal = (empty($maxTotal) ? 0.0 : floatval($maxTotal['value']));
			if (0 != $maxTotal)
				$maxTotal = It6_Models_Currency::convertAmountToCurrency($user['currencyId'], $maxTotal, $db);
			$bonus = ($base > $maxTotal ? $maxTotal : $base);
			$where = array('user_id = ?' => $userId);
			if ($onlyIfNotSetAlready)
				$where[] = 'entry_bonus_base IS NULL';
			$sqlBase = ($absolute ? $base : "entry_bonus_base+($base)");
			$n = $db->update(
				self::$TABLE,
				array(
					'entry_bonus_base' => new Zend_Db_Expr($sqlBase),
					// after entry_bonus_base column update
					'entry_bonus_amount' => new Zend_Db_Expr(0 != $maxTotal ? "LEAST($maxTotal,entry_bonus_base)" : 'entry_bonus_base'),
				),
				$where
			);
			$s = $db->update(
				self::$TABLE,
				array('entry_bonus_from' => It6_Date::dbNow()),
				array(
					'user_id=?' => $userId,
					'entry_bonus_from IS NULL',
				)
			);
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback();
			It6_Log::err(
				'Entry bonus base not set. userId=%userId% base=%base%',
				It6_Log::TAG_TRANSACTION,
				array('userId'=> $userId, 'base' => $base)
			);
			throw new It6_XmlRpc_Exception('User::setInitialBonusBase', 0, $e);
		}
		if (0 < $n) {
			It6_Log::info(
				'Entry bonus base was set: userId=%userId% base=%base%',
				It6_Log::TAG_TRANSACTION,
				array('userId'=> $userId, 'base' => $base)
			);
		}
		if (0 < $s) {
			It6_Log::info(
				'Entry bonus period has started: userId=%userId%',
				It6_Log::TAG_TRANSACTION,
				array('userId'=> $userId)
			);
		}
		return (0 < $n);
	}

	/**
	 * Get all users to check if entry bonus should be applied for them.
	 * @param boolean $onlyIds Set to TRUE if array of user IDs should be returned, FALSE for array of entitied
	 * @return array List of user IDs or list of user entities
	 */
	public static function getAllForEntryBonus($onlyIds) {
		$where = array('ebFrom IS NOT NULL', 'ebApplied IS NULL');
		if ($onlyIds) {
			$ids = array();
			$users = static::getAllWhereColumns($where, array('userId'));
			if (!empty($users)) {
				foreach ($users as $user)
					$ids[] = $user['userId'];
			}
			return $ids;
		}
		else
			return static::getAllWhere($where);
	}

		/**
	 * Get all users to check if birthday bonus should be applied for them.
	 * @param boolean $onlyIds Set to TRUE if array of user IDs should be returned, FALSE for array of entitied
	 * @return array List of user IDs or list of user entities
	 */
	public static function getAllForBirthdayBonus($onlyIds) {
		$where = array('MONTH(datum_narozeni) = MONTH(NOW())', 'DAY(datum_narozeni) = DAY(NOW())', 'datum_aktivace IS NOT NULL');
		if ($onlyIds) {
			$ids = array();
			$users = static::getAllWhereColumns($where, array('userId'));
			if (!empty($users)) {
				foreach ($users as $user)
					$ids[] = $user['userId'];
			}
			return $ids;
		}
		else
			return static::getAllWhere($where);
	}
	

		/**
	 * Get all users to check if registration bonus should be applied for them.
	 * @param boolean $onlyIds Set to TRUE if array of user IDs should be returned, FALSE for array of entitied
	 * @return array List of user IDs or list of user entities
	 */
	public static function getAllForRegistrationBonus($onlyIds) {
		$where = array('MONTH(datum_registrace) = MONTH(NOW())', 'DAY(datum_registrace) = DAY(NOW())', 'datum_aktivace IS NOT NULL', 'YEAR(datum_narozeni) != YEAR(NOW())');
		if ($onlyIds) {
			$ids = array();
			$users = static::getAllWhereColumns($where, array('userId'));
			if (!empty($users)) {
				foreach ($users as $user)
					$ids[] = $user['userId'];
			}
			return $ids;
		}
		else
			return static::getAllWhere($where);
	}
	/**
	 * Create a cash withrawal trasaction and if it meets the requirements confirm it
	 * @param struct $transactionData
	 * 
	 */
	public static function requestCashWithdrawal($transactionData) {
		if (!Webservice_Branch::isWithdrawEnabled($transactionData['hostId'], true)) {
			throw new It6_XmlRpc_Exception('Withdrawal is not allowed', 100);
		}
		try {
			$transactionId = Webservice_Transaction::make($transactionData);

			$acOn = Webservice_Parameter::getEffectiveValue('branch.transaction.userWithdrawCash.autoconfirm', array('host' => $transactionData['hostId']));
			if($acOn == 1) {
				$inOut = Webservice_Host::getInOut($transactionData['hostId']);
				$hosts = Webservice_Host::getAllWhereColumns(array('hostId=?' => $transactionData['hostId']), array('currencyId'));
				$host = reset($hosts);
				$ccSources = It6_Models_Currency::convertAmountFromCurrency($host->currencyId, $inOut['sources'], true);
				$ccTransValue = It6_Models_Currency::convertAmountFromCurrency($transactionData['currencyId'], $transactionData['value'], true);
				$reserve = Webservice_Parameter::getEffectiveValue('branch.fundReserve.autoconfirm', array('host' => $transactionData['hostId']));
				$maxAmount = Webservice_Parameter::getEffectiveValue('transaction.withdrawCash.autoconfirm.max', array('user' => $transactionData['userId']));

				if(
					$ccSources - $reserve + $ccTransValue >= 0
					&& abs($ccTransValue) < abs($maxAmount)
				) {
					It6_Log::info(
							"Autoconfirming transaction '%transactionId%'...",
							It6_Log::TAG_TRANSACTION,
							array('transactionId' => $transactionId)
					);
					Webservice_Transaction::confirm($transactionId);
				}
			}
			
			It6_Log::info(
				"Withdraw request branch success",
				It6_Log::TAG_USER_OPERATION,
				array(
					'userId' => $transactionData['userId'],
					'amount' => $transactionData['value'],
					'hostId' => $transactionData['hostId'],
					'notes'	 => $transactionData['notes'],
				)
			);
		
			return true;
		}
		catch (Exception $e) {
			if(!empty($transactionId))
				throw new It6_XmlRpc_Exception('Cant create user cash withdrawal request.', 0, $e);
			else
				throw new It6_XmlRpc_Exception('Cant autocommit cash withdrawal request.', 0, $e);
		}
	}
	
	/**
	 * Get blocked Ip address list.
	 * IP address is blocked (for user), if unsuccessfull login attempt count is
	 * grater than MAX_LOGIN constant.
	 * @param int $userId
	 * @return array
	 */
	public static function getBlockedIps($userId) {
		return self::getDb()->select()
				->from(static::$TABLE_LOGIN_ATTEMPTS,
						'block_ip, ('.MAX_LOGIN_TIMEOUT.' - TIME_TO_SEC(TIMEDIFF(now(), max(block_time))) ) AS secondsRemaining')
				->where('user_id = ?', $userId)
				->group('block_ip')
				->having('COUNT(block_ip) > ?', MAX_LOGIN)
				->having('secondsRemaining > 0')
				->order('block_time DESC')				
				->query()
				->fetchAll();		
	}
	
	public static function removeBlockedIps($userId, $blockedIps) {
		return self::getDb()->delete(
				static::$TABLE_LOGIN_ATTEMPTS,
				array('user_id = ?' => $userId,	'block_ip IN (?)' => $blockedIps));
	}
	
	/*
	 * Nastaví výchozí username u<id>. Username se asi nebude používat,
	 * pokud ano, mělo by jít upravit.
	 * Username (nick) bylo nahrazeno emailem a z registrace odebráno
	 * kvůli redukci počtu inputů.
	 */
	public static function getDefaultUsername() {
		$result = self::getDb()->select()
				->from(static::$TABLE,'MAX(user_id)+1 AS num')
				->query()
				->fetch();
		return 'u'.$result['num'];
	}
	
	/**
	 * Definuje registraci jako duplicitní na základě shodného jména, příjmení,
	 * adresy a data narození.
	 * @param type $userData
	 * @return bool
	 */
	public static function dulicateRegistration($userData) {
		$userCheck = Zend_Registry::get('db')->select()
			->from( self::$TABLE, array('c' => 'COUNT(*)') )
			->where(array_search('firstName', self::$CONV).' = ?', trim($userData['firstName']))
			->where(array_search('lastName', self::$CONV).' = ?', trim($userData['lastName']))
			->where(array_search('street', self::$CONV).' = ?', $userData['street'])
			->where(array_search('zip', self::$CONV).' = ?', $userData['zip'])
			->where(array_search('birthDate', self::$CONV).' = ?', $userData['birthDate'])
			->limit(1)
			->query()->fetchAll();
		return $userCheck[0]['c'] != 0;
	}
	
	public static function sendActivationMail($userId, $userEmail, $activateUrl, $view) {
		$activationLink = self::getActivationLink($userId, $userEmail, $activateUrl);
		// -- local --
		/*
		$config = array('auth' => 'login',
                'username' => 'mirkuv@gmail.com',
                'password' => 'jpvmrpzgrrjloury',
                'ssl' => 'tls');
		*/
		// -- local --
		//$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);

		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($view->trans('activation_email_1') . ',<br>'
				. $view->trans('activation_email_2').':<br>'
				. '<a href="'.$activationLink.'">'.$activationLink.'</a>'.'<br>'.'<br>'
				. $view->trans('activation_email_3'));
		$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
		$mail->addTo($userEmail);
		$mail->setSubject($view->trans('activation_email_subject'));
		$mail->send(/*$transport*/); // -- local --
	}
	
	public static function getActivationLink($userId, $userEmail, $activateUrl) {
		return 'http://' . str_replace('//', '/',
				WEBHOST . $activateUrl . 'email/' . $userEmail
				. '/q/' . self::getActivationValue($userId, $userEmail));
	}
	
	public static function getActivationValue($userId, $userEmail) {
		return md5(sha1($userId).md5($userEmail));
	}
	
	/**
	 * @param string $userEmail
	 * @param string $activationValue
	 * @return boolean or userId
	 */
	public static function isAuthorizedForActivation($userEmail, $activationValue) {
		$user = static::getAllWhere(array('email = ?' => $userEmail));
		$userId = $user[0]['userId'];
		if(!$userId){
			return false;
		}
		return ($activationValue == self::getActivationValue($userId, $userEmail))?
			$userId : false;
	}
	
	/**
	 * 
	 * @param type $userId
	 * @return bool true: activated; false: already activated
	 */
	public static function activate($userId) {
		$db = static::getDb();
		$countAffected = $db->update(self::$TABLE,
				array(array_search('activationTime', self::$CONV) => It6_Date::dbNow()),
				array('user_id = ?' => $userId,
					array_search('activationTime', self::$CONV) . ' IS NULL'));
		return $countAffected == 1;
	}

}