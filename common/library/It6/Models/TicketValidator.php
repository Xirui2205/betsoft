<?php

class It6_Models_TicketValidator extends It6_Models_DbDependent {

	/**
	 * @var integer $userId
	 */
	private $userId = null;

	/**
	 * @var It6_Models_Ticket $helper
	 */
	private $helper = null;

	/**
	 * @var integer $timestamp Timestamp which time dependent tests are made to
	 */
	private $timestamp = null;

	public function __construct($helper, $timestamp = null) {
		$this->helper = $helper;
		$this->userId = $helper->userId;
		$this->timestamp = (empty($timestamp) ? time() : intval($timestamp));
	}

	/*
	 public function isNotSame() {

	// more simple tickets can contain same bet
	if (It6_Models_Ticket::TYPE_SIMPLE == $this->helper->type)
	return true;

	$ids = array();
	$errors = array();

	foreach ( $this->helper->bets as $bet ) {
	if ( in_array($bet['id'], $ids) ) {
	$errors[] = $this->newError(
	16, $this->translate('ticket_er_16'), $bet['id'], $bet['column']);
	}
	$ids[] = $bet['id'];
	}

	return empty($errors) ? true : $errors;
	}
	*/

	public function isAko(){
		$dbAdmin = Zend_Registry::get('admindb');
		$paramAkoMinRate = It6_Models_Parameter::getDataByName(
			It6_Models_Parameter::NAME_TICKET_AKO_MINIMAL_RATE, $dbAdmin
		);
		$akoMinRate = (empty($paramAkoMinRate) ? 0.0 : floatval($paramAkoMinRate['value']));
		$result = $this->helper->checkBetsAko($akoMinRate);
		return $this->convertResult($result);
	}

