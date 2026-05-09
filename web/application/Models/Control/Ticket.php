<?php

/**
 *
 * Chybova zprava ma tvar
 * array("message"=>globalni chyba,"bet"=>array("bet_id"=>sazka chyba) )
 *
 */

class Models_Control_Ticket{

	/**
	 * data na tiektu
	 * @access public
	 * @var array
	 */
	public static $ticket = array();

	/**
	 * Variable argument count, arguments after $key are treated as message parameters.
	 * @param string $key Key to translate
	 */
	private static function trans($key /* , ... */) {
		if (func_num_args() > 1)
			return I18n::transParams($key, array_slice(func_get_args(), 1), 'TICKET', It6_Translate_Web::DICTIONARY);
		else
			return I18n::trans($key, 'TICKET', It6_Translate_Web::DICTIONARY);
	}

	/**
	 * Vraci TRUE pokud ma uzivatel v DB kupon ve schvalovacce
	 * @param int $log  identifikace prihlaseni
	 * @return bool
	 */

	public static function isProve($log){

		if($log == 2 || $log ==3){

			try{
				$timeout = Constant::get('BOOK_CHECK_TIME_MORE');
				$row = Zend_Registry::get('db')->select()->from(array('coupon_data'),array('data'))
				->where('(status=1 or status=5) and date>=?', It6_Date::dbNow(-$timeout))
				->where('user_id=?', Zend_Registry::get('user_id'))
				->where('admin_id=?', It6_Models_Admin::ID_INTERNET)
				->query()
				->fetchAll();


				if(count($row) > 0) return true;else return false;

			}catch ( Exception $e ) {

				Models_Exception_Handler::handle($e);

			}

		}

		return false;

	}


	/**
	 * Spusti vsechny kontroly na tiketu bez kontroly povolovani jen chyby
	 * @return array,bool
	 */

	public static function allChangeControl(){

		if($e = self::rateChange())   return $e;    //doslo ke zmene kurzu

		return false;
	}




	/**
	 * Spusti vsechny kontroly povolovani tiketu
	 * @return array,bool
	 */
	public static function proveControl(){

		if($e = self::isGoingToProve())     return $e;   //zda je novy a mel by jit do povolovani
		if($e = self::controllState())      return $e;   //kontrola stavu zda ma zacit/pokracovat v povolovacka

		return array('ok'=>true,'script'=>'if(typeof checkProveInterval != "undefined")clearInterval(checkProveInterval);');

	}


	/**
	 * zda jde do povolovani
	 * @return array,bool
	 */

