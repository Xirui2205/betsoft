<?php

class AjaxController extends Zend_Controller_Action {

	const STEP_UPDATE = 0;
	const STEP_CONFIRM1 = 10;
	const STEP_CONFIRM2_1 = 20;
	const STEP_CONFIRM2_2 = 21;
	const STEP_CONFIRM3 = 30;

	/**
	 * idnetifikator zakladani tiketu
	 * @access private
	 * @var int
	 */

	private static $step = null;
	private static $createTicket = false;
	private static $preapproved = false;
	private static $commands = array();
	private static $couponId = 0;
	private static $mixedResponse = true; // if response must have type prefix


	public function init() {
		if (!in_array($this->getRequest()->getActionName(), array('client-command', 'tickets-of-month')))
			Models_BasicRender::render($this->view, $this->_request);
		$this->view->addHelperPath('views/helpers', 'My_View_Helper');
		$this->_helper->layout->disableLayout();

	}

	private function setViewCurrency() {
		$ws = Zend_Registry::get('ws');

		if ( Zend_Registry::isRegistered('mena') ) {
			$this->view->mena = Zend_Registry::get('mena');
		}
		else {
			$this->view->mena = '';
		}
	}

	public function checkLogin() {
		if($this->view->log == 2 || $this->view->log == 3){
			return true;
		}
		else {
			self::$commands[] = array('name' => 'displayMessage', 'param' => Zend_Registry::get('translate')->trans('ticket_er_1', 'TICKET', It6_Translate_Web::DICTIONARY));
			self::$commands[] = array('name' => 'stopConfirmation', 'noBack' => (self::$step == self::STEP_CONFIRM1 ? true : false));
			self::$commands[] = array('name' => 'showLoginForm', 'param' => true);
			$this->_forward('client-command');
			It6_GlobalCache_Invalidator::invalidateLogoutFrame();
			return false;
		}
	}

	public function sendClientCommands($mixedResponse, $stopConfirmation, $msg) {
		self::$mixedResponse = $mixedResponse;
		if ($stopConfirmation)
			self::$commands[] = array('name' => 'stopConfirmation');
		self::$commands[] = array('name' => 'displayMessage', 'param' => $msg);
		$this->_forward('client-command');
	}

	public static function isPreconfirmationCouponStatus($status) {
		return (It6_Models_Ticket::COUPON_STATUS_NEW == $status
			|| It6_Models_Ticket::COUPON_STATUS_GOING_TO_ACCEPTATION == $status
		);
	}

	public static function isPostconfirmationCouponStatus($status) {
		return (
			It6_Models_Ticket::COUPON_STATUS_ACCEPTED == $status
			|| It6_Models_Ticket::COUPON_STATUS_REJECTED == $status
			|| It6_Models_Ticket::COUPON_STATUS_MODIFIED == $status
		);
	}