	/**
	 * This method also sets wrapped It6_Models_Ticket instance's members
	 * $willExceedRiskLimit and $exceedesRiskLimitPercent.
	 * @throws Exception
	 */
	public function riskLimit(){

		$errors = array();
		$db = Zend_Registry::get('db');
		$dbAdmin = Zend_Registry::get('admindb');
		$centralCurrencyId = It6_Models_Currency::getCentralCurrencyId($db);
		$maxFinal = null;
		$msg = null;
		$paramLimPrcnt = It6_Models_Parameter::getDataByName(
			It6_Models_Parameter::NAME_TICKET_CONFIRM_BET_OVER_RISK_LIMIT_PERCENT,
			$dbAdmin
		);
		if (null === $paramLimPrcnt)
			throw new Exception('Required parameter not found. name="' . It6_Models_Parameter::NAME_TICKET_CONFIRM_BET_OVER_RISK_LIMIT_PERCENT . '"');
		$paramLimPrcnt = floatval($paramLimPrcnt['value']) / 100.0;
		$willExceedRiskLimit = array();
		$exceedesRiskLimitPercent = array();
		$betIds = $this->helper->getBetIds();
		$limits = array();
		if (!empty($betIds)) {
			try {
				// limit values are in central currency
				$rows = $db->select()->from(array('sazky'), array(
						'id' => 'sazka_id',
						'limit' => 'risk_limit',
						'balance' => 'risk_limit_balance',
					))
					->where('sazka_id IN (?)', $betIds)
					->query()
					->fetchAll();
			} catch ( Exception $e ) {
				throw new Exception($e);
			}
			
			foreach ($rows as $row)
				$limits[$row['id']] = $row;
		}
		foreach ($this->helper->bets as $bet) {
			$betId = $bet['id'];

			if (empty($limits[$betId]))
				continue;

			$betLimits = $limits[$betId];
			$amount = $bet['riskAmount'];
			$max = $max2 = null;
			$limBalance = floatval( $this->helper->convertAmountToTicketCurrency($betLimits['balance'], $centralCurrencyId, $db) );
			$limMax = floatval( $this->helper->convertAmountToTicketCurrency($betLimits['limit'], $centralCurrencyId, $db) );
			$limPrcnt = $limMax * $paramLimPrcnt;
			if ($amount > $limPrcnt) {
				$exceedesRiskLimitPercent[$betId] = true;
				$max = $limPrcnt;
			}
			if ($amount > $limMax) {
				$willExceedRiskLimit[$betId] = true;
				$max2 = 0;
			}
			else if ($amount + $limBalance > $limMax) {
				$willExceedRiskLimit[$betId] = true;
				$max2 = max(0, $limMax - $limBalance);
			}

			if ($max === null || ($max2 !== null && $max2 < $max))
				$max = $max2;

			// only already exceeded risk limit (max = 0) is reported as error
			if (null !== $max && 0 == $max && It6_Models_Ticket::TYPE_SIMPLE == $this->helper->type) {
				if (!isset($msg))
					$msg = $this->translate('ticket_er_2');
				if (!isset($currency))
					$currency = ' ' . $this->helper->getCurrencyName($db);
				$rv = $this->helper->roundStake($max, $this->helper->cash, $db);
				$errors[] = $this->newError(2, $msg, $betId, $bet['column'], $rv, $rv . $currency);
			}

			if (null !== $max && (null === $maxFinal || $max < $maxFinal) ) {
				$maxFinal = $max;
				$finalBetId = $betId;
				$finalColumnId = $bet['column'];
			}
		}

		// only already exceeded risk limit (max = 0) is reported as error
		if(null !== $maxFinal && 0 == $maxFinal && It6_Models_Ticket::TYPE_SIMPLE != $this->helper->type) {
			if (!isset($currency))
				$currency = ' ' . $this->helper->getCurrencyName($db);
			$newStakes = $this->helper->computeStakesFromChangedBetRiscAmount($finalBetId, $finalColumnId, $maxFinal);

			/*
			 if (!empty($newStakes['combinations'])) {
			$msg = '';
			$n = sizeof($newStakes['combinations']);
			foreach ($newStakes['combinations'] as $k => $stake)
			$msg .= "($k/$n): $stake $currency\n";
			}
			else
			$msg = $newStakes['stake'] . ' ' . $currency;
			*/

			if (It6_Models_Ticket::TYPE_MAXI == $this->helper->type) {
				$rv = $this->helper->roundStake($maxFinal, $this->helper->cash, $db);
				$msgParam = array($rv . $currency, ' (' . It6_Models_Bet::readBetText($finalBetId) . ')');
			}
			else {
				$rv = $this->helper->roundStake($newStakes['stake'], $this->helper->cash, $db);
				//$msgParam = $rv . ' ' . $currency;
				$msgParam = array($rv . $currency, ' (' . It6_Models_Bet::readBetText($finalBetId) . ')');
			}

			//if (It6_Models_Ticket::TYPE_SIMPLE == $this->helper->type)
			//	$errors[] = $this->newError(2, $this->translate('ticket_er'));
			//else
			$errors[] = $this->newError(2, $this->translate('ticket_er_2'), null, null, $rv, $msgParam);
		}

		$this->helper->willExceedRiskLimit = (empty($willExceedRiskLimit) ? false : array_keys($willExceedRiskLimit));
		$this->helper->exceedesRiskLimitPercent = (empty($exceedesRiskLimitPercent) ? false : array_keys($exceedesRiskLimitPercent));
		return (empty($errors) ? true : $errors);
	}

	public function userHasMoney() {
		//$user = It6_Models_User::getData($this->userId);
		//$balance = It6_Models_User::get($this->userId, 'balance');

		$err = false;
		if ( $this->helper->isPointTicket() ) {
			$db = Zend_Registry::get('db');
			$balance = It6_Models_User::getPointAccountBalance($this->userId, $this->helper->pointType, $db);
			if ($this->helper->stakeInPoints > $balance)
			$err = 'out_of_points';
		}
		else {
			$rateAdvance = floatval($this->helper->rateAdvance);
			if (0 != $rateAdvance) {
				$rateAdvanceCost = Zend_Registry::get('ws')->Campaign->get(
				'PreferenceRate',
				'spend',
				'Cost',
				array('preferenceSize' => round(100 * ($rateAdvance - 1)) ));
				$balance = It6_Models_User::getPointAccountBalance($this->userId, It6_Models_PointType::ID_RATEADVANCE, $db);
				if ($rateAdvanceCost > $balance)
				$err = 'out_of_points';
			}
			if ( empty($this->helper->cash) ) {
				$balance = It6_Models_User::get($this->userId, 'balance');
				if ($this->helper->stake > $balance)
				$err = 'ticket_er_6';
			}
		}
		if (!empty($err))
		return array( $this->newError(6, $this->translate($err)) );
		else
		return true;
	}

