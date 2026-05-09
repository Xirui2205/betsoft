<?php
class Services_LiveBettingApp {

	const TIMEOUT = 20;
	const LANG_ID = 1;
	private $currencies = array();
	private $feeFix = null;
	private $feeRel = null;
	private $minAmount = null;
	private $maxAmount = null;

	/**
	* log user from mobile app
	* @param string username
	* @param string password
	* @return string
	*/
	public function loginUser($username, $password){
		$encrypted = Help::cryptPass(Help::DecodeUnicodeUrl($password));

		$dbSes = Zend_Registry::get('dbSes');
		$db = Zend_Registry::get('db');

		$row = $db->select()
		->from(
			array('u' => 'uzivatel'), 
			array('u.user_id', 'u.nick', 'u.datum_aktivace', 'u.zakazany', 'count(b.block_ip) AS block', 'u.ucet_status'))
		->joinLeft(
			array('b' => 'uzivatel_block'),
			"u.user_id = b.user_id AND b.block_ip = '" . Help::Slash(It6_Php::getRemoteAddr()) . "' AND (now() - b.block_time) < '" . MAX_LOGIN_TIMEOUT . "'",
			array()
		)
		->where('u.nick = ?', Help::DecodeUnicodeUrl($username))
		->where('u.heslo = ?', $encrypted)
		->where('u.datum_aktivace <= ?', It6_Date::dbNow())
		->where('u.anonymous=?', 0)
		->where('u.self_excluded_until <= ?', It6_Date::dbNow())
		->limit(1)
		->query()->fetch();

		#uzivatel byl nalezen#
		if ( !empty($row) && !empty($row['datum_aktivace']) && $row['zakazany'] == 0 &&
			//IT6: forced SSL
			//isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' &&
			$row['block'] <= MAX_LOGIN) {

			//user is logged
			session_regenerate_id(false);

			//TODO: zjistit jestli neni uzivatel jiz prihlasen
			$id = session_id();
			$userStatus = ( $row['zakazany'] ? 5 : (isset($row['ucet_status']) && $row['ucet_status']==2 ? 6 : 3) );
			//$session_id = Zend_Registry::get('ws')->Session->generateLivebettingSession($id);

			$dbSes->delete("session_app", array("user_id = ?" => $row["user_id"]));
			$livebettingSession = Zend_Registry::get('ws')->Session->generateLivebettingSession($id);

			$dbSes->insert("session_app", array(
					"user_id" => $row["user_id"],
					"session_id" => $livebettingSession,
					"last_activity" => It6_Date::dbNow()

				));

			$netip = preg_split("/\.{1}/",It6_Php::getRemoteAddr(),-1,PREG_SPLIT_NO_EMPTY);
			$data = '';

				$sql = "delete from session where user_id=" . $row['user_id'];
				$dbSes->query($sql);

				(isset($_SERVER['HTTP_USER_AGENT'])) ? $user_agent = $_SERVER['HTTP_USER_AGENT'] : $user_agent = '';

				$sql = "INSERT INTO session ".
						"(data,start,status,zprava,user_id,ses_id,ip,prohlizec,time,livebetting_session) ".
						"VALUES ('".
							Help::Slash($data)."','".
							It6_Date::dbNow()."',".
							$userStatus.",'".
							($row['zakazany']?"Váš účet byl zaplokován":"")."',".
							$row['user_id'].",'".
							addslashes($id)."','".
							$netip[0].".".$netip[1].".".$netip[2]."','".
							addslashes(md5($user_agent."somestring"))."',".
							time().",'".
							Help::Slash($livebettingSession)."')";

				$res = $dbSes->query($sql);
				
				$sql = "update uzivatel set block=0,block_ip='' where user_id=".$row['user_id'];
				$res = $db->query($sql);

				$GLOBALS['ses_status'] = 3;
				$db->update('uzivatel', array(
						'posledni_prihlaseni' => It6_Date::dbNow(),
						'block' => 0,
						'block_time' => null,
					),
					array('user_id=?' => intval($row['user_id']))
				);

			return $livebettingSession;

		} else {
			throw new Exception(It6_Models_Translator::translate("auth_failed", self::LANG_ID, $db));
		}
	}