	/**
	 * Makes complex checks of concurrent ticket confirmations
	 * @param integer $step STEP_* constant
	 * @param struct $coupon [optional] returned data fetched from DB
	 * @return boolean TRUE if check passed
	 */
	public function checkCouponConfirmation($step, &$coupon = null) {
		$ws = Zend_Registry::get('ws');
		$userId = Zend_Registry::get('user_id');
		$coupon = $ws->Coupon->getByUserIdAndAdminId($userId, It6_Models_Admin::ID_INTERNET);
		if (It6_Models_User::ID_INTERNET_ANONYMOUS != $userId && isset($_SESSION['coupon_data'])) {
			if ( empty($coupon) || self::isPreconfirmationCouponStatus($coupon['status']) || self::isPostconfirmationCouponStatus($coupon['status']) ) {
				Models_Ajax_Ticket::moveCouponFromSessionToDb($userId, Zend_Registry::get('db'));
				$coupon = $ws->Coupon->getByUserIdAndAdminId($userId, It6_Models_Admin::ID_INTERNET);
			}
			unset($_SESSION['coupon_data']);
		}
		if (empty($coupon) && !empty(self::$couponId))
			$coupon = $ws->Coupon->getLastArchiveById(self::$couponId);
		// check: any coupon exists
		$now = time();
		$exists = !empty($coupon);
		if ($exists) {
			//TODO:
		}
		$preconfirmationStep = (self::STEP_UPDATE == $step || self::STEP_CONFIRM1 == $step || self::STEP_CONFIRM2_1 == $step);
		$postconfirmationStatus = self::isPostconfirmationCouponStatus($coupon['status']);
		$mixedResponse = (self::STEP_UPDATE != $step);
		$stopConfirmation = !$preconfirmationStep;
		if (!$exists) {
			if ($preconfirmationStep)
				return true;
			else {
				$this->sendClientCommands($mixedResponse, $stopConfirmation, Zend_Registry::get('translate')->trans('ticket_er_23', 'TICKET', It6_Translate_Web::DICTIONARY));
				return false;
			}
		}
		// check coupon expiration
		$timestamp = It6_Date::fromDbAsTimestamp($coupon['date']);
		$timeout = It6_Models_Ticket::getStatusTimeout($coupon['status']);
		$expired = (time() > $timestamp + $timeout);
		if ($expired) {
			if ($preconfirmationStep)
				return true;
			else if (self::$couponId != $coupon['couponId']) {
				$this->sendClientCommands($mixedResponse, $stopConfirmation, Zend_Registry::get('translate')->trans('ticket_er_23', 'TICKET', It6_Translate_Web::DICTIONARY));
				return false;
			}
		}
		// check if status is accessible in given step
		$msg = false;
		switch ($coupon->status) {
		case It6_Models_Ticket::COUPON_STATUS_NEW:
			if (self::STEP_UPDATE != $step && self::STEP_CONFIRM1 != $step)
				$msg = Zend_Registry::get('translate')->trans('ticket_er', 'TICKET', It6_Translate_Web::DICTIONARY);
			break;
		case It6_Models_Ticket::COUPON_STATUS_GOING_TO_ACCEPTATION:
			if ((self::STEP_CONFIRM2_1 != $step && self::STEP_CONFIRM1 != $step && self::STEP_UPDATE != $step) || self::$couponId != $coupon['couponId'])
				$msg = Zend_Registry::get('translate')->trans('ticket_er_22', 'TICKET', It6_Translate_Web::DICTIONARY);
			break;
		case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION:
		case It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION:
		case It6_Models_Ticket::COUPON_STATUS_PROLONGED:
			if ((self::STEP_CONFIRM2_2 != $step || self::$couponId != $coupon['couponId'])) // && self::STEP_UPDATE != $step)
				$msg = Zend_Registry::get('translate')->trans('ticket_er_22', 'TICKET', It6_Translate_Web::DICTIONARY);
			break;
		case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_ACCEPTED:
		case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_REJECTED:
			if (self::$couponId != $coupon['couponId'] || (self::STEP_CONFIRM2_1 != $step && self::STEP_CONFIRM2_2 != $step))
				$msg = Zend_Registry::get('translate')->trans('ticket_er_22', 'TICKET', It6_Translate_Web::DICTIONARY);
			break;
		case It6_Models_Ticket::COUPON_STATUS_ACCEPTED:
		case It6_Models_Ticket::COUPON_STATUS_REJECTED:
			if ( (self::$couponId != $coupon['couponId'] || (self::STEP_CONFIRM2_1 != $step && self::STEP_CONFIRM2_2 != $step)) )
				//&& self::STEP_UPDATE != $step)
				$msg = Zend_Registry::get('translate')->trans('ticket_er_22', 'TICKET', It6_Translate_Web::DICTIONARY);
			break;
		case It6_Models_Ticket::COUPON_STATUS_MODIFIED:
			if ((self::STEP_CONFIRM3 != $step && self::STEP_CONFIRM2_2 != $step) || self::$couponId != $coupon['couponId'])
				$msg = Zend_Registry::get('translate')->trans('ticket_er_22', 'TICKET', It6_Translate_Web::DICTIONARY);
			break;
		case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED:
		case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED:
			if ((self::STEP_CONFIRM3 != $step && self::STEP_CONFIRM2_2 != $step) || self::$couponId != $coupon['couponId'])
				$msg = Zend_Registry::get('translate')->trans('ticket_er_22', 'TICKET', It6_Translate_Web::DICTIONARY);
			break;
		case It6_Models_Ticket::COUPON_STATUS_INTERRUPTED_ACCEPTATION:
			if (self::$couponId != $coupon['couponId'])
				$msg = Zend_Registry::get('translate')->trans('ticket_er_22', 'TICKET', It6_Translate_Web::DICTIONARY);
			break;
		case It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION_LIVE:
			throw new Exception('Not implemented');
			break;
		default:
			throw new Exception('Unknown coupon status: ' . $coupon['status']);
		}
		if (false !== $msg) {
			$this->sendClientCommands($mixedResponse, $stopConfirmation, $msg);
			return false;
		}
		return true;
	}

	public function indexAction() {
		$this->_helper->layout->disableLayout();
		//echo "<pre>";print_r($this->view->odds);exit;
	}