	/**
	 * @return array It6_Models_Ticket::TYPE_* => max bet number for type
	 */
	public static function getAllMaxBetNums() {
		$dbAdmin = Zend_Registry::get('admindb');
		$cfgLimitNames = array(
		It6_Models_Parameter::NAME_TICKET_MAX_BETS_SIMPLE,
		It6_Models_Parameter::NAME_TICKET_MAX_BETS_AKO,
		It6_Models_Parameter::NAME_TICKET_MAX_BETS_SYSTEM,
		It6_Models_Parameter::NAME_TICKET_MAX_BETS_MAXIKOMBI,
		It6_Models_Parameter::NAME_TICKET_MAX_GROUPS_MAXIKOMBI,
		);
		$cfgLimits = It6_Models_Parameter::getDataByName($cfgLimitNames, $dbAdmin);
		foreach ($cfgLimitNames as $name) {
			if (!isset($cfgLimits[$name]))
			throw new Exception('Required parameter not found. name="' . $name . '"');
		}
		return array(
		It6_Models_Ticket::TYPE_SIMPLE => intval($cfgLimits[It6_Models_Parameter::NAME_TICKET_MAX_BETS_SIMPLE]['value']),
		It6_Models_Ticket::TYPE_COMBI => intval($cfgLimits[It6_Models_Parameter::NAME_TICKET_MAX_BETS_AKO]['value']),
		It6_Models_Ticket::TYPE_SYSTEM => intval($cfgLimits[It6_Models_Parameter::NAME_TICKET_MAX_BETS_SYSTEM]['value']),
		It6_Models_Ticket::TYPE_MAXI => intval($cfgLimits[It6_Models_Parameter::NAME_TICKET_MAX_BETS_MAXIKOMBI]['value']),
		It6_Models_Parameter::NAME_TICKET_MAX_GROUPS_MAXIKOMBI =>
		intval($cfgLimits[It6_Models_Parameter::NAME_TICKET_MAX_GROUPS_MAXIKOMBI]['value']),
		);
	}

	/**
	 * @param boolean $canAutoAccept [optional] Will be set to FALSE if coupon/ticket cannot be accepted automatically, unmodified otherwise.
	 */
	public function isProveTicket(&$canAutoAccept = null){
		$db = Zend_Registry::get('db');
		$dbAdmin = Zend_Registry::get('admindb');
		$minStakeParam = (
		It6_Models_Ticket::TYPE_MAXI == $this->helper->type
		? It6_Models_Parameter::NAME_TICKET_MINIMAL_STAKE_MAXIKOMBI
		: It6_Models_Parameter::NAME_TICKET_MINIMAL_STAKE
		);
		$cfgLimitNames = array(
		$minStakeParam,
		It6_Models_Parameter::NAME_TICKET_MAXIMAL_STAKE,
		);
		$needsCombLimit = (It6_Models_Ticket::TYPE_MAXI == $this->helper->type);
		if ($needsCombLimit)
		$cfgLimitNames[] = It6_Models_Parameter::NAME_TICKET_MINIMAL_COMBINATION_STAKE;
		$cfgLimits = It6_Models_Parameter::getDataByName($cfgLimitNames, $dbAdmin);
		foreach ($cfgLimitNames as $name) {
			if (!isset($cfgLimits[$name]))
			throw new Exception('Required parameter not found. name="' . $name . '"');
		}
		$result = $this->helper->checkStakes(
		$cfgLimits[$minStakeParam]['value'],
		$cfgLimits[It6_Models_Parameter::NAME_TICKET_MAXIMAL_STAKE]['value'],
		($needsCombLimit ? $cfgLimits[It6_Models_Parameter::NAME_TICKET_MINIMAL_COMBINATION_STAKE]['value'] : false),
		$db
		);

		if ( true !== $result['result'] ) {
			$canAutoAccept = false;
			return $this->convertResult($result);
		}

		return true;
	}

	public function hasNotCorrelatedBets() {
		$db = Zend_Registry::get('db');
		$result = $this->helper->checkCorrelatedBets($db);
		return $this->convertResult($result);
	}