	public static function controllState(){

		$p = array();

		try{
			$userId = Zend_Registry::get('user_id');
			$db = Zend_Registry::get('db');
			$row2 = $db->select()->from(array('coupon_data'),array('status','date'=>'UNIX_TIMESTAMP(date)','data','modified'))
			->where('user_id=?', $userId)
			->where('admin_id=?', It6_Models_Admin::ID_INTERNET)
			->query()->fetchAll();
			if (count($row2) > 0) {
				$modified = (empty($row2[0]['modified']) ? array() : Zend_Json::decode($row2[0]['modified']));
				if($row2[0]['status'] == 1 && $row2[0]['date'] > (time()-(Constant::get('BOOK_CHECK_TIME')+10))  )       {$p["message"] = self::trans('ticket_prove');return $p;}
				if($row2[0]['status'] == 5 && $row2[0]['date'] > (time()-(Constant::get('BOOK_CHECK_TIME_MORE')+5))  )   {$p["message"] = self::trans('ticket_prove');return $p;}
				if($row2[0]['status'] == 3) {

					$p["error"] = self::trans('ticket_notprove');
					$p["script"]  = "tick.DeleteAll();clearInterval(checkProveInterval);";

					$data = array('status'=>0,'date'=> It6_Date::dbNow());
					Zend_Registry::get('db')->update(
						'coupon_data', $data, array('user_id=?' => $userId, 'admin_id=?' => It6_Models_Admin::ID_INTERNET)
					);

					return $p;
				}
				$amount = (empty($modified['stake']) ? 0.0 : floatval($modified['stake']));
				if($row2[0]['status'] == 4 && self::$ticket['totalSum'] > $amount) {

					$data = array('status'=>4,'date'=> It6_Date::dbNow());
					$db->update(
						'coupon_data', $data, array('user_id=?' => Zend_Registry::get('user_id'), 'admin_id=?' => It6_Models_Admin::ID_INTERNET)
					);

					$p["message"] = self::trans('ticket_othersum', $amount .' '. Zend_Registry::get('mena'));
	//				if(self::$ticket['type'] != 'simple')
	//					$p["script"]  = "tick.totalBet=String(".$row2[0]['amount'].");tick.SetTotalSum();tick.ServerUpdate();clearInterval(checkProveInterval);";
	//				else
	//					$p["script"]  = "tick.SetSimpleSum(String(".$row2[0]['amount']."));tick.ServerUpdate();clearInterval(checkProveInterval);";
					$data = Zend_Json::decode($row2[0]['data']);
					$data['userId'] = $userId;
					$helper = new It6_Models_Ticket($data, It6_Models_Ticket::DATA_AJAX, 'user');
					$helper->computeAggregates();
					$json = $helper->recalculatePreapproved($modified);
					$p['script'] = 'tick.PreapprovedTicketUpdate(' . $json . ');';
					return $p;

				}
				if($row2[0]['status'] == 7 && $row2[0]['date'] > (time()-(Constant::get('LIVE_WAIT_TIME_NO_CONFIRM')+1))  )   {$p["message"] = self::trans('ticket_prove_live');return $p;}
			}
		} catch ( Exception $e ) {
			It6_Log::err($e);
			$p["error"] = self::trans('ticket_er_11')." #56";return $p;

		}

		return false;

	}


	/**
	 * zda jsou na tiektu jen live sazky
	 * @return array,bool
	 */

	public static function onlyLive(){

		try{

			$bets = array();$bets['bets'] = array();
			$bets['prove'] = -1;$rate = 1;
			foreach(self::$ticket['bet'] as $k=>$h){

				$row = Zend_Registry::get('db')->select()->from(array('l'=>'live_event'),array('limit_bet'=>'fn_currency_central2user('. Zend_Registry::get('user_id') .',limit_bet)','limit_rate'))
				->join(array('s'=>'live_sazka'),'s.event_id=l.event_id')
				->where('s.sazka_id=?',$h['id_bet'])
				->query()->fetchAll();
				if(count($row) > 0){
					$bets['bets'][] = array('id'=>$h['id_bet'],'limit_bet'=>$row[0]['limit_bet'],'limit_rate'=>$row[0]['limit_rate']);
					if((self::$ticket['totalSum']/count(self::$ticket['bet'])) > $row[0]['limit_bet']) $bets['prove'] = 1;
					if(($h['rate']) > $row[0]['limit_rate']) $bets['prove'] = 1;
				}



			}

			if(count($bets['bets']) == count(self::$ticket['bet'])) $bets['bool'] = 1;else $bets['bool'] = -1;







		} catch ( Exception $e ) {
			It6_Log::err($e);
			$p["error"] = self::trans('ticket_er_11')." #59";return $p;

		}

		return $bets;

	}

	/**
	 * zda jde do povolovani
	 * @return array,bool
	 */