	public function ticketAction()
	{
		It6_GlobalCache::turnOff();
		if ( !isset($_GET['notInvalidate']) ) {
			It6_GlobalCache_Invalidator::invalidateTicketFrame();
		}

		$saveCoupon = false;
		if ( isset($_GET['saveCoupon']) ) {
			$saveCoupon = true;
		}
		
		self::$step = self::STEP_UPDATE;
		$ws = Zend_Registry::get('ws');
		$data = implode( "\r\n", file('php://input') );
		$coupon = Zend_Json::decode($data);
		self::$couponId = $coupon['couponId'];
		$userId = Zend_Registry::get('user_id');

		if (!$this->checkCouponConfirmation(self::$step, $dbCoupon))
			return;

		$response = array();

		#Ulozi data z tiketu#
		if ( !Models_Control_Ticket::isProve( $this->view->log ) ) {
			Models_Ajax_Ticket::save($dbCoupon, $coupon, $this->view->log, $data);
			$response['error'] = 0;
			$coupon['userId'] = $userId;
			$helper = new It6_Models_Ticket($coupon, It6_Models_Ticket::DATA_AJAX, 'user');
			$helper->computeAggregates();
			$total = $helper->stake; //(empty($coupon['totalSum']) ? 0 : $helper->roundStake($coupon['totalSum']));
			$givenTotal = (array_key_exists('givenStake', $coupon) && false !== $coupon['givenStake'] ? $helper->roundStake($coupon['givenStake']) : false);
			$computed = false;
			if (It6_Models_Ticket::TYPE_MAXI == $helper->type) {
				// now merging only maxicombi stakes differences for total stake
				if ($helper->ticketCount > 0 && false !== $givenTotal && $givenTotal != $total) {
					$helper->setCombinationsStakes($givenTotal, true);
					$changes = array('stake' => $givenTotal);
					$modified = $helper->recalculatePreapproved($changes);
					$ws->Coupon->updateAjaxData(self::$couponId, $helper);
				}
			}

			Models_Ajax_Ticket::getCampaignData($response, $helper);
			$validator = new It6_Models_TicketValidator($helper);
			$cmds = array();
			$refresh = false;
			if ( true !== ($result = $validator->hasNotCorrelatedBets()) ) {
				$translate = Zend_Registry::get('translate');
				foreach ($result as $err) {
					$errMsg = $err['errorMessage'];
					$param = false;
					if (is_array($errMsg)) {
						$param = $errMsg;
						$errMsg = array_shift($param);
					}
					else if (isset($err['errorMessageParam']))
						$param = $err['errorMessageParam'];
					if (false === $param)
						$errMsg = $translate->trans($errMsg, 'TICKET', It6_Translate_Web::DICTIONARY);
					else
						$errMsg = $translate->transParam($errMsg, $param, 'TICKET', It6_Translate_Web::DICTIONARY);
					$cmds[] = array(
						'name' => 'displayMessage',
						'param' => $errMsg,
						'bet' => $err['field'],
						'column' => $err['fieldSpec'],
						'pinPoint' => 'after-update',
					);
				}
			}
			if ( true !== ($result = $validator->isValidBet()) ) {
				foreach ($result as $err) {
					$cmds[] = array(
						'name' => 'deleteBet',
						'bet' => $err['field'],
						'column' => $err['fieldSpec'],
						'pinPoint' => 'after-update',
					);
				}
				$refresh = true;
			}
			
			if (empty($cmds) && $saveCoupon) {
				$savedCouponId = Models_Ajax_Ticket::saveCoupon($coupon);
				$translate = Zend_Registry::get('translate');
				$cmds[] = array(
						'name' => 'displayMessage',
						'param' => $translate->transParam('coupon_saved', $savedCouponId, 'TICKET', It6_Translate_Web::DICTIONARY),
						'type' => 'success',
				);
			}
			if (!empty($cmds))
				$response['commands'] = $cmds;
			if ($refresh)
				$response['refresh'] = true;
			$response['ticket'] = $helper->getExposedData();
		}
		else {
			$response['error'] = 1;
		}

		$this->getResponse()->setHeader('Content-Type', 'application/json');
		$this->view->response = Zend_Json::encode($response);
	}