	public function limitDay() {

		$errors = array();
		$bets = array();
		$sports = array();

		$db = Zend_Registry::get('db');

		$sportData = $this->helper->getSportData(false, $db);
		$centralCurrencyId = It6_Models_Currency::getCentralCurrencyId($db);
		$limits = array();
		foreach ($sportData as $sportId => $sport) {

			// values are in CC
			$row = $db->select()
			->from(array('l'=>'limity'), array('dayLimit' => 'limit_den'))
			->joinLeft(
			array('lu'=>'limity_user'),
				'l.sport_id=lu.sport_id AND lu.user_id=' . $db->quote($this->userId),
			array('balance' => 'vycerpal', 'dayLimitUser' => 'limit_den_individual')
			)
			->where('lu.sport_id=?', $sportId)
			->query()
			->fetchAll();

			if (count($row) == 0)
			continue;

			$row = $row[0];
			$balance = (
			is_numeric($row['balance'])
			? $this->helper->convertAmountToTicketCurrency($row['balance'], $centralCurrencyId, $db)
			: 0
			);
			if (is_numeric($row['dayLimitUser']))
			$limits[$sportId] = max(
			0,
			$this->helper->convertAmountToTicketCurrency($row['dayLimitUser'], $centralCurrencyId, $db) - $balance
			);
			else if (is_numeric($row['dayLimit']))
			$limits[$sportId] = max(
			0,
			$this->helper->convertAmountToTicketCurrency($row['dayLimit'], $centralCurrencyId, $db) - $balance
			);
		}

		foreach ($limits as &$limit)
		$limit = $this->helper->roundStake($limit, $this->helper->cash, $db);
		unset($limit);

		$limit = false;
		foreach ($sportData as $sportId => $sport) {
			$amount = $sport['riskAmount'];
			if (!isset($limits[$sportId]))
			continue;
			$sportLimit = $limits[$sportId];
			if (($amount > $sportLimit) && (false === $limit || $limit > $sportLimit)) {
				$limit = $sportLimit;
				if (It6_Models_Ticket::TYPE_MAXI == $this->helper->type
				|| It6_Models_Ticket::TYPE_SYSTEM == $this->helper->type
				|| It6_Models_Ticket::TYPE_SIMPLE == $this->helper->type) {
					$msgParam = $sportLimit;
					$msgParam2 = $sport['name'];
				}
				else if (It6_Models_Ticket::TYPE_COMBI == $this->helper->type) {
					$msgParam = ($sportLimit / count($sport['bets'])) * count($this->helper->bets);
					$msgParam2 = $sport['name'];
				}
			}
		}
		if (false !== $limit) {
			$currency = ' ' . $this->helper->getCurrencyName($db);
			if (!empty($msgParam2)) {
				$langId = Zend_Registry::get('translate')->getCurrentLangId();
				$msgParam2 = array($msgParam . $currency, ' (' . It6_Models_Translator::get($msgParam2, $langId, $db) . ')');
			}
			$errors[] = $this->newError(
			2, $this->translate('ticket_er_2'), null, null, $msgParam, $msgParam2
			);
			//		if (It6_Models_Ticket::TYPE_SIMPLE == $this->helper->type)
			//			$errors[] = $this->newError(1, $this->translate('ticket_er'));
			return $errors;
		}

		return true;
	}

	public function isIndvLimit() {
		$result = $this->helper->checkUserLimit($this->userId, $db, false);
		return $this->convertResult($result);
	}

	public function isUserLimitSetting() {
		$result = $this->helper->checkUserLimitSetting($this->userId, $db, false);
		return $this->convertResult($result);
	}

	public function sameTicket(){

		try{
			$ws = Zend_Registry::get('ws');
			$acl = Zend_Registry::get('acl');
			$identity = array(
			'user' => $this->userId,
			'admin' => $acl->getIdentity(It6_Acl::IDNAME_ADMIN),
			'host' => $acl->getIdentity(It6_Acl::IDNAME_HOST),
			'branch' => $acl->getIdentity(It6_Acl::IDNAME_BRANCH)
			);
			$maxDuplicit = $ws->Parameter->getEffectiveValue(It6_Models_Parameter::NAME_TICKET_DUPLICATE_COUNT, $identity);
			$maxDuplicit = intval($maxDuplicit);
		}
		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
		$db = Zend_Registry::get('db');
		$counts = $this->helper->getTicketHashCounts($db);
		// preserve only counts over limit
		$counts = array_filter($counts, function($item) use ($maxDuplicit) {
			return $item['count'] >= $maxDuplicit;
		});
		if (empty($counts))
		return true;

		switch ($this->helper->type) {
			case It6_Models_Ticket::TYPE_SIMPLE:
				$errors = array();
				foreach ($counts as $data) {
					list($betId, $colId) = explode(':', $data['subject']);
					$errors[] = $this->newError(
					8,
					$this->translate('ticket_er_8'),
					$betId, $colId
					);
				}
				return $errors;
			case It6_Models_Ticket::TYPE_COMBI:
				return array($this->newError(
				8,
				$this->translate('ticket_er_8')));
			case It6_Models_Ticket::TYPE_MAXI:
			case It6_Models_Ticket::TYPE_SYSTEM:
				$combs = '';
				foreach ($counts as $data) {
					if (!empty($combs))
					$combs .= ',';
					$combs .= $data['subject'];
				}
				return array($this->newError(
				29,
				$this->translate('ticket_er_29'),
				null, null, null, $combs
				));
			default:
				throw new Exception('Unknown coupon type: ' . $this->helper->type);
		}
		if ($maxDuplicit <= $this->helper->checkTicketHashes()) {

			return array($this->newError(
			8,
			$this->translate('ticket_er_8')));
		} else {
			return true;
		}
		return $result;
	}