	public static function isGoingToProve(){

		$p = array();

		try{
			$db = Zend_Registry::get('db');
			$userId = Zend_Registry::get('user_id');
			$row = $db->select()
				->from(
					array('nastaveni'),
					array(
						'ticket_limit_high' => 'fn_currency_central2user('.$userId.',ticket_limit_high)',
						'ticket_limit_low' => 'fn_currency_central2user('.$userId.',ticket_limit_low)'
					)
				)
				->query()
				->fetchAll();

			if (0 == count($row))
				return array('error' => self::trans('ticket_er_11') . ' #51');

			$row2 = $db->select()
				->from('coupon_data', array('status'))
				->where('user_id=?', $userId)
				->where('admin_id=?', 0)
				->query()
				->fetchAll();

			if(0 == count($row2))
				return array('error' => self::trans('ticket_er_11') . ' #52');

			$prove = false;

			if(self::$ticket['type'] == 'simple'){
				self::$ticket['totalSum'] = 0;
				foreach(self::$ticket['bet'] as $h)
					self::$ticket['totalSum'] +=  $h['amount'];
			}

			$live = self::onlyLive();
			$prove = false;
			if (0 == $row2[0]['status']) {
				//TODO: refactor use of Models_Helpers_User and user data in Zend_Registry
				if ( It6_Models_User::get($userId, 'watched', $db) )
					$prove = true;
				else {
					$helperCC = clone self::$ticket['helper'];
					$helperCC->convertStakesToCentralCurrency($userId, $db);
					$helperCC->computeAggregates();
					if ($helperCC->checkApprovalGroups($db))
						$prove = true;
				}
				if (!$prove) {
					if	( $live['bool'] == -1 && count(self::$ticket['bet']) == 1
						&& self::$ticket['bet'][0]['amount'] > $row[0]['ticket_limit_low'] )
						$prove = true;
					else if( $live['bool'] == -1 && count(self::$ticket['bet']) > 1
						&& self::$ticket['totalSum'] > $row[0]['ticket_limit_high'] )
						$prove = true;
					else if ( ($live['prove'] == 1 || $live['bool'] == 1) )
						$prove = true;
				}
			}

			if ($prove) {
				$p['script']  = 'tick.confirmationStart()';
				$p['message'] = self::trans( 1 == $live['bool'] ? 'ticket_prove_live' : 'ticket_prove' );

				$data = array( 'date' => It6_Date::dbNow() );
				if ($live['prove'] == 1 || $live['bool'] == -1)
					$data['status'] = 1;
				else
					$data['status'] = 7;

				Zend_Registry::get('db')->update(
					'coupon_data', $data, array('user_id=?' => Zend_Registry::get('user_id'), 'admin_id=?' => 0)
				);
				//TODO: is this intent? no transaction started here, should we end it?
				//Zend_Registry::get('db')->commit();
			}

		} catch ( Exception $e ) {

			return array('error' => self::trans('ticket_er_11') . ' #54');

		}

		if (count($p) > 0)
			return $p;
		else
			return false;
	}

	/**
	 * Spusti vsechny kontroly na tiketu bez kontroly povolovani jen chyby
	 * @return array,bool
	 */

	public static function allErrorControl(){

		if($e = self::isLog())               return $e;    //je prihlaseny?
		else if($e = self::isFreebet())      return $e;    //Kontrola freebetu
		else if($e = self::numBet())         return $e;    //Pocet sazek systemu a ostatnich a pocet bankeru
		else if($e = self::isValidBet())     return $e;    //Nejsou na tiketu neplatne sazky
		else if($e = self::sameTicket())     return $e;    // kontrola stejneho tiketu za 24 hod
		else if($e = self::isIndvLimit())    return $e;    //individualni limit na sazku
		else if($e = self::limitDay())       return $e;    //limit na den
		else if($e = self::isProveTicket())  return $e;    //kontrola zda mohou byt kombinovane jen  v pripade ne simple tiketu a zda je pritoma castka na simple tiketu
		else if($e = self::userHasMoney())   return $e;    //uzivatel ma dostatečný zůstatek
		else if($e = self::riskLimit())      return $e;    // kontrola risk limitu
		else if($e = self::isAko())          return $e;    // kontrola AKO
		else if($e = self::isNotSame())      return $e;    // kontrola zda na tiketu neni stejna sazka id

		return false;
	}

	/**
	 * kontrola zda na tiketu neni stejna sazka id
	 * @return array,bool
	 */
	public static function isNotSame(){

		$dd = array();
		$status = true;
		foreach(self::$ticket['bet'] as $k => $h){
			if (in_array($h['id_bet'], $dd))
				$status= false;
			$dd[] = $h['id_bet'];
		}

		if (!$status) 
			return array('message' => self::trans('ticket_er_16'));
		else
			return false;
	}