	public function confirm1Action()
	{
		It6_GlobalCache::turnOff();
		It6_GlobalCache_Invalidator::invalidateTicketFrame();
		if (!isset(self::$step)) {
			self::$step = self::STEP_CONFIRM1;
			self::$couponId = $this->getRequest()->getParam('couponId');
		}

		if (!$this->checkLogin())
			return;

		if (!$this->checkCouponConfirmation(self::$step, $dbCoupon))
			return;

		$ws = Zend_Registry::get('ws');

		if ( Models_Control_Ticket::isProve( $this->view->log )
				&& !self::$createTicket) {

			$this->view->response = 1;
		}
		else {
			$this->view->prove = array(
				'error' => array(), 'warning' => array(), 'action' => array()
			);

			if (self::STEP_CONFIRM1 == self::$step || self::STEP_CONFIRM2_1 == self::$step) { // && It6_Models_Ticket::COUPON_STATUS_NEW == $dbCoupon->$status) {
				$json = $this->getRequest()->getParam('coupon');
				$coupon = Zend_Json::decode($json);
				if (self::STEP_CONFIRM1 == self::$step)
					$newStatus = It6_Models_Ticket::COUPON_STATUS_GOING_TO_ACCEPTATION;
				else
					$newStatus = It6_Models_Ticket::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION;
				Models_Ajax_Ticket::save($dbCoupon, $coupon, $this->view->log, $json, $newStatus);
			}

			//TODO: transaction should not be started here but it should be started more transactions in particular functions
			//      (eg. there could be updates in coupon that shouldn't be rollbacked)
			//It6_DbTransaction::begin(Zend_Registry::get('db'));
			$get = Models_Ajax_Ticket::get($this->view);
			if ( !$get ) return;

			$translate = Zend_Registry::get('translate');

			$_SESSION['couponId'] = $couponId = $this->view->createTicket['couponId'];
			//$create = false;
			$aStatus = null;
			$status = $this->view->createTicket['status'];
			$enqueued = false;
			$actions = array();

			if (empty($couponId)) {
				$this->view->response = 'json:{"commands":[{"name":"stopConfirmation"}]}';
			}
			else if (It6_Models_Ticket::COUPON_STATUS_INTERRUPTED_ACCEPTATION == $status) {
				$this->view->prove['script'] = 'tick.confirmationEnd();';
				//$this->view->prove['error'][] = array('message' => $translate->trans('ticket_notprove', 'TICKET', It6_Translate_Web::DICTIONARY));
				$coupon = Zend_Json::decode($dbCoupon['data']);
				$coupon['userId'] = Zend_Registry::get('user_id');
				$helper = new It6_Models_Ticket($coupon, It6_Models_Ticket::DATA_AJAX, 'user');
				if (!empty($dbCoupon['modified']))
					$helper->mergePreapproved($dbCoupon['modified']);
				$helper->computeAggregates();
				$aStatus = Webservice_Ticket::validate(null, $helper);
				$this->view->confirmButton = 'confirm';
				$this->view->backButton = 'ticket_to_correct';
				Webservice_Coupon::updateStatus($dbCoupon['couponId'], It6_Models_Ticket::COUPON_STATUS_GOING_TO_ACCEPTATION);
				self::$step = self::STEP_CONFIRM1;
				if (self::$preapproved)
					$actions[] = 'tick.executeCommands([{"name":"setPreapproved","param":false}]);';
			}
			else if (self::$preapproved && It6_Models_Ticket::COUPON_STATUS_MODIFIED == $status) {
				//TODO: check timeout, cancel coupon otherwise, send feedback to user
				$reject = !self::$createTicket;
				$result = $ws->Ticket->acceptAutorisedTicket($couponId, $reject);
				if (It6_ArrayWrapper::isArray($result)) {
					$aStatus = $result;
					Webservice_Coupon::updateStatus($dbCoupon['couponId'], It6_Models_Ticket::COUPON_STATUS_GOING_TO_ACCEPTATION);
					self::$step = self::STEP_CONFIRM1;
					$actions[] = 'tick.executeCommands([{"name":"setPreapproved","param":false}]);';
				}
				else if ($reject)
					$this->view->response = 'json:{"commands":[{"name":"stopConfirmation"}]}';
				else
					$enqueued = true;
				/*
				if (isset($result))

				if (self::$createTicket) {
					$this->view->createMessage = array('ok' => 1);
				}
				else {
					//$ws->Coupon->delete($couponId);
					$this->view->response = 'Preapproved ticket rejected';
					$this->view->prove['ok'] = true;
					//$this->view->prove['script'] = 'TicketBack(true);';
				}
				*/
			}
			else if ( !self::$createTicket ) {
				$userId = Zend_Registry::get('user_id');
				$ws->Coupon->updateStatus($couponId, It6_Models_Ticket::COUPON_STATUS_GOING_TO_ACCEPTATION);
				if (empty($coupon)) {
					$json = $this->getRequest()->getParam('coupon');
					$coupon = Zend_Json::decode($json);
				}
				$coupon['userId'] = $userId;
				$helper = new It6_Models_Ticket($coupon, It6_Models_Ticket::DATA_AJAX, 'user');
				$helper->computeAggregates();
				$aStatus = Webservice_Ticket::validate(null, $helper);
				$this->view->prove['script'] = "tick.couponId=$couponId;";
				$this->view->backButton = 'back';
				$this->view->confirmButton = 'confirm';
			}
			else {
				$aStatus = $ws->Ticket->checkAuthorisationStatus($couponId);
				if (isset($aStatus['status']))
					$aStatus = $aStatus['status'];

				if ( is_numeric($aStatus) ) {
					if (Webservice_Ticket::STATUS_WAITING == $aStatus && It6_Models_Ticket::isStatusWaitingForConfirmd($status))
						$enqueued = true;
					else {
						switch ( $aStatus ) {
						case Webservice_Ticket::STATUS_WAITING:
							$this->view->prove['script'] = 'tick.confirmationStart(' . $couponId . ', true)';
							$this->view->prove['message'] = $translate->trans( 'ticket_prove', 'TICKET', It6_Translate_Web::DICTIONARY );
							break;
						case Webservice_Ticket::STATUS_CANCELED:
							$this->view->prove['script'] = 'tick.confirmationEnd(); tick.betsLocked = false; tick.couponId=0;';
							$this->view->prove['error'][] = array('message' => $translate->trans('ticket_notprove', 'TICKET', It6_Translate_Web::DICTIONARY));
							$this->view->backButton = 'back';
							break;
						case Webservice_Ticket::STATUS_ACCEPTED:
							$this->view->prove['ok'] = true;
							$this->view->prove['script'] = 'tick.confirmationEnd(); tick.couponId=0;';
							$this->view->createMessage = array('ok' => 1);
							break;
						case Webservice_Ticket::STATUS_ACCEPTED_MODIFIED:
							$helper = $this->view->createTicket['helper'];
							$modified = $this->view->createTicket['preapproved'];
							$helper->mergePreapproved($modified);
							$helper->computeAggregates();
							$amount = $this->view->createTicket['helper']->stake;
							$this->view->backButton = 'reject';
							$this->view->confirmButton = 'confirm';
							$this->view->prove['script'] = 'tick.PreapprovedTicketUpdate(' . $couponId . ', ' . Zend_Json::encode($modified) . ');';
							$this->view->prove['message'] = $translate->transParam('ticket_othersum', $amount .' '. Zend_Registry::get('mena'), 'TICKET', It6_Translate_Web::DICTIONARY);
							break;
						}
					}
				}
			}
			if (It6_ArrayWrapper::isArray($aStatus)) {
				$helper = $this->view->createTicket['helper'];
				$helperDirty = false;
				$errors = array();
				$warnings = array();
				$generalError = false;
				foreach ($aStatus as $err) {
					$errMsg = $err['errorMessage'];
					$param = false;
					if (is_array($errMsg)) {
						$param = $errMsg;
						$errMsg = array_shift($param);
					}
					else if (isset($err['errorMessageParam']))
						$param = $err['errorMessageParam'];
					if (false === $param)
						$errMsg = $translate->trans($errMsg, 'TICKET', It6_Translate_Web::DICTIONARY);
					else
						$errMsg = $translate->transParam($errMsg, $param, 'TICKET', It6_Translate_Web::DICTIONARY);
					if (1 == $err['error']) {
						if (empty($generalError))
							$generalError = $errMsg;
						continue;
					}
					if (isset($err['type']) && 'warning' == $err['type'])
						$warnings[] = array('message' => $errMsg, 'bet' => $err['field'], 'column' => $err['fieldSpec']);
					else
						$errors[] = array('message' => $errMsg, 'bet' => $err['field'], 'column' => $err['fieldSpec']);
					if (isset($err['actions']) && It6_ArrayWrapper::isArray($err['actions'])) {
						$clientCmds = array();
						foreach ($err['actions'] as $action) {
							if ('rateChanged' == $action['name']) {
								foreach ($helper->bets as &$bet) {
									if ($bet['id'] == $action['betId'] && $bet['column'] == $action['columnId']) {
										$bet['rate'] = $action['rate'];
										$helperDirty = true;
									}
								}
								$actions[] = "tick.rateChanged({$action['betId']}, {$action['columnId']}, {$action['rate']});";
								$helperDirty = true;
							}
							else if ('betStakeChanged' == $action['name']) {
								$helper->setBetData( array($action['bet'], $action['column']), array('amount' => $action['stake']) );
								$helperDirty = true;
							}
							else if ('stakeChanged' == $action['name']) {
								$helper->stake = $action['stake'];
								$helperDirty = true;
							}
							else {
								$clientCmds[] = $action;
								//$actions[] = "tick.deleteBet({$action['bet']}, {$action['column']}, {$action['rate']});";
							}
						}
						if (!empty($clientCmds))
							$actions[] = 'tick.executeCommands(' . Zend_Json::encode($clientCmds) . ');';
					}
				}
				if ($helperDirty) {
					$helper->computeAggregates();
					$actions[] = 'tick.updateFromServer({ticket:' . Zend_Json::encode($helper->getExposedData()) . '}, false, false); tick.SetBet();';
				}
				if (empty($errors) && false !== $generalError)
					$errors[] = $generalError;
				$this->view->prove['error'] = $errors;
				$this->view->prove['warning'] = $warnings;
				$this->view->prove['action'] = $actions;
				$this->view->backButton = 'ticket_to_correct';
			}

			if ($enqueued) {
				$this->view->prove['message'] = $translate->trans( 'ticket_queued', 'TICKET', It6_Translate_Web::DICTIONARY );
				$this->view->prove['script'] = 'tick.confirmationStart(' . $couponId . ', true)';
			}

			$this->setViewCurrency();

			if ( !empty($this->view->createTicket['rateAdvance']) && $this->view->createTicket['rateAdvance'] > 1 ) {
				$this->view->rateAdvanceCost = $ws->Campaign->get(
					'PreferenceRate',
					'spend',
					'Cost',
					array('preferenceSize' => round((100 * ($this->view->createTicket['rateAdvance'] - 1))) ));
			}
			//echo "<pre>";print_r($this->view->createTicket);exit;
		}

		$this->view->starting = (self::STEP_CONFIRM1 == self::$step);
		Models_BasicRender::render($this->view,$this->_request);

	}