	public function isValidBet(){

		$betIds = $this->helper->getBetIds();
		if (empty($betIds))
		return true;

		$invalidBets = array();

		$db = Zend_Registry::get('db');
		$rows = $db->select()
		->from(
		array('sazky'),
		array(
				'id' => 'sazka_id',
				'name' => 'text',
				'status' => 'status',
				'validFrom' => 'platna_od',
				'validTo' => 'platna_do',
				'isValidFrom' => new Zend_Db_Expr( $db->quoteInto('(platna_od<=?)', It6_Date::timestampToDb($this->timestamp)) ),
				'isValidTo' => new Zend_Db_Expr( $db->quoteInto('(platna_do>=?)', It6_Date::timestampToDb($this->timestamp + BET_STOP_TIME)) ),
		)
		)
		->where('sazka_id IN (?)', $betIds)
		->query()
		->fetchAll();
		$dbBets = array();
		foreach ($rows as $row)
		$dbBets[$row['id']] = $row;

		foreach($this->helper->bets as $bet){
			$invalid = false;
			$recValue = null;
			$msgParam = null;
			$betId = $bet['id'];
			if (!array_key_exists($betId, $dbBets))
			$invalid = true;
			else {
				$dbBet = $dbBets[$betId];
				if (0 != $dbBet['status']) {
					$invalid = true;
					$msgParam = 'status';
				}
				if (!$dbBet['isValidTo']) {
					$invalid = true;
					$recValue = $dbBet['validTo'];
					$msgParam = 'valid_to';
				}
				else if (!$dbBet['isValidFrom']) {
					$invalid = true;
					$recValue = $dbBet['validFrom'];
					$msgParam = 'valid_from';
				}
			}
			if ($invalid) {
				$invalidBets[] = array($betId, $bet['column'], $recValue, $msgParam);
				//$invalidBetNames[] = $dbBet['name'];
			}
		}
		if (empty($invalidBets))
		return true;

		$errors = array();
		//$langId = Zend_Registry::get('translate')->getCurrentLangId();
		//$names = It6_Models_Translator::translate($invalidBetNames, $langId, $db);
		foreach ($invalidBets as $bet)
		$errors[] = $this->newError(
		7, 'ticket_er_7', $bet[0], $bet[1], $bet[2], $bet[3], null,//$names[ $dbBets[$betId]['name'] ],
		array(array('name' => 'deleteBet', 'bet' => $bet[0], 'column' => $bet[1]))
		);
		return $errors;
	}

	public function numBet() {

		list($check, $count) = $this->helper->checkCounts();

		switch ($check) {
			case It6_Models_Ticket::CHECK_OK:
				return true;
			case It6_Models_Ticket::CHECK_ERROR_NO_BETS:
				return array($this->newError(
				20,
				$this->translate('ticket_er_20')));
			case It6_Models_Ticket::CHECK_ERROR_MAX_BETS:
				return array($this->newError(
				9,
				$this->translate('ticket_er_9')));
			case It6_Models_Ticket::CHECK_ERROR_MAX_GROUPS:
				return array($this->newError(
				9,
				$this->translate('ticket_er_9')));
			case It6_Models_Ticket::CHECK_ERROR_MIN_NON_T:
				return array($this->newError(
				28,
				$this->translate('ticket_er_28'),
				null, null, $count, $count
				));
				//	case It6_Models_Ticket::CHECK_ERROR_MIN_NON_T:
				//		return array($this->newError(
				//				10,
				//				$this->translate('ticket_er_10')));
			default:
				return array($this->newError(
			1,
			$this->translate('ticket_er')));
		}
	}