	/**
	 * Kontrola freebetu
	 * @return array,bool
	 */
	public static function isFreebet(){

		$p = array();

		if (!empty(self::$ticket['bet_code'])) {

			$row = Zend_Registry::get('db')->select()
				->from('ticket_bonus_uzivatel', array('kod', 'bonus_castka'))
				->where('user_id=?', Zend_Registry::get('user_id'))
				->where('kod=?', self::$ticket['bet_code'])
				->where('vybral=0')
				->query()
				->fetchAll();

			if (0 == count($row))
				return array('message' => self::trans('ticket_er_15'));
			else if ( (self::$ticket['type'] == 'simple' && count(self::$ticket['bet']) > 1)
				|| self::$ticket['type'] == 'system' || self::$ticket['type'] == 'maxikombi' )
				return array('message' => self::trans('ticket_er_14'));

			if(self::$ticket['type'] == 'simple')
				self::$ticket['bet'][0]['amount'] = $row[0]['bonus_castka'];
			else // prakticky jen AKO
				self::$ticket['totalSum'] = $row[0]['bonus_castka'];

		}

		return false;
	}

	/**
	 * AKO
	 * @return array,bool
	 */

	public static function isAko(){

		$helper = &self::$ticket['helper'];
		$result = $helper->checkBetsAko();
		if (!$result['result']) {
			$helper->translateCheckResult($result);
			return $result;
		}
		return false;

/*
		$p = array();

		foreach (self::$ticket['bet'] as $k => $h) {
			if (self::$ticket['type'] == 'simple' && $h['ako'] > 0)
				$p['bet'][$h['id_bet']] = self::trans('ticket_er_13', $h['ako']);
			else if ( self::$ticket['type'] != 'simple' && ($h['ako'] > count(self::$ticket['bet']) - 1) ) 
				$p['bet'][$h['id_bet']] = self::trans('ticket_er_13', $h['ako']);
		}

		if (count($p) > 0) {
			$p['message'] = self::trans('ticket_er');
			return $p;
		}

		return false;
*/
	}

	/**
	 * Pocet sazek systemu a ostatnich a pocet bankeru
	 * @return array,bool
	 */

	public static function rateChange(){

		$p = array();

		foreach (self::$ticket['bet'] as $k => $h){

			try{

				$row = Zend_Registry::get('db')->select()->from(array('sz' => 'sazka_pohled'), array('sz.kurz'))
					->join(array('s'=>'udalost'), 's.udalost_id=sz.udalost_id', array())
					->where('sz.sazka_id=?', $h['id_bet'])
					->where('sz.sloupec_id=?', $h['id_col'])
					->where('sz.platny_od=(SELECT d.platny_od FROM sazka_kurz d WHERE d.sazka_id=sz.sazka_id ORDER BY d.platny_od DESC LIMIT 1)')
					->query()
					->fetchAll();

				if (count($row) > 0 && $row[0]['kurz'] != $h['rate']) {
					$kurz = $row[0]['kurz'];
					$p["bet"][$h['id_bet']] = self::trans('ticket_er_12', $h['rate'] . ' -> ' . $kurz);

					if (!isset($p['script']))
						$p['script'] = '';

					$p['script'] .= 'rate = '. $row[0]['kurz'] . ';'
						. 'tick.bet['. $h['id_bet'] .']['. $h['id_col'] .']["rate"] = parseFloat(rate).toFixed(2);'
						. 'tick.bet['. $h['id_bet'] .']['. $h['id_col'] .']["rate2"] = rate.toString();tick.UpdateAll();';
				}
			}catch ( Exception $e ) {

				Models_Exception_Handler::handle($e);

			}

		}

		if(count($p) > 0)
			return $p;
		else
			return false;
	}

	/**
	 * Denni limity na sport a sazky
	 * @return array,bool
	 */

