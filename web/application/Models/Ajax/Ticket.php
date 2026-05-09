<?php

class Models_Ajax_Ticket {


	/**
	 * Zjisti kontrolu k tiketu na chyby
	 * @param array $ticket data na tiketu
	 * @return array
	 */

	public static function control($ticket) {

		Models_Control_Ticket::$ticket = $ticket;
		 
		return Models_Control_Ticket::allErrorControl();
		 
	}

	/**
	 * Zjisti kontrolu k tiketu na zmeny
	 * @param array $ticket data na tiketu
	 * @return array
	 */

	public static function controlChange($ticket) {

		Models_Control_Ticket::$ticket = $ticket;
		 
		return Models_Control_Ticket::allChangeControl();
		 
	}


	 
	/**
	 * Zjisti kontrolu povolovani bookmakerem
	 * @param array $ticket data na tiketu
	 * @return array
	 */

	public static function controlProve($ticket) {

		Models_Control_Ticket::$ticket = $ticket;
		 
		return Models_Control_Ticket::proveControl();
		 
	}

	/**
	 * Nacte data na tiket
	 * @param object $view  view
	 * @return bool
	 */

	public static function freeBet($view) {
		 
		$pole = array();
		 
		if ($view->log == 2 || $view->log == 3) {

			 
			$row = Zend_Registry::get('db')->select()->from(array('ticket_bonus_uzivatel'),array('kod','bonus_castka'))
			->where('user_id=?',Zend_Registry::get('user_id'))
			->where('vybral=?',0)->query()->fetchAll();

			foreach($row as $h) {

				$pole[] = array($h['kod'],$h['bonus_castka']);

			}

		}
		 
		return $pole;
		 
	}

	public static function moveCouponFromSessionToDb($userId, $db) {
		if (!empty($_SESSION['coupon_data'])) {
			try {
				$json = $_SESSION['coupon_data'];
				$coupon = Zend_Json::decode($json);
			}
			catch (Exception $e) {
			}
			unset($_SESSION['coupon_data']);
			// if in session is stored coupon with bets, replace user's coupon_data record
			if (!empty($coupon['bet'])) {
				$db->delete('coupon_data', array('user_id=?' => $userId, 'admin_id=?' => It6_Models_Admin::ID_INTERNET));
				$couponId = It6_Models_CouponSequence::nextId($db);
				$db->insert(
					'coupon_data',
					array(
						'user_id' => $userId,
						'admin_id' => It6_Models_Admin::ID_INTERNET,
						'host_id' => It6_Models_Host::ID_INTERNET,
						'coupon_id' => $couponId,
						'data' => $json,
						'status' => It6_Models_Ticket::COUPON_STATUS_NEW,
						'date' => It6_Date::dbNow(),
						'modified' => null
					)
				);
				$coupon['couponId'] = $couponId;
			}
			return $coupon;
		}
		return null;
	}

	/**
	 * Nacte data na tiket
	 * @param object $view  view
	 * @return bool
	 */