	/**
	* get user info
	* @param string handle
	* @return array
	*/
	public function getUserInfo($handle) {

		$db = Zend_Registry::get("db");

		// naleznu uzivatele s aktivni session_app
		$tm = localtime(time(), true);
		$validTime = mktime(
			$tm['tm_hour'],
			$tm['tm_min'] - self::TIMEOUT,
			$tm['tm_sec'],
			$tm['tm_mon'] + 1,
			$tm['tm_mday'],
			$tm['tm_year'] + 1900
		);

		$validTime = It6_Date::timestampToDb($validTime);

		try {
			$select = $db->select()
				->from(array("u" => "vic_main.uzivatel"), array(
					"name" => "u.jmeno",
					"surname" => "u.prijmeni",
					"variableSymbol" => "u.handle",
					"balance" => "i.zustatek"
				))
				->join(array("s" => "vic_session.session_app"), "u.user_id = s.user_id", null)
				->join(array("i" => "vic_main.uzivatel_im_data"), "u.user_id = i.user_id", null)
				->where("s.session_id = ?", $handle)
				->where("s.last_activity > ?", $validTime);

			$tmp = $select->query()->fetch();

			if (!isset($tmp["name"]) || empty($tmp)) 
				throw new Exception(It6_Models_Translator::translate("invalid_handle", self::LANG_ID, $db));

			self::updateSessionTimeout($handle);

			//only opened
			$states = array(1);
			$userId = self::getUserIdByHandle($handle);

			$live_tickets = Webservice_Livebetting::getAllByState($states, array('userId = ?' => $userId));
			$live_tickets = It6_ArrayWrapper::toNativeArray($live_tickets);

			$tmp["openedLiveTicketsCount"] = count($live_tickets);
			return $tmp;
		}
		catch (Exception $e) {
			It6_Log::err(
				"Invalid session handle'%handle%'.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('handle' => $handle), $e
			);
			throw new Exception(It6_Models_Translator::translate("invalid_handle", self::LANG_ID, $db));
		}
	}

	/**
	* get full user info
	* @param string handle
	* @return array
	*/
	public function getUserInfoFull($handle) {

		$db = Zend_Registry::get("db");

		$tm = localtime(time(), true);
		$validTime = mktime(
			$tm['tm_hour'],
			$tm['tm_min'] - self::TIMEOUT,
			$tm['tm_sec'],
			$tm['tm_mon'] + 1,
			$tm['tm_mday'],
			$tm['tm_year'] + 1900
		);

		$validTime = It6_Date::timestampToDb($validTime);

		try {
			$select = $db->select()
				->from(array("u" => "vic_main.uzivatel"), array(
					"name" => "u.jmeno",
					"surname" => "u.prijmeni",
					"gender" => "u.pohlavi",
					"citizenId" => "u.citizen_id",
					"street" => "u.ulice",
					"zip" => "u.psc",
					"city" => "u.misto",
					"birthday" => "u.datum_narozeni",
					"email" => "u.email",
					"phone" => "u.telefon",
					"nickname" => "u.nick",
				))
				->join(array("s" => "vic_session.session_app"), "u.user_id = s.user_id", null)
				->where("s.session_id = ?", $handle)
				->where("s.last_activity > ?", $validTime);

			$tmp = $select->query()->fetch();

			if (!isset($tmp["name"]) || empty($tmp)) 
				throw new Exception(It6_Models_Translator::translate("invalid_handle", self::LANG_ID, $db));

			self::updateSessionTimeout($handle);

			$select = $db->select()
				->from(array("u" => "vic_main.uzivatel"), array(
					"bankCode"			=> "b.bank_code",
					"bankName"			=> "b.bank_name",
					"accountPrefix"		=> "a.account_prefix",
					"accountNumber"		=> "a.account_number"
				))
			->join(array("s" => "vic_session.session_app"), "u.user_id = s.user_id", null)
			->join(array("a" => "vic_main.user_bank_account"), "u.user_id = a.user_id", null)
			->join(array("b" => "vic_main.bank"), "a.bank_id = b.bank_id", null)
			->where("s.session_id = ?", $handle);

			$tmp2 = $select->query()->fetch();
			//aby to vratilo alespon null hodnoty
			$tmp["bankCode"] = $tmp2["bankCode"];
			$tmp["bankName"] = $tmp2["bankName"];
			$tmp["accountPrefix"] = $tmp2["accountPrefix"];
			$tmp["accountNumber"] = $tmp2["accountNumber"];

			return $tmp;
		}
		catch (Exception $e) {
			It6_Log::err(
				"Invalid session handle'%handle%'.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('handle' => $handle), $e
			);
			throw new Exception(It6_Models_Translator::translate("invalid_handle", self::LANG_ID, $db));
		}
	}