	public function isFreebet(){
		//TODO
		return true;
	}

	/**
	 * @deprecated replaced by routines in AjaxController and WS
	 * @param unknown_type $adminId
	 */
	public function isApprovalTimeoutElapsed($adminId = 0) {
		try{
			$rows = Zend_Registry::get('db')->select()->from(array('cupon_data'), array('data'))
			->where('status IN (1, 5)')
			->where( 'date<?', It6_Date::timestampToDb($this->timestamp - Constant::get('BOOK_CHECK_TIME_MORE')) ) // parameter moved to WS managed params
			->where('user_id=?', Zend_Registry::get('user_id'))
			->where('admin_id=?', $adminId)
			->query()
			->fetchAll();

			return (sizeof($rows) > 0);
		}
		catch (Exception $e) {
			Models_Exception_Handler::handle($e);
		}

		return false;
	}

	/**
	 * Kontrola jestli se zmenily nejake kurzy.
	 * @return array|FALSE array('bet' => array( betId1 => message, ...), 'script' => JS_to_update_ticket_at_client)
	 */
	public function checkRateChanges() {

		$db = Zend_Registry::get('db');
		$result = $this->helper->checkRateChanges($this->userId, $this->timestamp, $db);
		return $this->convertResult($result);

		/*
		 $changed = array();

		foreach($this->ticket['bet'] as $id => $data){
		try{
		$res = Zend_Registry::get('db')->select()->from(array('s' => 'sazka_pohled'), array('s.kurz'))
		->join(array('u' => 'udalost'), 'u.udalost_id=s.udalost_id')
		->where('s.sazka_id=?', $data['id_bet'])
		->where('s.sloupec_id=?', $data['id_col'])
		->where('s.platny_od=(SELECT k.platny_od FROM sazka_kurz k WHERE k.sazka_id=s.sazka_id ORDER BY k.platny_od DESC LIMIT 1)')
		->query();

		if ( ($dbData = $res->fetch()) && ($daData['kurz'] != $data['rate']) ) {
		$betId = $data['id_bet'];
		$colId = $data['id_col'];
		$changed['bet'][$betId] = Zend_Registry::get('translate')->trans('ticket_er_12', 'TICKET', It6_Translate_Web::DICTIONARY)
		. ' ' . $data['rate'] . ' -> ' . $dbData['kurz'];
		if(!isset($changed['script']))
		$changed['script'] = '';
		$changed['script'] .= "\nrate = " . $dbData['kurz'] . ";\n"
		. "tick.bet[$betId][$colId]['rate'] = parseFloat(rate).toFixed(2);\n"
		. "tick.bet[$betId][$colId]['rate2'] = rate.toString();\n"
		. "tick.UpdateAll();\n";
		}
		}catch (Exception $e) {
		Models_Exception_Handler::handle($e);
		}
		}
		if(count($changed) > 0)
		return $changed;

		return false;
		*/
	}

	/**
	 * Checks if there's same event on one ticket
	 * @return boolean|array TRUE on success or array of errors
	 */
	public function hasNoSameEvent() {
		if (It6_Models_Ticket::TYPE_SIMPLE == $this->helper->type) {
			$ids = array();
			foreach ($this->helper->bets as $bet) {
				$betId = $bet['id'];
				$columnId = $bet['column'];
				if (array_key_exists($betId, $ids)
				&& array_key_exists($columnId, $ids[$betId])) {
					return array($this->newError(16, 'ticket_er_16'));
				}
				else
				$ids[$betId][$columnId] = true;
			}
		}
		else {
			$ids = array();
			foreach ($this->helper->bets as $bet) {
				$betId = $bet['id'];
				if (array_key_exists($betId, $ids))
				return array($this->newError(16, 'ticket_er_16'));
				else
				$ids[$betId] = true;
			}
		}
		return true;
	}

