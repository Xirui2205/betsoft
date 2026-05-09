<?php

/**
 * @deprecated
 */
class Models_Ajax_Create {


	/**
	 * pole stavu
	 * @access public
	 * @var array
	 */
	public static $message = array();


	/**
	 * data na tiketu
	 * @access public
	 * @var array
	 */
	public static $ticket = array();

	/**
	 * timestamp
	 * @access private
	 * @var int
	 */
	private static $time;

	/**
	 * id vlozeneho tiektu
	 * @access private
	 * @var int
	 */
	private static $ticket_id;

	/**
	 * Variable argument count, arguments after $key are treated as message parameters.
	 * @param string $key Key to translate
	 */
	private static function trans($key /* , ... */) {
		if (func_num_args() > 1)
			return I18n::transParams($key, array_slice(func_get_args(), 1), 'TICKET', It6_Translate_Web::DICTIONARY);
		else
			return self::trans($key, 'TICKET', It6_Translate_Web::DICTIONARY);
	}

	/**
	 * Vytvari tiket
	 * @param array $ticket data na tiketu
	 * @return array,bool
	 */

	
	public static function ticket($ticket) {
		
		try {
			$ws = Zend_Registry::get('ws');

			$couponId = $ticket['couponId'];
			if ( empty($couponId) )
				throw new Exception("Missing couponId.");

			//$ticketId = $ws->Ticket->acceptAutorisedTicket($couponId);
			
			return $ticketId;

		}
		catch (Exception $e) {
			It6_Log::err($e);
			self::$message['error'][] = self::trans('ticket_er_11') .' #1';
		}

		/*
		 
		self::$ticket = $ticket;
		 
		self::$time = time();
		 
		if ( self::$ticket['type'] == 'simple') {
			self::createSimpleTicket();	
		}
		else if ( self::$ticket['type'] == 'kombi') { 
			self::createCombiTicket(); 
		}
		else if ( self::$ticket['type'] == 'maxikombi') { 
			self::createMaxicombiTicket(); 
		}
		else if ( self::$ticket['type'] == 'system') {
			self::createMaxicombiTicket();
		}
		else {
			It6_Log::err($e);
			self::$message['error'][] = self::trans('ticket_er_11') .' #1';	
		}
		 
		 
		self::checkSumUserBalance(); // TODO koncna kontrola zda neni ucet v minusu TODO
		 
		if ( isset( self::$message['error'] ) );
		else {
			self::clearCuponData();
			//self::$message['error'][] = 1;
			if ( !isset(self::$message['error'])) {
				
			}
		}
		 		 
		if ( isset( self::$message['error'] ) ) {
			//NOTE: rollback should make block of code that started transaction
			//Zend_Registry::get('db')->rollback();	
		}

		return self::$ticket_id;
		*/
	}

	/**
	 * vycisti tabulku coupon_data
	 * @return void
	 */

	private static function clearCuponData() {
		 
		try {			 
			Zend_Registry::get('db')
				->delete('coupon_data', array('user_id=?' => Zend_Registry::get('user_id'), 'admin_id=?' => It6_Models_Admin::ID_INTERNET) );
				
		} catch ( Exception $e ) {
			It6_Log::err($e);
			self::$message['error'][] = self::trans('ticket_er_11') . ' #21';
		}		 
	}



	/**
	 * konecna kontrola zda neni ucet v minusu
	 * @return void
	 */

	public static function checkSumUserBalance() {
		 
		try {
			$row2 = Zend_Registry::get('db')
				->select()
				->from( array('a'=>'uzivatel_im_data'), array('zustatek') )
				->where( 'user_id=?', Zend_Registry::get('user_id') )
				->query()
				->fetchAll();
			 
			if ( count($row2) == 0 || $row2[0]['zustatek'] < 0 ) {
				self::$message['error'][] = self::trans('ticket_er_11') . ' #2';
				return;
			}

		} catch ( Exception $e ) {
			It6_Log::err($e);
			self::$message['error'][] = self::trans('ticket_er_11') . ' #20';
		}
		 
	}

	/**
	 * Vytvari tiket do DB
	 * @param array $ticket data na tiketu
	 * @param string $freeBet free bet code
	 * @param string $totalSum castka na tiket
	 * @param string $type typ tiketu
	 * @param int $system jaky system
	 * @param int $rate jaky system
	 * @param int $mail poslat vysledek mailem
	 * @return array,bool
	 */
	 