	public static function limitDay() {

		$p = array();
		$bets = array();
		$sports = array();

		foreach(self::$ticket['bet'] as $h) {

			if(self::$ticket['type'] == 'simple') $bets = array();

			try {

				$row = Zend_Registry::get('db')->select()
					->from(array('s' => 'sazka_pohled'), array('s.kurz'))
					->join(array('u' => 'udalost'), 'u.udalost_id=s.udalost_id', array('u.sport_id'))
					->where('s.sazka_id=?', $h['id_bet'])
					->where('s.sloupec_id=?', $h['id_col'])
					->where('s.platny_od=(SELECT k.platny_od FROM sazka_kurz k WHERE k.sazka_id=s.sazka_id ORDER BY k.platny_od DESC LIMIT 1)')
					->query()
					->fetchAll();

				if (count($row) == 0)
					return array('message' => self::trans('ticket_er_11'));

				if (!isset($bets[$row[0]['sport_id']]['pocet']))
					$bets[$row[0]['sport_id']]['pocet'] = 1;
				else
					++$bets[$row[0]['sport_id']]['pocet'];
				$sports[$h['id_bet']] = $row[0]['sport_id'];
			} catch ( Exception $e ) {

				Models_Exception_Handler::handle($e);

			}
/*
			// simple mohu odbavit hned
			if(self::$ticket['type'] == 'simple') {
				foreach ($bets as $sportId => $h2) {
					if (isset($h2['limit']) && $h2['limit'] < $h['amount']) {
						$p['bet'][$h['id_bet']] = self::trans('ticket_er_2', // refactor use of trans()
							$h2['limit'] . (Zend_Registry::isRegistered('mena') ? ' ' .Zend_Registry::get('mena') : ''));
					}
				}
			}
*/
		}

		$userId = Zend_Registry::get('user_id');
		foreach ($bets as $sportId => $h) {

			$row = Zend_Registry::get('db')->select()
				->from(
					array('b'=>'limity'),
					array(
						'vycerpal' => new Zend_Db_Expr('fn_currency_central2user('. $userId .',a.vycerpal)'),
						'limit_den_individual' => new Zend_Db_Expr('fn_currency_central2user('. $userId . ',a.limit_den_individual)'),
						'limit_den' => new Zend_Db_Expr('fn_currency_central2user(' . $userId . ',b.limit_den)')
					)
				)
				->joinLeft(array('a'=>'limity_user'), 'a.sport_id=b.sport_id', array())
				->where('b.sport_id=?', $sportId)
				->orWhere('a.user_id=' . $userId . ' AND a.sport_id='. $sportId)
				->query()
				->fetchAll();

			if (count($row) == 0)
				continue;
			else if (is_numeric($row[0]['limit_den_individual']))
				$bets[$sportId]['limit']  =  $row[0]['limit_den_individual']  - $row[0]['vycerpal'];
			else if (is_numeric($row[0]['limit_den']))
				$bets[$sportId]['limit']  =  $row[0]['limit_den']  - intval($row[0]['vycerpal']);
		}

		//TODO: check if all tickets have 'helper' instantiated, if so, you can check all types in same way
/*		if (self::$ticket['type'] == 'simple' && count($p) > 0) {
			$p['message'] = self::trans('ticket_er');
			return $p;
		}
		else if (self::$ticket['type'] == 'kombi') {

			if(!isset(self::$ticket['totalSum']) || !is_numeric(self::$ticket['totalSum'])) {
				$p['message'] = self::trans('ticket_er_11');
				return $p;
			}
			$oneBet = self::$ticket['totalSum'] / count(self::$ticket['bet']);

			$limit = false;
			$limit2 = 0;

			foreach ($bets as $sportId => $h2) {
				if (isset($h2['limit']) && ($h2['limit'] < $oneBet * $h2['pocet']) && (false === $limit || $limit > $h2['limit'])) {
					$limit = $h2['limit'];
					$limit2 = ($h2['limit'] / $h2['pocet']) * count(self::$ticket['bet']);
				}
			}

			if (false !== $limit) {
				// refactor use of trans()
				$p['message'] = self::trans('ticket_er_2', $limit2  . (Zend_Registry::isRegistered('mena') ? ' ' . Zend_Registry::get('mena') : ''));
				return $p;
			}

		}
		else if (self::$ticket['type'] == 'system' || self::$ticket['type'] == 'maxikombi') {
*/			$sportRiscAmount = array();
			foreach (self::$ticket['helper']->bets as $bet) {
				$sportId = $sports[$bet['id']];
				if (!isset($sportRiscAmount[$sportId]))
					$sportRiscAmount[$sportId] = $bet['riskAmount'];
				else
					$sportRiscAmount[$sportId] += $bet['riskAmount'];
			}
			$limit = false;
			$limit2 = 0;
			foreach ($bets as $sportId => $h) {
				$amount = (isset($sportRiscAmount[$sportId]) ? $sportRiscAmount[$sportId] : 0);
				if (isset($h['limit']) && ($amount > $h['limit']) && (false === $limit || $limit > $h['limit'])) {
					$limit = $h['limit'];
					$limit2 = ($h['limit'] / $h['pocet']) * count(self::$ticket['bet']);
					if ('simple' == self::$ticket['type']) {
						if (!isset($currency)) {
							$currency = Zend_Registry::isRegistered('mena');
							$currency = ($currency ? ' ' . $currency : '');
						}
						$p['bet'][$h['id_bet']] = self::trans('ticket_er_2', $h['limit'] . $currency);
					}
				}
			}
			if (false !== $limit) {
				if (!isset($currency)) {
					$currency = Zend_Registry::get('mena');
					$currency = ($currency ? ' ' . $currency : '');
				}
				if ('simple' == self::$ticket['type'])
					$p['message'] = self::trans('ticket_er');
				else
					$p['message'] = self::trans('ticket_er_2', $limit2 . $currency);
				return $p;
			}
//		}

		return false;
	}