	public static function get($view) {
		$ret = array();
		$db = Zend_Registry::get('db');
		$userId = It6_Models_User::ID_INTERNET_ANONYMOUS;
		if ($view->log == 2 || $view->log == 3) {
			$userId = Zend_Registry::get('user_id');
			$ret = self::moveCouponFromSessionToDb($userId, $db);
			if (empty($ret)) {
				$ret = array();
				$row = $db->select()->from(array('coupon_data'),array('coupon_id', 'status', 'data', 'modified'))
					->where('user_id=?', $userId)
					->where('admin_id=?', It6_Models_Admin::ID_INTERNET)
					->query()
					->fetchAll();

/* only if coupon all states are persistent (including those moved to archive)
				if (count($row) == 0 && !empty($_SESSION['couponId'])) {
					$row = $db->select()->from(array('coupon_data_archive'),array('coupon_id', 'status', 'data', 'modified'))
						->where('coupon_id=?', $_SESSION['couponId'])
						->order('archived_at DESC')
						->limit(1)
						->query()
						->fetchAll();
				}
*/
				if (count($row) > 0 && mb_strlen($row[0]['data']) > 0) {
					$row = $row[0];
					$ret = Zend_Json::decode($row['data']);
					$ret['couponId'] = $row['coupon_id'];
					$ret['status'] = $row['status'];
					$ret['preapproved'] = (empty($row['modified']) ? array() : Zend_Json::decode($row['modified']));
					switch ($row['status']) {
					case It6_Models_Ticket::COUPON_STATUS_GOING_TO_ACCEPTATION:
						 $ret['confirmationStep'] = 1;
						 break;
					case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION:
					case It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION:
					case It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION_LIVE:
					case It6_Models_Ticket::COUPON_STATUS_PROLONGED:
					case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_ACCEPTED:
					case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_REJECTED:
					case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED:
					case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED:
					case It6_Models_Ticket::COUPON_STATUS_ACCEPTED:
					case It6_Models_Ticket::COUPON_STATUS_REJECTED:
						$ret['confirmationStep'] = 2;
						break;
					case It6_Models_Ticket::COUPON_STATUS_MODIFIED:
						$ret['confirmationStep'] = 3;
						break;
					default:
						$ret['confirmationStep'] = 0;
						break;
					}
				}
			}
		}
		else{
			if (isset($_SESSION['coupon_data']))
				$ret = array_merge($ret, Zend_Json::decode($_SESSION['coupon_data']));
		}
		if (!array_key_exists('couponId', $ret))
			$ret['couponId'] = 0;
		if (!array_key_exists('status', $ret))
			$ret['status'] = It6_Models_Ticket::COUPON_STATUS_NEW;
		if (!array_key_exists('preapproved', $ret))
			$ret['preapproved'] = array();
		$ret['bankerCount'] = 0;
		if(count($ret) > 0 && isset($ret) && isset($ret['bet'])) {
			foreach($ret['bet'] as $k=>$betData) {
				if (!empty($betData['banker']))
					++$ret['bankerCount'];
				$ret['bet'][$k] = array_merge($ret['bet'][$k],Models_Markets_MarketData::getBetTicketData($betData['id_bet'],$betData['id_col']));
				 
			}
			 
		}
		$ret['userId'] = $userId;
		$helper = new It6_Models_Ticket($ret, It6_Models_Ticket::DATA_AJAX, 'user', $db);
		if (It6_Models_Ticket::COUPON_STATUS_MODIFIED == $ret['status'])
			$helper->mergePreapproved($ret['preapproved']);
		$helper->computeAggregates();
		$ret['helper'] = $helper;
		if (array_key_exists('bet', $ret))
			$ret['bet'] = $helper->sortBetsByGroup($ret['bet'], $ret['type']);

		//if(isset($row) && count($row) == 0) {$view->createMessage = array('ok'=>1);return false;}

		static::getCampaignData($ret, $helper);
		$ret['maxBetNum'] = It6_Models_TicketValidator::getAllMaxBetNums();

		$view->createTicket = $ret;
		$view->helper = $helper;

		return true;

	}

	/**
	 * Nastavi na nulu v povolovani data
	 * @return bool
	 */