	/**
	* get config
	* @return array
	*/
	public function getConfig() {
		return array(
			"bankName" => VIC_BANK_NAME,
			"bankAdress" => VIC_BANK_ADDRESS,
			"bankCode" => VIC_BANK_CODE,
			"bankAccount" => VIC_BANK_ACCOUNT
		);
	}

	/**
	* request withdrawal from bank account
	* @param string handle
	* @param integer amount
	* @return boolean
	*/
	public function requestWithdrawalFromAccount($handle, $amount) {

		$userId = $this->getUserIdByHandle($handle);
		$db = Zend_Registry::get("db");
		$ws = Zend_Registry::get("ws");

		$userInfo = $this->getUserInfo($handle);
		$userBalance = $userInfo["balance"];

		$transactionType = Webservice_TransactionType::getByName(Webservice_TransactionType::NAME_USER_WITHDRAW_BANK);
		$limitLow = max(0, -$transactionType->highLimit);
		$limitHigh = min($userBalance, -$transactionType->lowLimit);

		if ($amount < $limitLow) {
			$one = str_replace("%value%", $limitLow, It6_Models_Translator::translate("Minimum possible choice is %value% %currency%.", self::LANG_ID, $db));
			$message = str_replace("%currency%", 'CZK', $one);
			throw new Exception($message);
		}

		if ($amount > $limitHigh) {
			$one = str_replace("%value%", $limitHigh, It6_Models_Translator::translate("Maximum possible choice is %value% %currency%.", self::LANG_ID, $db));
			$message = str_replace("%currency%", 'CZK', $one);
			throw new Exception($message);
		}

		self::updateSessionTimeout($handle);

		if ($ws->User->canWithdraw($userId)) {

			$fee = $this->getFee(8,$amount);//prozatim pouze CZK

			if ( $amount <= 0 || ( $userBalance < $amount + $fee))
				throw new Exception(It6_Models_Translator::translate("not_sufficient_balance", self::LANG_ID, $db));

			$ws->Transaction->make(array(
				'typeName' => 'user.withdraw.bank',
				'userId' => $userId,
				'currencyId' => 8, //CZK
				'value' => -$amount,
				'fee' => $fee,
				"createAdminId" => It6_Models_Admin::ID_INTERNET,
				"confirmAdminId" => It6_Models_Admin::ID_INTERNET,
				'hostId' => It6_Models_Host::ID_INTERNET)
			);

			It6_Log::info(
				"Withdraw request bank success",
				It6_Log::TAG_USER_OPERATION,
				array(
					'userId' => $userId,
					'amount' => $amount,
					'fee' => $fee,
					'currencyId' => 8, //CZK
					'userBalance' => $userBalance
				)
			);
		} else {
			throw new Exception(It6_Models_Translator::translate('user_is_not_allowed_to_withdraw', self::LANG_ID, $db));
		}
	}