	public static function createTicket($ticket, $freeBet = null, $totalSum, $type = 'simple', $system = 0, $rate = 0, $mail = 1) {

		//TODO Sloucit amount a ticketSave
		if ( !isset( self::$message['error'] ) ) {
			$betAmount = self::amount( $totalSum, $freeBet ); // kontrola zda free bet a pak aktualizace freebet nebo aktualizace zustatku	
		}
		 
		if ( !isset( self::$message['error'] ) ) {
			self::ticketSave($ticket, $betAmount, $freeBet, $type, $system, $rate, $mail);// vlozit do ticket a tiket kurz
		}
		
		if ( !isset( self::$message['error'] ) ) {
			self::risk($ticket,$betAmount); // aktualizace risk limitu	
		}
		 
		if ( !isset( self::$message['error'] ) ) { 
			self::limit($ticket,$betAmount);// aktualizace sazkoveho limitu
		}

	}

	/**
	 * aktualizace sazkoveho limitu ... nacita uzivateli se pro kazdy sport zvlast
	 * drive : limit[sport] += pocet_sazek[sport] * (vklad_tiket / pocet_sazek)
	 * nyni: konkretni riskAmount pro kazdou sazku
	 * @param array $ticket data na tiketu
	 * @param int $betAmount castka na tiket
	 * @return bool
	 */
	//TODO: test
	public static function limit($ticket, $betAmount) {
		throw new Exception('This method needs refactoring');
		try {
			$db = Zend_Registry::get('db');
			$userId = Zend_Registry::get('user_id');
			$helper = &self::$ticket['helper'];
			$helperCC = clone $helper; // central currency
			$helperCC->convertStakesToCentralCurrency($userId);
			$helperCC->computeAggregates();
			//NOTE: refactor getSportRiscAmounts use
			$sportRiscAmounts = $helperCC->getSportRiscAmounts();
			foreach ($sportRiscAmounts as $sportId => $amount) {
				$rows = $db->select()
					->from('limity_user', array('vycerpal'))
					->where('user_id=?', $userId)
					->where('sport_id=?', $sportId)
					->query()
					->fetchAll();
				if (empty($rows))
					$db->insert('limity_user', array('vycerpal' => $amount, 'sport_id' => $sportId, 'user_id' => $userId));
				else
					$db->update(
						'limity_user',
						array('vycerpal' => '(vycerpal+' . $db->quote($amount, Zend_Db::FLOAT_TYPE) . ')'),
						array('sport_id=?' => $sportId, 'user_id=?' => $userId)
					);
			}

/*
			foreach ( $sport as $k => $h ) {

				$row2 = Zend_Registry::get('db')
					->select()
					->from( array('a'=>'limity_user'), array('vycerpal') )
					->where( 'user_id=?', Zend_Registry::get('user_id') )
					->where( 'sport_id=?', $k )
					->query()
					->fetchAll();

				if ( count($row2) == 0 ) {
					 
					$data = array();
					$data['vycerpal'] = $h;
					$data['sport_id'] = $k;
					$data['user_id'] = Zend_Registry::get('user_id');
					 
					Zend_Registry::get('db')
						->insert('limity_user',$data);					 
				} 
				else {
					 					 
					Zend_Registry::get('db')
						->query('update limity_user set vycerpal=vycerpal+'
									. $h
									.' where user_id='
									. Zend_Registry::get('user_id')
									.' and sport_id='
									. intval($k) );
				}
			}
*/
		} catch ( Exception $e ) {
			It6_Log::err($e);
			self::$message['error'][] = self::trans('ticket_er_11') .' #5';
		}
	}


	/**
	 * aktualizace risk limitu
	 * @param array $ticket data na tiketu
	 * @param int $betAmount castka na tiket
	 * @return bool
	 */
	//TODO: test
	public static function risk($ticket, $betAmount) {
		try {
			if (isset(self::$ticket['helper'])) {
				$helper = &self::$ticket['helper'];
				$helperCC = clone $helper; // central currency
				$helperCC->convertStakesToCentralCurrency(Zend_Registry::get('user_id'));
				$helperCC->computeAggregates();
//echo '<pre>'.print_r($helper,true).'</pre>';
//echo '<pre>'.print_r($helperCC,true).'</pre>';
				$helperCC->saveBetsRiskLimit(false);
			}
			else { //TODO: should be unnecessary
				throw new Exception('This code should not invoked');
				$am = $betAmount / count($ticket);
				$db =  Zend_Registry::get('db');
				$row = $db->query('select fn_currency_user2central(?,?) AS castka', array(Zend_Registry::get('user_id'), $am))
					->fetchAll();

				if ( count($row) == 0 ) {
					self::$message['error'][] = self::trans('ticket_er_11') . ' #6';
					return;
				}
				else
					$castka = $row[0]['castka'];

				foreach ($ticket as $h) {
					$db->update(
						'sazky',
						array('risk_limit_balance' => '(risk_limit_balance+' . $db->quote($castka, Zend_Db::FLOAT_TYPE) . ')'),
						'sazka_id=' . intval($h['id_bet'])
					);
				}
			}
		} catch ( Exception $e ) {
			It6_Log::err($e);
			self::$message['error'][] = self::trans('ticket_er_11') .' #7';
		}
	}