	public function maxWinLimit() {
		$db = Zend_Registry::get('db');
		$dbAdmin = Zend_Registry::get('admindb');
		//$limit = It6_Models_Parameter::getDataByName(It6_Models_Parameter::NAME_TICKET_MAXIMAL_WIN, $dbAdmin);
		//$result = $this->helper->checkMaxWin(floatval($limit), $db);
		/*
		 $acl = Zend_Registry::get('acl');
		$identity = array(
		'user' => $this->userId,
		'admin' => $acl->getIdentity(It6_Acl::IDNAME_ADMIN),
		'host' => $acl->getIdentity(It6_Acl::IDNAME_HOST),
		'branch' => $acl->getIdentity(It6_Acl::IDNAME_BRANCH)
		);
		$limit = Zend_Registry::get('ws')->Parameter->getEffectiveValue(It6_Models_Parameter::NAME_TICKET_MAXIMAL_WIN, $identity);
		*/
		$cfgLimitNames = array(
		It6_Models_Parameter::NAME_TICKET_MAXIMAL_WIN,
		It6_Models_Parameter::NAME_TICKET_MAXIMAL_WIN_MAXIKOMBI,
		);
		$cfgLimits = It6_Models_Parameter::getDataByName($cfgLimitNames, $dbAdmin);
		foreach ($cfgLimitNames as $name) {
			if (!isset($cfgLimits[$name]))
			throw new Exception('Required parameter not found. name="' . $name . '"');
		}
		$limit = Zend_Json::decode($cfgLimits[It6_Models_Parameter::NAME_TICKET_MAXIMAL_WIN]['value']);
		$limitMaxikombi = floatval($cfgLimits[It6_Models_Parameter::NAME_TICKET_MAXIMAL_WIN_MAXIKOMBI]['value']);
		$result = $this->helper->checkMaxWin($limit, $limitMaxikombi, $db);
		return static::convertResult($result);
	}

	public function checkCampaign() {
		$ws = Zend_Registry::get('ws');

		if ( !empty($this->helper->rateAdvance) && floatval($this->helper->rateAdvance) > 0.0 ) {

			$rateAdvance = $ws->Campaign->validate(
			'PreferenceRate',
			'spend',
			array(
				'type'           => $this->helper->type,
				'betCount'       => $this->helper->betCount,
				'stake'          => $this->helper->stake,
				'rate'           => $this->helper->rate,
				'userId'         => $this->userId,
				'preferenceSize' => round($this->helper->rateAdvance*100.0 - 100.0)));
			if ( !$rateAdvance )
			return array($this->newError(25, 'ticket_er_25'));
		}

		$rates = array();
		foreach ( $this->helper->bets as $bet )
			$rates[] = $bet['rate'];
		$points = $ws->Campaign->validate(
			'CreatePointTicket',
			'spend',
			array(
				'type'          => $this->helper->type,
				'pointType'     => $this->helper->pointType,
				'betCount'      => $this->helper->betCount,
				'stake'         => $this->helper->stake,
				'stakeInPoints' => $this->helper->stakeInPoints,
				'rates'          => $rates,
				'userId'        => $this->userId,
				'currencyId'    => $this->helper->currencyId
			)
		);
		
		if ( !$points && $this->helper->isPointTicket() )
			return array($this->newError(26, 'ticket_er_26'));

		return true;
	}

	public function checkColumns() {
		if (It6_Models_Ticket::TYPE_SIMPLE == $this->helper->type)
			return true;
		$db = Zend_Registry::get('db');
		$betSubtypes = array();
		$eventColumns = array();
		foreach ($this->helper->bets as $bet) {
			$betSubtypes[$bet['subtypeId']] = true;
			if (isset($eventColumns[$bet['eventId']][$bet['column']]))
				++$eventColumns[$bet['eventId']][$bet['column']];
			else
				$eventColumns[$bet['eventId']][$bet['column']] = 1;
		}
		$data = It6_Models_BetSubtype::getValidationColumnData(array_keys($betSubtypes), $db);
		$errEvents = array();
		foreach ($eventColumns as $eventId => $columns) {
			$counts = array();
			foreach ($columns as $columnId => $count) {
				foreach ($data as $subtypeId => $columnCounts) {
					if (isset($columnCounts[$columnId])) {
						$counts[] = array_merge(
							array('columnId' => $columnId), $columnCounts[$columnId]
						);
					}
				}
			}
			$c = count($counts);
			for ($i = 0; $i < $c; ++$i) {
				$max = $counts[$i]['maxEventCount'];
				if (empty($max))
					continue;
				$param = $counts[$i]['maxEventCountParam'];
				$countedColumns = array();
				$sum = 0;
				for ($j = 0; $j < $c; ++$j) {
					if ($counts[$j]['maxEventCountParam'] <= $param) {
						$columnId = $counts[$j]['columnId'];
						$countedColumns[] = $columnId;
						$sum += $eventColumns[$eventId][$columnId];
					}
				}
				if ($sum > $max) {
					$errEvents[$eventId] = $countedColumns;
				}
			}
		}
		if (!empty($errEvents)) {
			$errors = array();
			foreach ($errEvents as $eventId => $columns) {
				foreach ($columns as $columnId) {
					foreach ($this->helper->bets as $bet) {
						if ($bet['eventId'] == $eventId && $bet['column'] == $columnId) {
							$errors[] = $this->newError(31, 'ticket_er_31', $bet['id'], $bet['column']);
						}
					}
				}
				break; // just first event is reported
			}
			return $errors;
		}
		else
			return true;
	}