	public static function setZero() {

		try{
			 

			$d = array();
			$d['status'] = 0;

			if(Zend_Registry::isRegistered('user_id')) Zend_Registry::get('db')->update(
				'coupon_data', $d, array("user_id=?" => Zend_Registry::get('user_id'), 'admin_id=?' => It6_Models_Admin::ID_INTERNET)
			);

		} catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);
			 
		}

	}

	/**
	 * Vymaze radek v cupon
	 * @return bool
	 */

	public static function setEmpty() {

		try{
			 
			Zend_Registry::get('db')->delete(
				'coupon_data', array("user_id=?" => Zend_Registry::get('user_id'), 'admin_id=?' => It6_Models_Admin::ID_INTERNET)
			);

		} catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);
			 
		}

	}

	/**
	 * Ulozi tiket do DB nebo Session podle toho jeslti je prihlaseny
	 * @param struct $dbCoupon Current coupon in DB
	 * @param struct $coupon Decoded coupon data from request
	 * @param int $log  identifikace prihlaseni
	 * @param string $json Request data for coupon
	 * @param integer Status which will be saved (NEW is default)
	 * @return bool
	 */

	public static function save(&$dbCoupon, $coupon, $log, $json = null, $status = null) {
		if ($log == 2 || $log == 3) {
			$db = Zend_Registry::get('db');
			if (!empty($coupon['bet'])) {
				$bets = array();
				$order = 0;
				foreach($coupon['bet'] as &$bet) {
					++$order;
					if ( empty($bet['order'] )) {
						$bet['order'] = $order;
					}
					else {
						$order = $bet['order'];
					}
					$bets[] = $bet['id_bet'];
					unset($bet);
				}
				$row = $db->select()
					->from(array('sazky'), array('sazka_id'))
					->where('sazka_id IN (?)', $bets)
					->where('live=0')->query()->fetchAll();
				$live = (count($row) > 0 ? 0 : 1);
			}
			else
				$live = 0;

			$userId = Zend_Registry::get('user_id');

			$d = array();
			$d['live'] = $live;
			$d['data'] = (isset($json) ? $json : Zend_Json::encode($coupon));
			$d['status'] = (isset($status) ? $status : It6_Models_Ticket::COUPON_STATUS_NEW);
			$d['date'] = It6_Date::dbNow();
			$d['modified'] = null;
			$d['user_id'] = $userId;
			$d['admin_id'] = It6_Models_Admin::ID_INTERNET;
			$d['host_id'] = It6_Models_Host::ID_INTERNET;

			try{
				//$db->beginTransaction();
//				$row = $db->select()->from(array('coupon_data'), array('coupon_id', 'status'))
//				->where('user_id=?', $userId)
//				->where('admin_id=?', It6_Models_Admin::ID_INTERNET)
//				->query()->fetchAll();

				if(!empty($dbCoupon)) {
					$couponId = $dbCoupon['couponId'];
					if (It6_Models_Ticket::COUPON_STATUS_NEW !=  $dbCoupon['status']
						&& It6_Models_Ticket::COUPON_STATUS_GOING_TO_ACCEPTATION !=  $dbCoupon['status'])
						$couponId = It6_Models_CouponSequence::nextId($db);
					$d['coupon_id'] = $couponId;
					$db->update(
						'coupon_data', $d, array('user_id=?' => $userId, 'admin_id=?' => It6_Models_Admin::ID_INTERNET)
					);
					$dbCoupon['couponId'] = $couponId;
				}
				else {
					$d['coupon_id'] = It6_Models_CouponSequence::nextId($db);
					$db->insert('coupon_data', $d);
				}
			} catch ( Exception $e ) {
				Models_Exception_Handler::handle($e);
			}

			//Zend_Registry::get('db')->commit();

		}else
			$_SESSION['coupon_data'] = $json;

		return true;

	}

	/**
	 * Sets data for campaign handling in ticket
	 * @param array $data Output array to add data into
	 * @param It6_Models_Ticket $helper Initializaed helper for coupon
	 */
	public static function getCampaignData(array& $data, $helper) {
		$ws = Zend_Registry::get('ws');
		
		$helper->computeAggregates();
		
		$userId = Zend_Registry::isRegistered('user_id') ?
					Zend_Registry::get('user_id') : null;
		
		
		
		$data['showRateAdvance'] = false;
		
		$tmp = $ws->Campaign->getPreferenceRateValidSizes(
				$helper->type,
				$helper->betCount,
				$helper->stake,
				$helper->rate,
				$userId,			
				$helper->isPointTicket(),
				$helper->rateAdvance
		);
		
		$tmp = It6_ArrayWrapper::toNativeArray($tmp);
		
		
		
		$data['preferenceSizes'] = array();
		foreach ( $tmp as $size => $v ) {
			if ( $v['valid'] ) {
				$data['preferenceSizes'][$size] = true;
				$data['showRateAdvance'] = true;
			}
			else
				$data['preferenceSizes'][$size] = false;
		}

		$crc = array(
			'userId'      => $userId,
			'pointTicket' => $helper->isPointTicket(),
		);
		
		if ( $helper->type == It6_Models_Ticket::TYPE_MAXI ) {
			$crc['combinations'] = $helper->getDataForCampaign();
		}
		else {
			$crc['betCount']  = $helper->betCount;
			$crc['stake'] = $helper->stake;
			$crc['rate'] = $helper->rate;
		}
		
		$data['showGetPoints'] = $ws->Campaign->validate(
			'CreateMoneyTicket',
			'get',
			$crc
		);
		
		if ( $data['showGetPoints'] ) {
			$data['getPoints'] = $ws->Campaign->get(
				'CreateMoneyTicket',
				'get',
				'Value',
				$crc
			);
		}
		else {
			$data['getPoints'] = 0;
		}
		
		$pointsMinAmount = $ws->Campaign->get(
			'CreatePointTicket',
			'spend', 'minAmount',
			array()
		);

		$rates = array();
		foreach ( $helper->bets as $bet )
			$rates[] = $bet['rate'];
		$data['showPoints'] = $ws->Campaign->validate(
			'CreatePointTicket',
			'spend',
			array(
				'type'          => $helper->type,
				'pointType'     => $helper->pointType,
				'betCount'      => count($helper->bets),
		        'stakeInPoints' => $pointsMinAmount+1,
				'rates'         => $rates,
				'userId'        => $userId,
				'currencyId'    => $helper->currencyId
		));

		if ( !empty($helper->rateAdvance) && $helper->rateAdvance > 1)
			$data['rateAdvanceCost'] = $ws->Campaign->get(
					'PreferenceRate',
					'spend',
					'Cost',
					array('preferenceSize' => round(100 * ($helper->rateAdvance - 1)) ));
		else
			$data['rateAdvanceCost'] = 0;
	}

	/**
	 * Uloží tvale kupón do DB pod číselným aliasem
	 * @param array $coupon
	 */
	public static function saveCoupon($coupon) {
		$d = array();
		// smazání zbytečných dat
		unset($coupon['couponId'], $coupon['givenStake'], $coupon['bet_code'], $coupon['win']);
		if (!empty($coupon['combinations']) && is_null($coupon['combinations'][0])) {
			array_shift($coupon['combinations']);
		}
		if (!empty($coupon['bet'])) foreach ($coupon['bet'] as $id => $bet) {
			unset($coupon['bet'][$id]['couponTime'], $coupon['bet'][$id]['order']);
			if ($coupon['type'] == 'simple') unset($coupon['bet'][$id]['group']);
			else unset($coupon['bet'][$id]['amount']);
		}
		if ($coupon['type'] != 'maxikombi') unset($coupon['combinations']);
		
		$d['data'] = Zend_Json::encode($coupon);
		$data_hash =  md5($d['data']);
		if (
			isset($_SESSION['saved_coupon_data']['hash']) && 
			$_SESSION['saved_coupon_data']['hash'] == $data_hash && 
			isset($_SESSION['saved_coupon_data']['id'])
		) {			
			return $_SESSION['saved_coupon_data']['id'];
		}
		
		$db = Zend_Registry::get('db');
		
		$db->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
		$db->beginTransaction();
		$alias = $db->fetchOne($db->select()->forUpdate()->from( 'coupon_saved_alias', 'alias')->where('saved_coupon_id is null')->limit(1)->order('alias')->__toString());
		if (empty($alias)) {
			$db->insert('coupon_saved_alias', array());
			$alias = $db->lastInsertId();
		}
		
		$d['date'] = It6_Date::dbNow();
		$d['user_id'] = Zend_Registry::get('user_id');
		$d['admin_id'] = It6_Models_Admin::ID_INTERNET;
		$d['host_id'] = It6_Models_Host::ID_INTERNET;
		$d['platny_do'] = null;
		if (!empty($coupon['bet'])) foreach ($coupon['bet'] as $id => $bet) {
			$platny_do = $db->fetchOne($db->select()->from('sazky', 'platna_do')->limit(1)->where('sazka_id = ?', $bet['id_bet'])->__toString());
			if (is_null($d['platny_do']) || strtotime($d['platny_do']) < strtotime($platny_do))
				$d['platny_do'] = $platny_do;
		}
		$d['alias'] = $alias;
		
		$db->insert('coupon_saved', $d);
		$db->update('coupon_saved_alias', array('saved_coupon_id' => $db->lastInsertId()),  'alias = ' . $alias);
		$db->commit();
		
		$_SESSION['saved_coupon_data'] = array(
			'hash' => $data_hash,
			'id' => $alias,
		);
		
		return $alias;
	}
	
	/**
	 * Vyhledá uložený kupón podle ID 
	 * @param int $couponId
	 */
	public static function findSavedCoupon($couponId) {
		$ws = Zend_Registry::get('ws');
		return $ws->Coupon->findSavedCoupon($couponId);
	}
}