	/**
	 * vlozit do ticket a tiket kurz
	 * @param array $ticket data na tiketu
	 * @param int $betAmount castka na tiket
	 * @param string $freeBet free bet code
	 * @param string $type typ tiketu
	 * @param int $system jaky system
	 * @param int $rate jaky system
	 * @param int $mail poslat vysledek mailem
	 * @return bool
	 */

	public static function ticketSave($ticket,$betAmount,$freeBet,$type,$system,$rate,$mail) {

		try {
			$couponId = (empty(self::$ticket['couponId']) ? 0 : self::$ticket['couponId']);
			
			$helper = &self::$ticket['helper'];

			$data = array();
			$data['user_id'] = Zend_Registry::get('user_id');
			$data['castka'] = $betAmount;
			$data['zalozen'] = It6_Date::dbNow();
			$data['free_bet_bonus'] = ($freeBet == null?0:1);
			$data['system'] = $system;
			$data['type'] = $type;
			$data['win'] = ($type == 'simple' || $type == 'kombi' ? round($rate * $betAmount, 2) : 0);
			$data['rate'] = round($rate,2);
			$data['mail'] =  ($mail == 1?1:0);
			$data['tickethash'] = $helper->totalHash;
			$data['host_id'] = It6_Models_Branch::ID_HOST_INTERNET;
			$data['cash'] = 0;
			$data['admin_id'] = It6_Models_Admin::ID_INTERNET;
			$data['coupon_id'] = $couponId;
			if ('system' == $type || 'maxikombi' == $type) {
				$data['win'] = $helper->win;
				$data['group_count'] = $helper->getGroupCount(true);
				$data['group_t'] = ($helper->hasGroupT() ? 1 : 0);
			}

			$db = Zend_Registry::get('db');
			$db->insert('ticket', $data);

			$id = $db->lastInsertId();
			$handle = It6_NineDigitHandle::makeHandle($id, $db);
			$db->update('ticket', array('handle' => "$handle"), array('ticket_id=?' => $id));

			self::$ticket_id =  $id;
			$helper->id = $id;

			foreach ( $ticket as $h) {

				$data = array();
				$data['ticket_id'] = $id ;
				$data['sazka_id'] = $h['id_bet'];
				$data['sloupec_id'] = $h['id_col'];
				$data['banker'] = ($type == 'system' && $h['banker'] == 1?1:0);
				if ('system' == $type || 'maxikombi' == $type)
					$data['group_id'] = $h['group'];

				Zend_Registry::get('db')->insert('ticket_kurz',$data);
			}

			if ('system' == $type || 'maxikombi' == $type) {
				$helper->saveCombinations();
			}
			$helper->saveTicketHashes(false);
			
			It6_Log::info(
				'Ticket created',
				It6_Log::TAG_USER_OPERATION,
				array(
					'userId' => Zend_Registry::get('user_id'),
					'ticketId' => self::$ticket_id,
					'ticket' => serialize( self::$ticket)));

			self::$message['ok'] = 1;

		} catch ( Exception $e ) {
			It6_Log::err($e);
			self::$message['error'][] = self::trans('ticket_er_11') .' #8';
		}
		 

	}
	 
	 
	/**
	 *  kombinace systemu
	 * @param int $system typ systemu
	 * @return bool
	 */
/*
	public static function systemCombinations($system) {

		$banker = $bet = array();
		foreach ( self::$ticket['bet'] as $k=>$h) {

			if ( $h['banker'] == 1) $banker[] = $h['id_bet'];
			else $bet[] = $h['id_bet'];

		}
		 
		$p = Models_Helpers_Help::CombSystem($bet,$banker,$system);
		 
		foreach ( $p as $hh) {

			$data = array();
			$data['user_id'] = Zend_Registry::get('user_id');
			$data['datum']   = date('Y-m-d H:i:s',self::$time);
			$data['bet']     = implode(';',$hh);

			try {

				Zend_Registry::get('db')->insert('system_user_bets',$data);
				 
			} catch ( Exception $e ) {

				self::$message['error'][] = self::trans('ticket_er_11') .' #31';
				 
			}
			 
		}

	}
*/
	/**
	 *  kontrola zda free bet a pak aktualizace freebet nebo aktualizace zustatku
	 * @param string $freeBet free bet codezky
	 * @param int $totalSum castka na tiket
	 * @return bool
	 * @deprecated
	 */