	public static function newError($number, $message, $betId = null, $columnId = null, $recommendedValue = null,
	$messageParam = null, $type = null, $actions = null) {

		$ret = new Entities_TicketValidationError();
		$ret->error = $number;
		$ret->errorMessage = $message;
		$ret->errorMessageParam = $messageParam;
		$ret->field = $betId;
		$ret->fieldSpec = $columnId;
		$ret->recommendedValue = $recommendedValue;
		$ret->type = $type;
		$ret->actions = $actions;
		return $ret;
	}

	protected function translate($message) {
		//return self::$translate->trans($message); // refactor use of trans()
		return $message;
	}

	/**
	 * Merges ticket_er_2 (max stake) errors to one with minimal amount.
	 * @param array $results Results from multiple validation tests
	 * @return TRUE|array true if all was OK or merged array of errors
	 */
	public function mergeMaxStakeErrors(array $results) {
		$errors = array();
		$err2 = false;
		foreach ($results as $result) {
			if (true === $result)
			continue;
			foreach ($result as $err) {
				if (2 == $err['error']) {
					if (false === $err2 || $err2['recommendedValue'] > $err['recommendedValue'])
					$err2 = $err;
				}
				else
				$errors[] = $err;
			}
		}
		if (!empty($err2)) {
			if (0 >= $err2['recommendedValue']) {
				$msgParam = (is_array($err2['errorMessageParam']) && array_key_exists(1, $err2['errorMessageParam']) ? $err2['errorMessageParam'][1] : '');
				$err2 = It6_Models_TicketValidator::newError(
				27, $this->translate('ticket_er_27'), $err2['field'], $err2['fieldSpec'], 0, $msgParam
				);
			}
			$errors[] = $err2;
		}
		return (empty($errors) ? true : $errors);
	}

	protected function convertResult($result) {
		if ( true === $result['result'] ) return true;

		$ret = array();

		if (!empty($result['message'])) {
			$tmp = array();
			if (is_array($result['message'])) {
				$tmp['errorMessage'] = $result['message'][0];
				$tmp['errorMessageParam'] = $result['message'][1];
			}
			else
			$tmp['errorMessage'] = $result['message'];
			$tmp['error'] = substr($tmp['errorMessage'],strrpos($tmp['errorMessage'],'_') + 1);
			$tmp['field'] = null;
			$tmp['fieldSpec'] = null;
			$tmp['recommendedValue'] = (isset($result['recommendedValue']) ? $result['recommendedValue'] : null);
			if (!empty($result['type']))
			$tmp['type'] = $result['type'];
			if (!empty($result['actions']))
			$tmp['actions'] = $result['actions'];
			$ret[] = $tmp;
		}
		if ( !empty($result['bet']) ) {
			foreach ( $result['bet'] as $betId => $data ) {
				foreach ( $data as $colId => $err ) {
					$tmp = array();
					$tmp['field'] = $betId;
					$tmp['fieldSpec'] = $colId;
					if (is_array($err['message'])) {
						$msg = $err['message'][0];
						$tmp['errorMessageParam'] = $err['message'][1];
					}
					else
					$msg = $err['message'];
					$tmp['error'] = substr($msg,strrpos($msg,'_') + 1);
					$tmp['errorMessage'] = $msg;
					$tmp['recommendedValue'] = (isset($err['recommendedValue']) ? $err['recommendedValue'] : null);
					if (!empty($err['type']))
					$tmp['type'] = $err['type'];
					if (!empty($err['actions']))
					$tmp['actions'] = $err['actions'];
					$ret[] = $tmp;
				}
			}
		}

		return $ret;
	}

} // class It6_Models_TicketValidator