	/**
	 * Pocet sazek systemu a ostatnich a pocet bankeru
	 * @return array,bool
	 */

	public static function numBet() {

		$helper = &self::$ticket['helper'];
		list($check, $count) = $helper->checkCounts();

		switch ($check) {
		case It6_Models_Ticket::CHECK_OK:
			return false;
		case It6_Models_Ticket::CHECK_ERROR_NO_BETS:
			$msg = 'ticket_er_11';
			break;
		case It6_Models_Ticket::CHECK_ERROR_MAX_BETS:
			$msg = 'ticket_er_9';
			break;
		case It6_Models_Ticket::CHECK_ERROR_MAX_GROUPS:
			$msg = 'ticket_er_9'; //TODO: new resource
			break;
		case It6_Models_Ticket::CHECK_ERROR_MIN_NON_T:
			$msg = 'ticket_er_10';
			break;
		default:
			$msg = 'ticket_er';
			break;
		}
		return array( 'message' => (false !== $count ? self::trans($msg, $count) : self::trans($msg)) );
	}


	/**
	 * kontrola stejneho tiketu za 24 hod
	 * @return array,bool
	 */

	public static function sameTicket(){

/*
		$p = array();

		try{

			$row = Zend_Registry::get('db')->select()
				->from(array('ticket_pohled'), array('ticket_id', 'sazka_id'))
				->where('zalozen>DATE_SUB(NOW(),INTERVAL 1 DAY)')
				->where('user_id=?', Zend_Registry::get('user_id'))
				->order('sazka_id')
				->query()->fetchAll();

			//TODO: use webservice, use user param
			$row2 = Zend_Registry::get('db')->select()->from(array('t_cfg'),array('s_hodnota'))
				->where('s_nazev=?', 'SAME_TICKET_LIMIT')
				->query()->fetchAll();

			$sameNum = $row2[0]['s_hodnota'];

		}catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}

		$ticket = array();
		foreach($row as $h)
			$ticket[$h['ticket_id']][] = $h['sazka_id'];
		unset($row);

		if ('simple' == self::$ticket['type']) {

			$p = array();

			foreach (self::$ticket['bet'] as $h) {
				$n = 0;
				foreach ($ticket as $s) {
					if (count($s) == 1 && in_array($h['id_bet'], $s))
						++$n;
				}
				if ($n > $sameNum) {
					$p['bet'][$h['id_bet']] = self::trans('ticket_er_8');
					break;
				}
			}

			if (count($p) > 0) {
				$p['message'] = self::trans('ticket_er');
				return $p;
			}
		}
		else if ('kombi' == self::$ticket['type']) {

			$ticket_c = array();
			foreach (self::$ticket['bet'] as $h)
				$ticket_c[] = $h['id_bet'];

			$n = 0;
			foreach ($ticket as $s) {
				if(count($ticket_c) == count($s) && count(array_diff($ticket_c, $s)) == 0)
					++$n;
			}

			if ($n > $sameNum)
				$p['message'] = self::trans('ticket_er_8');
		}
		else if ('maxikombi' == self::$ticket['type'] || 'system' == self::$ticket['type']) {
			//TODO: implement
			
		}
		elseif(self::$ticket['type'] == 'system'){
			//TODO: make part of maxic

			$ticket_c = $banker = array();

			foreach(self::$ticket['bet'] as $h){

				if($h['banker'] == 1) $banker[] = $h['id_bet'];
				else  $ticket_c[]= $h['id_bet'];

			}

			//TODO: reimplement using It6_Models_Ticket
			$row = Zend_Registry::get('db')->select()->from(array('system_user_bets'),array('bet'))
				->where('datum>DATE_SUB(NOW(),INTERVAL 1 DAY)')
				->where('user_id=?',Zend_Registry::get('user_id'))
				->query()->fetchAll();


			$ticket = array();
			foreach($row as $h){

				$ticket[] = explode(';',$h['bet']);

			}

			foreach(self::$ticket['combinations'] as $k => $h){
				//TODO: reimplement using It6_Models_Ticket
				$sysComb = Models_Helpers_Help::CombSystem($ticket_c,$banker,$k);

				$x = 0;
				foreach($ticket as $s){

					foreach($sysComb as $h2){

						if(count($h2) == count($s) && count(array_diff($h2,$s)) == 0) {$x++;}

					}

				}

				if($x >$sameNum) $p["message"] = self::trans('ticket_er_8');

			}

		}
*/
		try{
			//TODO: use webservice, use user param
			//TODO: move to It6_Models_Ticket::checkTicketHashes()
			throw new Exception('Deprecated SAME_LIMIT constant');
			$row = Zend_Registry::get('db')->select()
				->from(array('t_cfg'), array('s_hodnota'))
				->where('s_nazev=?', 'SAME_TICKET_LIMIT')
				->query()
				->fetch();

			$maxDuplicit = $row['s_hodnota'];
		}
		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}