	public static function amount($totalSum,$freeBet) {

		try {

			if ( $freeBet != null) {

				$row = Zend_Registry::get('db')->select()->from(array('ticket_bonus_uzivatel'),array('kod','bonus_castka'))
				->where('user_id=?',Zend_Registry::get('user_id'))
				->where('kod=?',$freeBet)
				->where('vybral=?',0)
				->query()->fetchAll();
				 
				if ( count($row) == 0)   {self::$message['error'][] = self::trans('ticket_er_15') .' #9';return;}

				 
				$am = round($row[0]['bonus_castka'],2);
				 
				$data = array('vybral'=>1,'date'=>date('Y-m-d H:i:s',self::$time));
				 
				Zend_Registry::get('db')->update(
					'ticket_bonus_uzivatel',
					$data,
					array('user_id=?' => Zend_Registry::get('user_id'), 'kod=?' => $freeBet)
				);
			}else{

				$am = round($totalSum,2);

				//NOTE: probably garbage from time before use of stored procedure
				//$data = array('zustatek'=>'zustatek-'.$am);
				//Zend_Registry::get('db')->query('call BalanceMinus('. intval(Zend_Registry::get('user_id')) .','. $am.',0,0)');

				$userId = intval(Zend_Registry::get('user_id'));
				$transaction = array(
					'value' => -$am, 'userId' => $userId, 'hostId' => It6_Models_Host::ID_INTERNET,
					'typeName' => 'user.ticket.Create', 'currencyId' => It6_Models_User::get($userId, 'currencyId')
				);
				Zend_Registry::get('ws')->Transaction->make($transaction);
			}


				

			 
			 
		} catch ( Exception $e ) {
			It6_Log::err($e);
			self::$message['error'][] = self::trans('ticket_er_11') .' #10';
			 
		}


		return $am;
		 
	}



	/**
	 * Vytvari system tiket
	 * @return array,bool
	 */
	/* deprecated, now part of createMaxicombiTicket
	public static function createSystemTicket() {
		 
		$fieldBet = array();
		 
		if ( !isset(self::$ticket['mail'])) self::$ticket['mail'] = 1;
		 
		foreach ( self::$ticket['combinations'] as $sysH) {
			 
			$fieldBet = array();

			$rate = 1;
			 
			foreach ( self::$ticket['bet'] as $k=>$h) {

				try {
				if (!$h['visible'])
					continue;
					$row = Zend_Registry::get('db')->select()->from(array('sz'=>'sazka_pohled'),array('sz.kurz'))
					->join(array('s'=>'udalost'),'s.udalost_id=sz.udalost_id')
					->where('sz.sazka_id=?',$h['id_bet'])
					->where('sz.sloupec_id=?',$h['id_col'])
					->where('sz.platny_od=(select d.platny_od from sazka_kurz d where d.sazka_id=sz.sazka_id order by d.platny_od desc limit 1)')
					->query()->fetchAll();

					if ( count($row) > 0 )    $rate *= $row[0]['kurz'];
					else self::$message['error'][] = self::trans('ticket_er_11') .' #11';

				} catch ( Exception $e ) {

					self::$message['error'][] = self::trans('ticket_er_11') .' #12';
					 
				}
				 
				$fieldBet[] = $h;
				 
			}
			 
			self::createTicket($fieldBet,(mb_strlen(self::$ticket['bet_code']) > 0?self::$ticket['bet_code']:null),self::$ticket['totalSum'],'system',$sysH['k'],$rate,intval(self::$ticket['mail']));

		}
		 
	}
	*/
	 
	/**
	 * Vytvari kombi tiket
	 * @return array,bool
	 */
	 