	/**
	* request withdrawal at branch
	* @param string handle
	* @param integer amount
	* @param integer branchId
	* @return boolean
	*/
	public function requestWithdrawalAtBranch($handle, $amount, $branchId) {

		$userId = $this->getUserIdByHandle($handle);
		$db = Zend_Registry::get("db");
		$ws = Zend_Registry::get("ws");

		$userInfo = $this->getUserInfo($handle);
		$userBalance = $userInfo["balance"];

		$transactionType = Webservice_TransactionType::getByName(Webservice_TransactionType::NAME_USER_WITHDRAW_CASH);
		$limitLow = max(0, -$transactionType->highLimit);
		$limitHigh = min($userBalance, -$transactionType->lowLimit);

		if ($amount < $limitLow) {
			$one = str_replace("%value%", $limitLow, It6_Models_Translator::translate("Minimum possible choice is %value% %currency%.", self::LANG_ID, $db));
			$message = str_replace("%currency%", 'CZK', $one);
			throw new Exception($message);
		}

		if ($amount > $limitHigh) {
			$one = str_replace("%value%", $limitHigh, It6_Models_Translator::translate("Maximum possible choice is %value% %currency%.", self::LANG_ID, $db));
			$message = str_replace("%currency%", 'CZK', $one);
			throw new Exception($message);
		}

		self::updateSessionTimeout($handle);

		if ($ws->User->canWithdraw($userId)) {
			$hosts = $ws->Host->getByBranchId($branchId);
			$hostId = $hosts[0]->hostId;
			$currencyId = It6_Models_User::get($userId, 'currencyId', $db);
			$transactionData = array(
				'typeName' => 'user.withdraw.cash',
				'userId' => $userId,
				'currencyId' => $currencyId,
				'hostId' => $hostId,
				'notes' => 'branchId: '.$branchId,
				'value' => -$amount,
				"createAdminId" => It6_Models_Admin::ID_INTERNET,
				"confirmAdminId" => It6_Models_Admin::ID_INTERNET
			);
			$ws->User->requestCashWithdrawal($transactionData);
		} else {
			throw new Exception(It6_Models_Translator::translate('user_is_not_allowed_to_withdraw', self::LANG_ID, $db));
		}
	}

	/**
	* get user tickets
	* @param string handle
	* @return array
	*/
	public function getTickets($handle) {

		$db = Zend_Registry::get("db");
		$ws = Zend_Registry::get("ws");

		$userId = $this->getUserIdByHandle($handle);

		if (!empty($userId)) {

			self::updateSessionTimeout($handle);

			$live_tickets = Webservice_Livebetting::getAllWhereOrder(array('userId = ?' => $userId), array("timeCreated DESC"));
			$live_tickets = It6_ArrayWrapper::toNativeArray($live_tickets);

			$i = 1;

			$tickets = array();
			foreach ($live_tickets as $ticket) {
				$i++;
				$ticket["state"] = It6_Models_Translator::translate($ticket["state"], self::LANG_ID, $db);
				$ticket["timeCreate"] = It6_Date::fromDb($ticket['timeCreated']);
				$tickets[] = $ticket;
				if ($i > 100)
					break;
			}

			return array(
				"classic" => array(),
				"live" => $tickets
			);
		} else {
			throw new Exception(It6_Models_Translator::translate("invalid_handle", self::LANG_ID, $db));
		}
	}