		$helper = &self::$ticket['helper'];
		if ($maxDuplicit < $helper->checkTicketHashes())
			return array('message' => self::trans('ticket_er_8'));
		else
			return false;
	}


	/**
	 * Nejsou na tiketu neplatne sazky
	 * @return array,bool
	 */

	public static function isValidBet(){

		$p = array();

		foreach(self::$ticket['bet'] as $h){

			try{

				$rows = Zend_Registry::get('db')->select()->from(array('sazky'),array('sazka_id'))
					->where('platna_od<=NOW()')
					->where('platna_do>=NOW()' )
					->where('status=?',0)
					->where('sazka_id=?', $h['id_bet'])
					->query()
					->fetchAll();

			}catch ( Exception $e ) {

				Models_Exception_Handler::handle($e);

			}

			if(count($rows) == 0){
				$p["bet"][$h['id_bet']] = self::trans('ticket_er_7');
				if(!isset($p["script"])) $p["script"] = '';
				$p["script"] .= 'tick.SetBanker('. $h['id_bet'] .','. $h['id_col'] .',0);tick.ChangeBet('. $h['id_bet'] .','. $h['id_col'] .');';
			}
		}

		if (count($p) > 0) {
			$p['message'] = self::trans('ticket_er');
			return $p;
		}

		return false;
	}


	/**
	 * kontrola risk limitu
	 * @return array,bool
	 */

	public static function riskLimit(){

		$userId = Zend_Registry::get('user_id');
		$p = array();
		$helper = &self::$ticket['helper'];
		$maxFinal = null;
		foreach ($helper->bets as $bet) {
			$amount = $bet['riskAmount'];
			$max = $max2 = null;
			try {
				$row = Zend_Registry::get('db')->select()
					->from(array('sazky'), array(
						'risk_limit' => 'fn_currency_central2user(' . $userId . ',risk_limit)',
						'risk_limit_balance' => 'fn_currency_central2user(' . $userId . ',risk_limit_balance)',
						'risk_limit_prct' => 'fn_currency_central2user( ' . $userId . ',(risk_limit*(fn_get_const(\'RISK_LIMIT_PCT\')/100)) )'
						)
					)
					->where('sazka_id=?', $bet['id'])
					->query()
					->fetch();
			} catch ( Exception $e ) {
				Models_Exception_Handler::handle($e);
			}

			if ($amount > $row['risk_limit_prct'])
				$max = $row['risk_limit_prct'];
			if ($amount + $row['risk_limit_balance'] > $row['risk_limit'])
				$max2 = abs($row['risk_limit'] - $row['risk_limit_balance']);

			$max = (($max > $max2 && $max2 != null) || $max == null ? $max2 : $max);

			if ($max != null && 'simple' == $helper->type) {
				if (!isset($currency)) {
					$currency = Zend_Registry::get('mena');
					$currency = ($currency ? ' ' . $currency : '');
				}
				$p['bet'][$bet['id']] = self::trans('ticket_er_2', $max . $currency);
			}


			if ($max != null && ($maxFinal == null || $max < $maxFinal) ) {
				$maxFinal = $max;
				$finalBetId = $bet['id'];
				$finalColumnId = $bet['column'];
			}

		}

		if($maxFinal != null) {
			if (!isset($currency)) {
				$currency = Zend_Registry::get('mena');
				$currency = ($currency ? ' ' . $currency : '');
			}
			$newStakes = $helper->computeStakesFromChangedBetRiscAmount($finalBetId, $finalColumnId, $maxFinal);
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
			if (It6_Models_Ticket::TYPE_MAXI == $helper->type)
				$msg = '(' . It6_Models_Bet::readBetText($finalBetId) . ') ' . $maxFinal . $currency;
			else
				$msg = $newStakes['stake'] . ' ' . $currency;
			
			if ('simple' == self::$ticket['type'])
				$p['message'] = self::trans('ticket_er');
			else
				$p['message'] = self::trans('ticket_er_2', $msg);

			//$p['message'] = self::$translate->trans('simple' == self::$ticket['type'] ? 'ticket_er' : 'ticket_er_2' . ' ' . $maxFinal . $currency);
			return $p;
		}

		return false;
	}

	/**
	 * uzivatel ma dostatečný zůstatek
	 * @return array,bool
	 */

	public static function userHasMoney() {
		$stake = self::$ticket['helper']->stake;
		if (0 == $stake || $stake > Zend_Registry::get('zustatek'))
			return array('message' => self::trans('ticket_er_6'));
		else
			return false;
	}

	/**
	 * kontrola zda mohou byt kombinovane jen  v pripade ne simple tiketu a zda je pritoma castka na simple tiketu
	 * @return array,bool
	 */
	//TODO: test
	public static function isProveTicket(){
		$helper = &self::$ticket['helper'];
		$result = $helper->checkStakes();
		if (!$result['result']) {
			$helper->translateCheckResult($result);
			return $result;
		}
		$result = $helper->checkCorrelatedBets();
		if (!$result['result']) {
			$helper->translateCheckResult($result);
			return $result;
		}
		return false;
	}

	/**
	 * Kontrola individualniho limitu
	 * @return array,bool
	 */
	public static function isIndvLimit(){
		$helper = &self::$ticket['helper'];
		$result = $helper->checkUserLimit(Zend_Registry::get('user_id'));
		if (!$result['result']) {
			$helper->translateCheckResult($result);
			return $result;
		}
		return false;
	}


	/**
	 * Kontrola zda je uzivatel prihlaseny
	 * @return array,bool
	 */

	public static function isLog(){
		if($GLOBALS['ses_status'] != 2 && $GLOBALS['ses_status'] != 3 && !isset($_POST['webservicepass']))
			return array("message"=>self::trans('ticket_er_1'));

		return false;
	}

}