	public static function createCombiTicket() {
		 
		$fieldBet = array();
		 
		if ( !isset(self::$ticket['mail'])) self::$ticket['mail'] = 1;
		 
		$rate = 1;
		 
		foreach ( self::$ticket['bet'] as $k=>$h) {

			try {
				if (!$h['visible'])
					continue;
				$row = Zend_Registry::get('db')->select()->from(array('sz'=>'sazka_pohled'),array('sz.kurz'))
				->join(array('s'=>'udalost'),'s.udalost_id=sz.udalost_id')
				->where('sz.sazka_id=?',$h['id_bet'])
				->where('sz.sloupec_id=?',$h['id_col'])
				->where('sz.platny_od=(select d.platny_od from sazka_kurz d where d.sazka_id=sz.sazka_id order by d.platny_od desc limit 1)')
				->query()->fetchAll();

				if ( count($row) > 0 )    $rate *= $row[0]['kurz'];
				else self::$message['error'][] = self::trans('ticket_er_11') .' #13';

			} catch ( Exception $e ) {
				It6_Log::err($e);
				self::$message['error'][] = self::trans('ticket_er_11') .' #14';
				 
			}
			 
			$fieldBet[] = $h;
			 
		}
		 
		self::createTicket($fieldBet,(mb_strlen(self::$ticket['bet_code']) > 0?self::$ticket['bet_code']:null),self::$ticket['totalSum'],'kombi',0,$rate,intval(self::$ticket['mail']));

	}

	/**
	 * Vytvari simple tiket
	 * @return array,bool
	 */
	 
	public static function createSimpleTicket() {
		 
		if ( !isset(self::$ticket['mail'])) self::$ticket['mail'] = 1;
		 
		foreach ( self::$ticket['bet'] as $k=>$h) {

			try {
				if (!$h['visible'])
					continue;
				$row = Zend_Registry::get('db')->select()->from(array('sz'=>'sazka_pohled'),array('sz.kurz'))
				->join(array('s'=>'udalost'),'s.udalost_id=sz.udalost_id')
				->where('sz.sazka_id=?',$h['id_bet'])
				->where('sz.sloupec_id=?',$h['id_col'])
				->where('sz.platny_od=(select d.platny_od from sazka_kurz d where d.sazka_id=sz.sazka_id order by d.platny_od desc limit 1)')
				->query()->fetchAll();

				if ( count($row) > 0 )    $h['rate'] = $row[0]['kurz'];
				else self::$message['error'][] = self::trans('ticket_er_11') .' #15';

			} catch ( Exception $e ) {
				It6_Log::err($e);
				self::$message['error'][] = self::trans('ticket_er_11') .' #16';
				 
			}

			self::createTicket(array($h),(mb_strlen(self::$ticket['bet_code']) > 0?self::$ticket['bet_code']:null),$h['amount'],'simple',0,$h['rate'],intval(self::$ticket['mail']));

		}
		 
		 
	}

	 
	/**
	 * Vytvari maxikombi tiket
	 * @return array,bool
	 */
	 
	public static function createMaxicombiTicket() {
 
		$fieldBet = array();
 
		if ( !isset(self::$ticket['mail'])) self::$ticket['mail'] = 1;

		foreach ( self::$ticket['bet'] as $k=>$h) {

			try {
				if (!$h['visible'])
					continue;
				$row = Zend_Registry::get('db')->select()->from(array('sz'=>'sazka_pohled'),array('sz.kurz'))
					->join(array('s'=>'udalost'),'s.udalost_id=sz.udalost_id')
					->where('sz.sazka_id=?',$h['id_bet'])
					->where('sz.sloupec_id=?',$h['id_col'])
					->where('sz.platny_od=(select d.platny_od from sazka_kurz d where d.sazka_id=sz.sazka_id order by d.platny_od desc limit 1)')
					->query()->fetchAll();

				if ( count($row) == 0 )
					self::$message['error'][] = self::trans('ticket_er_11') .' #13';
			} catch ( Exception $e ) {
				It6_Log::err($e);
				self::$message['error'][] = self::trans('ticket_er_11') .' #14';
			}

			$fieldBet[] = $h;
		}
 
		self::createTicket(
			$fieldBet,
			(mb_strlen(self::$ticket['bet_code']) > 0 ? self::$ticket['bet_code'] : null),
			self::$ticket['helper']->stake,
			self::$ticket['type'],
			0,
			self::$ticket['helper']->rate,
			intval(self::$ticket['mail'])
		);
	}

}