	public function confirm2Action() {
		It6_GlobalCache::turnOff();
		It6_GlobalCache_Invalidator::invalidateTicketFrame();
		$start = $this->getRequest()->getParam('start');
		self::$step = (empty($start) ? self::STEP_CONFIRM2_2 : self::STEP_CONFIRM2_1);
		self::$couponId = $this->getRequest()->getParam('couponId');
		self::$createTicket = true;
		$this->view->backButton = false;
		$this->view->confirmButton = false;
		$this->confirm1Action();
	}

	/**
	 * Confirm preapproved coupon
	 */
	public function confirm3Action() {
		It6_GlobalCache::turnOff();
		It6_GlobalCache_Invalidator::invalidateTicketFrame();
		self::$step = self::STEP_CONFIRM3;
		self::$couponId = $this->getRequest()->couponId;
		self::$createTicket = (0 != $this->getRequest()->confirm);
		self::$preapproved = true;
		$this->view->backButton = false;
		$this->view->confirmButton = false;
		$this->confirm1Action();
	}

	public function liveonlineAction() {

		if ( $this->_request->getParam('1') == 1 ) {
			$this->view->s = 1;
			$this->view->bet = Models_LiveBetting_Calendar::getOnLine();
		}
		else if( $this->_request->getParam('1') == 2 ) {
			$this->view->s = 2;
			$this->view->bet = Models_LiveBetting_Calendar::getComming();
		}
		else if ( $this->_request->getParam('1') == 3 ) {
			$this->view->s = 3;
			$this->view->info = Models_LiveBetting_Match::renderLiveMatch(1);
		}
		else if ( $this->_request->getParam('1') == 4 ) {
			$this->view->s = 4;
			$this->view->rate = Models_LiveBetting_Match::renderLiveMatch(2);
		}
	}

