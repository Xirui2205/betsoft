<?php
class MyaccountController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		Models_BasicRender::render($this->view, $this->_request);
		Models_Ajax_Ticket::get($this->view);
		$this->view->addHelperPath('views/helpers', 'My_View_Helper');
		$this->view->leftMenuItems = Models_Helpers_Panels::getPersonalMenuItems();

		if ( $this->view->log != 2 && $this->view->log != 3 ) {
			It6_GlobalCache_Invalidator::invalidateUserFrames();
			header('Location: '.PROTOCOL. $_SERVER['HTTP_HOST']);
			exit;
		}

		It6_GlobalCache::turnOff();
		require_once "class/class.Date.php";
	}

	public function indexAction() {
		$this->_redirect($this->view->urlSet(35)); // muj-ucet/osobni
	}

	public function ticketAction() {
		$this->view->noRight = true;
		$ws = Zend_Registry::get('ws');

		$this->view->info = Models_MyAccount_Ticket::getAllByUser($this->_request->getParams(), $this->view);

		for ( $i = 0; $i < count($this->view->info); ++$i ) {
			if ( empty($this->view->info[$i]['point_type_id']) ) {
				$this->view->info[$i]['currency'] = Zend_Registry::get('mena');
			}
			else {
				$this->view->info[$i]['currency'] = $ws->PointsType->getById(intval($this->view->info[$i]['point_type_id']));
				$this->view->info[$i]['currency'] = $this->view->info[$i]['currency']['name'];
			}
		}
		//commented out by Martin 11.4.2011, all handled by the above method
		//$menu = Models_Helpers_Panels::sportMenu($this->view);
		#WEEK TICKET#
		/*Models_Helpers_Panels::monthTicket($this->view);
		if ( !is_array($this->view->month) || count($this->view->month) == 0 )
			$this->view->month = array();
		*/
	}

	public function slipAction() {
		$this->view->noRight = true;
		
		$handle	= $this->_request->getParam('t');

		Models_MyAccount_Ticket::getTicketByHandleAndUser($this->view, $handle);

		if ( empty($this->view->ticket['state']) ) {
			$this->_forward('not-found','static-page');
			return;
		}
		
		//commented out by Martin 11.4.2011, all handled by the above method
		//$menu	= Models_Helpers_Panels::sportMenu($this->view);
		//Models_Helpers_Panels::monthTicket($this->view);
		//if(!is_array($this->view->month) || count($this->view->month) == 0) $this->view->month = array();
		#END WEEK TICKET#
		#Live Calendar small#

		/*
		if ( empty($this->view->info[0]['helper']->pointType) ) {
			$this->view->currency = Zend_Registry::get('mena');
		}
		else {
			$this->view->currency = $ws->PointsType->getById(intval($this->view->info[0]['helper']->pointType));
			$this->view->currency = $this->view->currency['name'];
		}
		*/
	}

	public function liveSlipAction() {
		$this->view->noRight = true;
		It6_GlobalCache::turnOff();

		$handle	= $this->_request->getParam('t');
		//Models_Helpers_Panels::leftAndRightColAndNews($this->view);

		//$ws = Zend_Registry::get('ws');
		//$userId = Zend_Registry::get('user_id');
		Models_MyAccount_LiveTicket::getTicketByHandleAndUser($this->view, $handle);
		//$this->view->ticket = $ws->Livebetting->getByHandle($handle);

		//if ( empty($this->view->ticket) || $this->view->ticket->userId != $userId )
		//	throw new Exception('Invalid ticket handle.');

		//$this->view->ticket->bets = $ws->Livebetting->getBets($this->view->ticket->id);
	}

	public function personalAction() {
		$this->view->noRight = true;
		$this->view->banks = Zend_Registry::get('ws')->Bank->getAllOrder(array('bank_code'));

		if( isset( $_POST['submit'] ) ) {
			Models_Helpers_Personal::validate($this->view);
		} else {
			Models_Helpers_Personal::read($this->view);
		}
	}

	public function liveTicketAction() {
		$this->view->noRight = true;
		It6_GlobalCache::turnOff();

		$ws = Zend_Registry::get('ws');

		$this->view->info = Models_MyAccount_LiveTicket::getAllByUser($this->_request->getParams(), $this->view);
		$this->view->noRight = true;
		
		for ( $i = 0; $i < count($this->view->info); ++$i ) {
			// if ( empty($this->view->info[$i]['point_type_id']) ) {
				$this->view->info[$i]['currency'] = Zend_Registry::get('mena');
			// } else {
			//	$this->view->info[$i]['currency'] = $ws->PointsType->getById(intval($this->view->info[$i]['point_type_id']));
			//	$this->view->info[$i]['currency'] = $this->view->info[$i]['currency']['name'];
			//}
		}

		/* $ws = Zend_Registry::get('ws');
		$userId = Zend_Registry::get('user_id');
		$this->view->tickets = $ws->Livebetting->getAllWhereOrder(
			array('userId = ?' => $userId),
			array('timeCreated DESC')); */
	}

	/*public function transactionsAction() {

		$pageSize = 20;
		$page = $this->_request->getParam('page');

		$this->view->transactions
			= It6_ArrayWrapper::toNativeArray(
				Models_MyAccount_Transactions::getUserTransactions());

		$this->view->transactionsPage
			= array_slice($this->view->transactions, $page*$pageSize, $pageSize);

		$this->view->transactionTypes
			= Models_MyAccount_Transactions::getUserTransactionTypes();

		$this->view->pagging
			= Models_Helpers_Pagging::page(count($this->view->transactions),$pageSize,$page);
	}*/

	/*public function withdrawRequestAction() {
		if( isset( $_POST['submit'] ) ) {
			Models_MyAccount_WithdrawRequest::submit($this->view);
		}
	}*/

	public function printSlipAction() {
		$this->view->print = true;
		$this->_helper->layout->setLayout('popup');
		$handle	= $this->_request->getParam('t');
		Models_MyAccount_Ticket::getTicketByHandleAndUser($this->view, $handle);
	}

	public function printLiveSlipAction() {
		$this->view->print = true;
		$this->_helper->layout->setLayout('popup');
		$handle	= $this->_request->getParam('t');
		Models_MyAccount_LiveTicket::getTicketByHandleAndUser($this->view, $handle);
	}

	public function entryBonusAction() {
		$this->view->noRight = true;
		$userId = (Zend_Registry::isRegistered('user_id') ? Zend_Registry::get('user_id') : 0);
		$this->view->bonus = (empty($userId) ? false : Zend_Registry::get('ws')->Campaign->getEntryBonusInfo($userId));
	}

	public function cancelTicketAction() {
		if ( ALLOW_WEB_CANCEL_TICKET == 0 ) {
			throw new Exception('Ticket cancel is not alowed from the web.');
		}
		$this->view->noRight = true;
		$this->view->form = Models_MyAccount_CancelTicket::buildForm($this->view);
		if( isset( $_POST['submit'] ) ) {
			Models_Myaccount_CancelTicket::submitForm($this->view);
			$this->view->form = Models_Myaccount_CancelTicket::buildForm($this->view);
		}
	}

	public function settingLimitsAction() {
		$this->view->noRight = true;
		$this->_helper->layout->setLayout('default');
		$form = new Models_Form_SettingLimits();

		$isSubmit = $this->getRequest()->getPost('submit');
		if (!empty($isSubmit)) $inData = $this->getRequest()->getPost();

		if (!empty($inData)) {

			if ($form->isValid($inData)) {
				$values = $form->getValues();
				$values["userId"] = Zend_Registry::get('user_id');
				$values["timeFrom"] = It6_Date::dbNow();
				$values["timeTo"] = It6_Date::toDb($values["timeTo"]);

				try {
					Zend_Registry::get('ws')->SettingLimits->insertUserLimits($values);

					$msg = Zend_Registry::get('translate')->trans('limit_insert_ok');
					$msg = sprintf($msg, $values['limitAmount'], $values['timeTo']);
					$this->view->result = It6_FeedbackMsg::printNotice($msg, false);

					It6_Log::info(
						"User [%userId%] set new limits.",
						It6_Log::TAG_USER_OPERATION,
						array(
							'userId' => $values['userId'],
							'Data' => Zend_Json::encode($values),
						)
					);
					$this->render('resultLimits');
				}
				catch( Exception $e ) {
					$this->view->result = It6_FeedbackMsg::printError( array('insert-error: '.$e->getMessage()) );
					It6_Log::err(
						"User limits setting was not inserted.",
						It6_Log::TAG_USER_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			}

		}

		$userLimits = Zend_Registry::get('ws')->SettingLimits->getUserLimits(Zend_Registry::get('user_id'));
		if (!empty($userLimits)) {
			$userLimits = It6_ArrayWrapper::toNativeArray($userLimits);
			//$userLimits['timeFrom'] = It6_Date::fromDb($userLimits['timeFrom']);
			$userLimits['timeTo'] = It6_Date::fromDb($userLimits['timeTo']);

			if ($userLimits['limitAmount'] == $userLimits['actualAmount']) 
				$this->view->reached = It6_FeedbackMsg::printError('amount_limit_reached');

			$form->populate($userLimits);
			$msg = Zend_Registry::get('translate')->trans('actual_limits_info');
			$msg = sprintf($msg, $userLimits['timeTo'], $userLimits['actualAmount']);
			$this->view->result = It6_FeedbackMsg::printNotice($msg, false);
		}
		$this->view->form = $form;
	}
}