	/**
	* get user transactions
	* @param string handle
	* @return array
	*/
	public function getTransactions($handle) {

		$db = Zend_Registry::get("db");
		$ws = Zend_Registry::get("ws");

		$userId = $this->getUserIdByHandle($handle);

		if (!empty($userId)) {
			self::updateSessionTimeout($handle);

			$extensions = array();
			
			$table = new It6_WsForm_Table(array(
				array('transaction_id','transactionId'),
				array('tt.name','typeName'),
				array('value','value'),
				array('time','time'),
				array('balance','balance')
			));
			$table->getColumnsExtension($extensions);

			$filterDefUser = array(
				array('?'=> array('userId' => $userId)),
				array('?'=> array('accountType' => 'user'))
			);
			$filterUser = new It6_WsExtension_Client_Filter('filter2', $filterDefUser);
			$extensions[] = $filterUser;


			$transactions	= Zend_Registry::get('ws')->ext($extensions)->Transaction->getAllHistory();

			$ret = array();

			foreach ( $transactions as $transaction ) {
				if ( 'canceled' == $transaction->bhStatus )
					$transaction->value *= -1;
				//if ( $transaction->startBalance == $transaction->endBalance )
				//	$transaction->value = '-';

				$ret[] = array(
					"id" => $transaction->handle,
					"time" => It6_Date::fromDb($transaction->time),
					"value" => $transaction->value,
					"ticketId" => $transaction->ticketId,
					"endBalance" => $transaction->endBalance,
					"currencyCode" => $transaction->currencyCode,
					"status" => It6_Models_Translator::translate("transaction-" . $transaction->bhStatus, self::LANG_ID, $db),
					"typeName" => It6_Models_Translator::translate("transactions-" . $transaction->typeName, self::LANG_ID, $db)
					);
				}

			return $ret;
		} else {
			throw new Exception(It6_Models_Translator::translate("invalid_handle", self::LANG_ID, $db));
		}
	}


	/**
	* get branches
	* @return array
	*/
	public function getBranches() {

		$ws = Zend_Registry::get("ws");
		$branches = $ws->Branch->getAllWhere(array("isActive = ?" => 1, 'banned IS NULL', "isListed = ?" => 1));
		$ret = array();

		foreach ($branches as $branch) {

			$ret[] = array(
				"name" 			=> $branch->name,
				"place"			=> $branch->place,
				"street"		=> $branch->street,
				"town"			=> $branch->town,
				"id"			=> $branch->branchId,
				"zip"			=> $branch->zip,
				"openingHours" 	=> $ws->Branch->getOpeningHoursByBranchId($branch->branchId),
				"longitude"		=> $branch->longitude,
				"latitude"		=> $branch->latitude
			);
		}
		return $ret;
	}

	/**
	 * get stats
	 * @param string handle
	 * @return array
	 */
	public function getStats($handle) {
		$db = Zend_Registry::get("db");
		$tickets = $this->getTickets($handle);
		return array(
			"transactions" => count($this->getTransactions($handle)),
			"tickets" => count($tickets["live"]),
			"branches" => count($this->getBranches())
		);
	}

	/**
	 * return actual promos
	 * @param array promosSent
	 * @return array
	 */
	public function getActualPromos($promosSent = array()) {

		$tmp = array();
		$ret = array();
		foreach ($promosSent as $promo) {
			$tmp[$promo["promoId"]] = $promo["imgName"];
		}

		$promos =  Models_Marketing_Promo::getPromos(1, 1);

		foreach ($promos as $promoId => $promo) {
			$ret[] = array(
				"promoId"		=> $promoId,
				"url" 			=> $promo["url"],
				"text" 			=> $promo["text"],
				"title" 		=> $promo["title"],
				"imageChanged"	=> (isset($tmp[$promoId]) && ($tmp[$promoId] !=  $promo["img"])) ? 1 : 0,
				"imgName" 		=> $promo["img"],
				"url"			=> $promo["url"],
				"betId" 		=> (!empty($promo["bets"]["data"])) ? $promo["bets"]["data"]["0"]["sazka_id"] : null,
				"betLiveId" 	=> (!empty($promo["bets"]["data"])) ? $promo["bets"]["data"]["0"]["liveBetId"] : null,
				"validTo"		=> (isset($promo["validTo"])) ? $promo["validTo"] : null,
				"validFrom"		=> (isset($promo["validFrom"])) ? $promo["validFrom"] : null,
			);
		}

		return $ret;
	}

	private function updateSessionTimeout($handle) {
		$dbSes = Zend_Registry::get("dbSes");

		$dbSes->update("session_app", 
			array("last_activity" => It6_Date::dbNow()), 
			array("session_id = ?" => $handle));

		Webservice_Session::updateWebSessionTimestamp($handle);
	}