	public function captchaAction() {
		It6_GlobalCache::turnOff();
		$name = $this->_request->getParam('name');
		if (empty($name))
			$name = 'default';
		$captcha = Models_Helpers_Captcha::create($name);
		$captcha->generate();
		Models_Helpers_Captcha::initView($this->view, $captcha);
	}

	public function maxicombinatorDetailAction() {
		$this->_helper->layout->setLayout('popup');
		It6_GlobalCache::turnOff();
		if ( !Models_Ajax_Ticket::get($this->view) )
			return;
		$this->setViewCurrency();
	}

	public function clientCommandAction() {
		$commands = array('commands' => self::$commands );
		$this->view->mixedResponse = self::$mixedResponse;
		$this->view->jsonCommands = Zend_Json::encode($commands);
	}

	public function ticketsOfMonthAction() {
			$tickets = array();
		$this->view->month = $tickets;
	}

	public function lastMinuteTabAction() {
		$bets = Models_Markets_MarketData::loadLastMarkets();
		$now = time();
		$tm = $now + 5 * 60;
		$columns = array(
			// subtype => list of three/two columns
			23 => array(138, 139, 140), // 1X2
			218 => array(1857, 1858, 1859), // 1X2/1X12X2 only 1X2 columns
			29 => array(152, 153), // 12
		);
		$ids = array();
		foreach ($bets as $betId => &$bet) {
			if ($bet['timestamp'] < $tm)
				$tm = $bet['timestamp'];
			$ids[$betId] = true;
			$subtypeId = $bet['podtyp_id'];
			$bet['usedColumns'] = (isset($columns[$subtypeId]) ? $columns[$subtypeId] : $columns[23]);
		}
		$ttl = max(60, $tm - $now);
		It6_GlobalCache::maxExpiration($ttl);
		It6_GlobalCache::setKey(It6_GlobalCache::KEY_PREFIX_LAST_MINUTE_BETS, implode(';', array_keys($ids)), $ttl);
		$this->view->last = $bets;
	}

	public function ternoTabAction() {
		$bets = It6_Models_Markets::loadTernoMarkets();
		$now = time();
		$tm = $now + 15 * 60;
		$columns = array(
			// subtype => list of three/two columns
			23 => array(138, 139, 140), // 1X2
			218 => array(1857, 1858, 1859), // 1X2/1X12X2 only 1X2 columns
			29 => array(152, 153), // 12
		);
		$ids = array();
		foreach ($bets as $betId => &$bet) {
			if ($bet['timestamp'] < $tm)
				$tm = $bet['timestamp'];
			$ids[$betId] = true;
			$subtypeId = $bet['podtyp_id'];
			$bet['usedColumns'] = (isset($columns[$subtypeId]) ? $columns[$subtypeId] : $columns[23]);
		}
		$ttl = max(60, $tm - $now);
		It6_GlobalCache::maxExpiration($ttl);
		It6_GlobalCache::setKey(It6_GlobalCache::KEY_PREFIX_TERNO_BETS, implode(';', array_keys($ids)), $ttl);
		$this->view->terno = $bets;
	}

	public function sportsbookAction() {
		$this->view->isAjax = true;
		//Models_Helpers_Panels::timeFilter($this->view);

		$menu = Models_Helpers_Panels::sportMenu($this->view, true);
		$openUrlArr = $menu->getOpenUrl();

		$sport		= trim( $menu->getUrl($openUrlArr['sport'], 1), '/');
		$oblast		= trim( $menu->getUrl($openUrlArr['oblast'], 2), '/');
		$udalost	= trim( $menu->getUrl($openUrlArr['udalost'], 3), '/');


		Models_Markets_MarketData::isAjax(true);
		Models_Markets_MarketData::init($openUrlArr, null, Models_Markets_MarketData::MARKET_DATE_ORDER_DIRECTION, $this->view->timeFilter);
		//$this->view->type = Models_Markets_MarketData::getTypes($this->view->timeFilter);
		$this->view->odds = Models_Markets_MarketData::getOdds();
	}

	public function sportMenuAction($allAreas = false) {
		Models_Helpers_Panels::timeFilter($this->view);
		$menu = Models_Helpers_Panels::sportMenu($this->view, false);
		
		$openUrlArr = $menu->getOpenUrl();
		$sport		= $menu->getUrl($openUrlArr['sport'], 1);
		$oblast		= $menu->getUrl($openUrlArr['oblast'], 2);
		$udalost	= $menu->getUrl($openUrlArr['udalost'], 3);
		
		$ws = Zend_Registry::get('ws');
		$this->view->maxAreas = $ws->Parameter->getGlobalParameter('web.AreasCount');

		$this->view->rangeValues =  Models_Helpers_Panels::getRangeValues();
		
		if ($allAreas == true || !empty($oblast)){
			$this->view->showAllAreas = true;
		}
		else{
			$this->view->showAllAreas = false;
		}
		$urlParts = array(
			$openUrlArr['sport'] => 1,
			$openUrlArr['oblast'] => 2,
			$openUrlArr['udalost'] => 3
		);
		$this->view->urlParams = $menu->getAllLangUrlParams($urlParts);
	}

	public function sportMenuAllAction() {		
		$this->_helper->viewRenderer('sportMenu');
		$this->sportMenuAction(true);
	}