	public function getUserIdByHandle($handle) {

		$tm = localtime(time(), true);
		$validTime = mktime(
			$tm['tm_hour'],
			$tm['tm_min'] - self::TIMEOUT,
			$tm['tm_sec'],
			$tm['tm_mon'] + 1,
			$tm['tm_mday'],
			$tm['tm_year'] + 1900
		);

		$validTime = It6_Date::timestampToDb($validTime);

		$dbSes = Zend_Registry::get("dbSes");
		$select = $dbSes->select()
			->from('session_app')
			->where("session_id = ?", $handle)
			->where("last_activity > ?", $validTime);

		$tmp = $select->query()->fetch();
		return $tmp["user_id"];
	}

	public function getUserIdByHandle2($handle) {

		$user_id = $this->getUserIdByHandle($handle);

		if (!isset($user_id))
			return null;
		else {
			self::updateSessionTimeout($handle);
			return $user_id;
		}
	}

	private function getFee($currencyId, $amount) {
		$db = Zend_Registry::get("db");

		if (empty($this->currencies)) $this->setCurrencies();

		if (!array_key_exists($currencyId, $this->currencies)) {
			It6_Log::err(
				"Unknown currency ID: %value%.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('value' => $currencyId)
			);
			throw new ExHandler('comunication_error');
		}

		$currency = $this->currencies[$currencyId];
		return ($currency['rate'] * $this->feeFix + $amount * $this->feeRel);
	}

	private function setCurrencies() {
		$db = Zend_Registry::get("db");

		$transactionType = Zend_Registry::get('ws')->TransactionType
			->getByName(Webservice_TransactionType::NAME_USER_WITHDRAW_BANK);

		if (empty($transactionType)) {
			It6_Log::err(
				"Transaction type %value% not found.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('value' => $transactionType)
			);
			throw new ExHandler('comunication_error');
		}

		$this->minAmount = (isset($transactionType->lowLimit) ? $transactionType->lowLimit : false);
		$this->maxAmount = (isset($transactionType->highLimit) ? $transactionType->highLimit : false);
		$this->feeFix = (empty($transactionType->feeFix) ? 0 : $transactionType->feeFix);
		$this->feeRel = (empty($transactionType->feeRel) ? 0 : $transactionType->feeRel);

		foreach (It6_Models_Currency::readDataAll() as $id => $data) {
			if ($data['allowed']) {
				$rate = $data['rate'];
				$data2 = array(
					'feeFix' => $this->feeFix * $data['rate'],
					'feeRel' => $this->feeRel,
				);
				if (false !== $this->minAmount)
					$data2['minAmount'] = $this->minAmount * $rate;
				if (false !== $this->maxAmount)
					$data2['maxAmount'] = $this->maxAmount * $rate;
				$this->currencies[$id] = array_merge($data, $data2);
			}
		}
	}

	/**
	 * return true/false for valid/invalid sesion by handle
	 * @param string handle
	 * @return boolean
	 */
	public function isValidHandle($handle) {
		$db = Zend_Registry::get("db");

		$tm = localtime(time(), true);
		$validTime = mktime(
			$tm['tm_hour'],
			$tm['tm_min'] + self::TIMEOUT,
			$tm['tm_sec'],
			$tm['tm_mon'] + 1,
			$tm['tm_mday'],
			$tm['tm_year'] + 1900
		);

		$validTime = It6_Date::timestampToDb($validTime);

		// naleznu uzivatele s aktivni session_app
		$dbSes = Zend_Registry::get('dbSes');

		$select = $dbSes->select()
			->from('session_app')
			->where('session_id = ?', $handle)
			->where('last_activity > ?', $validTime);

		$res = $select->query()->fetch();

		if (!empty($res)) {
			self::updateSessionTimeout($handle);
			return true;
		} else {
			return false;
		}
	}
}