	public function branchDetailsAction() {
		$ws = Zend_Registry::get('ws');

		$extensions = array();
		$cols = array('branchId', 'place','zip','street','town','latitude','longitude','info');

		$branchId		= $this->getRequest()->getParam('branchId');
		$locationId		= $this->getRequest()->getParam('locationId');
		$townName		= $this->getRequest()->getParam('townName');
		$maxLat			= $this->getRequest()->getParam('maxLat');
		$maxLng			= $this->getRequest()->getParam('maxLng');
		$minLat			= $this->getRequest()->getParam('minLat');
		$minLng			= $this->getRequest()->getParam('minLng');

		$extensions = array();
		$filterDef = array(
			array('?'=> array('branchId' => array(It6_Models_Branch::ID_INTERNET,It6_Models_Branch::ID_INTERNET_LIVE)), 'OP' => 'NOT IN (?)'),
			array('?'=> array('isTesting' => '0'), 'OP' => '='),
			array('?'=> array('isListed' => '1'), 'OP' => '='),
			array('?'=> array('isActive' => '1'), 'OP' => '='),
		);
		if($this->getRequest()->getParam('topOnly') == 'true')
			$filterDef[] = array('?' => array('isTop' => '1'), 'OP' => '=');
		if(!empty($branchId))
			$filterDef[] = array('?'=> array('branchId' => $branchId), 'OP' => '=');
		else if(!empty($locationId))
			$filterDef[] = array('?'=> array('branchLocationId' => $locationId), 'OP' => '=');
		if(!empty($townName))
			$filterDef[] = array('?'=> array('town' => $townName), 'OP' => '=');
		if(!empty($maxLat))
			$filterDef[] = array('?'=> array('latitude' => $maxLat), 'OP' => '<=');
		if(!empty($minLat))
			$filterDef[] = array('?'=> array('latitude' => $minLat), 'OP' => '>=');
		if(!empty($maxLng))
			$filterDef[] = array('?'=> array('longitude' => $maxLng), 'OP' => '<=');
		if(!empty($minLng))
			$filterDef[] = array('?'=> array('longitude' => $minLng), 'OP' => '>=');
		$extensions[] = new It6_WsExtension_Client_Filter('def-filter', $filterDef);


		$extensions[]	= new It6_WsExtension_Client_Order('def-order', array('town', 'street'));
		$branches		= $ws->ext($extensions)->Branch->getAll();
		$branches		= It6_ArrayWrapper::toNativeArray($branches);

		$this->view->branches	= $branches;
		$this->view->detailZoom	= Models_Branch::getMapZoom('detail');
		
		$this->render('branch/common-branch-details', null, true);
	}

	public function ticketGameAction() {
		$gameId = $this->_request->getParam('g');
		if (empty($gameId)) {
			return;
		}
		$ws = Zend_Registry::get('ws');
		$game = $ws->Campaign->getTicketGame($gameId);
		if (empty($game['gameVisible'])) {
			return;
		}
		$handlerClass = $game['handlerClass'];
		$handler = new $handlerClass($game);
		$handler->prepareView($this->view);
		$suffix = strtolower($handlerClass);
		$this->render("ticket-game-$suffix");
	}

	public function validateFormAction() {
		$this->view->form = key($this->getRequest()->getPost());
		if (isset($_POST['registrationNextstep'])) {
			$this->view->isValid=  Models_Helpers_RegistrationNextstep::validate($this->view, true);
		} else {
			$this->view->isValid=Models_Helpers_Registration::validate($this->view, true);  
		}
	}

	public function getTownsAction() {
		$ws = Zend_Registry::get('ws');
		$zipCode = $this->getRequest()->getParam('psc');
		
		$extensions = array();
		$extensions[] = new It6_WsExtension_Client_Order('def-order', array('town', 'townPart'));
		
		$towns = $ws->ext($extensions)->ZipCode->getTownsByZipCode($zipCode);
		$this->view->towns = It6_ArrayWrapper::toNativeArray($towns);
	}
	
	public function supertipTabAction() {	
		$bets = It6_Models_Markets::loadSupertipMarkets();
		$now = time();
		$tm = $now + 15 * 60;
		$columns = array(
			229 => array(2030, 2031, 2032), // 1X2 supertip
		);
		$ids = array();
		foreach ($bets as $betId => &$bet) {
			if ($bet['timestamp'] < $tm)
				$tm = $bet['timestamp'];
			$ids[$betId] = true;
			$subtypeId = $bet['podtyp_id'];
			$bet['usedColumns'] = (isset($columns[$subtypeId]) ? $columns[$subtypeId] : $columns[229]);
		}
		$ttl = max(60, $tm - $now);
		It6_GlobalCache::maxExpiration($ttl);
		It6_GlobalCache::setKey(It6_GlobalCache::KEY_PREFIX_SUPERTIP_BETS, implode(';', array_keys($ids)), $ttl);
		$this->view->supertip = $bets;
	}

	public function ticketPinAction() {
		$_SESSION['ticketPin'] = $_POST['ticketPin'];
		It6_GlobalCache_Invalidator::invalidateTicketFrame();
	}

	public function filterButtonAction() {
		It6_GlobalCache::turnOff();
		$this->render('filter-button');
	}

	public function lightboxAction() {
		It6_GlobalCache::turnOff();

		if (empty($_COOKIE["lightboxDisplayed"])) {
			$t = localtime(time() + 24*3600, true);
			setcookie("lightboxDisplayed", 1, mktime(0, 0, 0, $t['tm_mon'] + 1, $t['tm_mday'], $t['tm_year'] + 1900), '/', WEBHOST);
			$this->view->displayLightBox = 1;
		}
		else
			$this->view->displayLightBox = 0;
		
	}
}