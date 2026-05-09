<?php

class UserProfileController extends controllers_UserAbstractController {

	const MODE_DEFAULT = 1;
	const MODE_BOOKMAKER = 2;

	protected $updateSectionId		    = 228;
	protected $viewSectionId		    = 227;
	protected $indexSectionId		    = 40;
	protected $insertSectionId		    = 249;
	protected $exportSectionId		    = 299;
	protected $blockClientCardSectionId	= 314;
	protected $indexBmSectionId		    = 318;
	protected $viewBmSectionId		    = 319;
	protected $updateBmSectionId	    = 320;
	protected $banUserSectionId	   		= 395;
	
	protected $userId;
	protected $ws;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('userId ASC');

	private static $TBODY_LAYOUT = 'user-tbody';


	public function init() {
		parent::init();
		if (in_array($this->section, array($this->indexBmSectionId, $this->viewBmSectionId, $this->updateBmSectionId, $this->banUserSectionId,))) {
			// switch to BM sections
			$this->view->indexSectionId = $this->indexSectionId = $this->indexBmSectionId;
			$this->view->viewSectionId = $this->viewSectionId = $this->viewBmSectionId;
			$this->view->updateSectionId = $this->updateSectionId = $this->updateBmSectionId;
			$this->view->banUserSectionId = $this->banUserSectionId = $this->banUserSectionId;
		}
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_CALLCENTRUM))
			$this->view->callcenterLogged = true;
		else 
			$this->view->callcenterLogged = false;
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_SUPERVISION))
			$this->view->supervisionLogged = true;
		else
			$this->view->supervisionLogged = false;
	}



	public function indexAction($mode = self::MODE_DEFAULT) {
		$inData		= $this->getRequest()->getParams();
		$extensions	= array();

		if ( isset($inData['ban_allow_action']) && $inData['ban_allow_action'] != "" || ( isset($inData['ban_allow_user_id']) && $inData['ban_allow_user_id'] != 0) ) {
			if ( isset($inData['selectUser']) ) {
				$userId = $inData['selectUser'];
			} else {
				if ( is_array($inData['ban_allow_user_id']) ) {
					$userId = $inData['ban_allow_user_id'];						
				} else {
					$userId = array($inData['ban_allow_user_id'] => $inData['ban_allow_user_id']);
				}
			}

			if ( $inData['ban_allow_action'] == "bann" ) {
				$this->ws->User->banUsers($userId, $inData['sendEmail'], $inData['sendSMS']);
			} else {
				$this->ws->User->allowUsers($userId, $inData['sendEmail'], $inData['sendSMS']);					
			}
		}

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		// make possible to select user from GET param
		if (!isset($this->filterData['userId'])) {
			$id = intval($this->getRequest()->getParam('user_id'));
			if (!empty($id)) {
				$this->filterData['userId'] = $inData['filter']['userId'] = $id;
			}
		}

		//create filter
		$sexOptions		= array('0'=>i18n::tr('Any'), 'm'=>i18n::tr('Male'), 'f'=>i18n::tr('Female'));
		$langOptions	= Models_Utils::getSelectOptions($this->ws->Language->getAll(), 'languageId', 'name');
		$langOptions[0]	= i18n::tr('Any');
		ksort($langOptions);
		
		$branches = It6_ArrayWrapper::toNativeArray($this->ws->Branch->getAllOrder(array('name')));
		$branches = It6_ArrayWrapper::toAssocArray($branches, 'handle', '%name%'. ' - ' . '%handle% ',array(0 => '--None--'));
		
		$filterCfg = array(
			array('First Name', 'firstName', 'text', array(array('firstName', '=')), 'Zend_Validate_Alnum', array('allowWhiteSpace' => true)),
			array('Last Name', 'lastName', 'text', array(array('lastName', '=')), 'Zend_Validate_Alnum', array('allowWhiteSpace' => true)),
			array('Username', 'username', 'text', array(array('username', '=')), 'Zend_Validate_Alnum'),
			array('Email', 'email', 'text', array(array('email', '=')), 'Zend_Validate_EmailAddress'),
			array('Id', 'userId', 'text', array(array('userId', '=')), 'Zend_Validate_Digit', array('allowWhiteSpace' => false)),
			array('Client card number', 'clientCardNumber', 'text', array(array('clientCardNumber', '=')), 'Zend_Validate_Digit', array('allowWhiteSpace' => false)),
			array('Handle', 'userHandle', 'text', array(array('userHandle', '=')), 'Zend_Validate_Digit', array('allowWhiteSpace' => false)),
			array('Is Testing', 'isTesting', 'checkbox', array(array('isTesting', '='))),
			array('Language', 'langId', 'select', array(array('languageId', '=')), null, null, $langOptions),
			array('Sex', 'sex', 'select', array(array('sex', '=')), null, null, $sexOptions),
			array('Birth Date From', 'birthDateFrom', 'date', array(
				array(array('DATE(?)' => 'birthDate'), '>= DATE(?)')), 'It6_Validate_Date'),
			array('Birth Date To', 'birthDateTo', 'date', array(
				array(array('DATE(?)' => 'birthDate'), '<= DATE(?)')), 'It6_Validate_Date'),
			array('Registration Date From', 'registrationTimeFrom', 'date', array(
				array(array('DATE(?)' => 'registrationTime'), '>= DATE(?)')
			), 'Zend_Validate_Date', array('locale' => Zend_Registry::get('Zend_Locale'))),
			array('Registration Date To', 'registrationTimeTo', 'date', array(
				array(array('DATE(?)' => 'registrationTime'), '<= DATE(?)')), 'It6_Validate_Date'),
			array('Balance From', 'balanceFrom', 'text', array(array('balance', '>')), 'Zend_Validate_Float'),
			array('Balance To', 'balanceTo', 'text', array(array('balance', '<')), 'Zend_Validate_Float'),			
			array(i18n::tr('branch_handle'), 'branchHandle', 'text', array(array('branchHandle', '=')), 'Zend_Validate_Int'),
			array(i18n::tr('branch'), 'branchId', 'select', array(array('branchHandle', '=')), 'Zend_Validate_Int', NULL, $branches),
		);
		if (self::MODE_BOOKMAKER == $mode) {
			array_splice($filterCfg, 3, 1); // removing Email
		}
		$filter = new It6_WsForm_Filter($filterCfg);
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);


		//create table
		$tableCfg = array(
			array(null, null),
			array(null, null),
			array(null, null),
			array('User Id', 'userId'),
			array('First Name', 'firstName'),
			array('Last Name', 'lastName'),
			array('Username', 'username'),
			array('Email', 'email'),
			array('Balance', 'balance'),
			array('Currency', 'currencyName'),
			array('Points', 'mainPointsBalance'),
			array('Birth Date', 'birthDate'),
			array('Registration Date', 'registrationTime'),
			array('Bets', 'winRatio'),
			array('Rating', 'financeRating'),
		//	array('Saldo', null),
			array('user_home_branch', 'branchName'),
		);
		if (self::MODE_BOOKMAKER == $mode) {
			array_splice($tableCfg, 7, 1);
			array_shift($tableCfg);
		}
		$table = new It6_WsForm_Table($tableCfg);
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);

		$users = array();

		if ( $this->getRequest()->getPost('submit') || $this->getRequest()->getPost('paginator') ) {
			if ( !empty($inData) ) {
				$users = $this->ws->ext($extensions)->User->getAll();
				$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
			}
			else {
				$users = array();
				$this->view->notFilter = true;
				$this->view->paginator = false;
			}
			
			$users = It6_ArrayWrapper::toNativeArray($users);
		}

		//TODO: this should be done by WS
		/*foreach($users as $key => $user) {
			$users[$key]['saldo'] = Models_User::getSaldo($user['userId']);
		}*/

		$this->view->shoda		= new UserTrack();
		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(
			self::$TBODY_LAYOUT, $users, array('modeBookmaker' => (self::MODE_BOOKMAKER == $mode))
		);
	}



	public function viewAction($mode = self::MODE_DEFAULT) {
		$this->_helper->layout->setLayout('empty');

		$user 					= $this->ws->User->getById( (integer)$this->userId );
		$user					= It6_ArrayWrapper::toNativeArray($user);
		$user['fsb']			= Models_User::getFsb($this->userId);
		$user['isUserOnline']	= Models_User::isUserOnline($this->userId);
		$user['saldo']			= Models_User::getSaldo($this->userId);

		$this->view->user 			= $user;
		$this->view->maxBetSetup	= Models_User::getUSupplyValue($this->userId, 'max_bet_by');
	}



	public function updateAction($mode = self::MODE_DEFAULT) {
		$this->_helper->layout->setLayout('empty');

		$user				= $this->ws->User->getById( (integer)$this->userId );
		$userProfileForm	= new Models_Form_UserProfile($this->updateSectionId, $this->userId, $mode);
		$user['activationTime'] = It6_Date::fromDb($user['activationTime']);
		$user['birthDate'] = It6_Date::fromDbAsDate($user['birthDate']);

		if ($this->getRequest()->getPost('submit')) {

			if (!$userProfileForm->isValid( $this->getRequest()->getPost() ))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			else {
				$values = $userProfileForm->getValues();
				unset($values['save']);
				if (self::MODE_BOOKMAKER != $mode) {
					$values['activationTime'] = It6_Date::toDb($values['activationTime']);
					$values['birthDate'] = It6_Date::toDbAsDate($values['birthDate']);
				}

				try {
					if ( $this->ws->User->update($values)) {
						$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
						It6_Log::info(
							"User '%userId%' was updated.",
							It6_Log::TAG_ADMIN_OPERATION,
							array('userId' => $this->userId, 'oldData' => Zend_Json::encode($user),'newData'=>  Zend_Json::encode($values))
						);
						$this->_forward(self::MODE_BOOKMAKER == $mode ? 'view-bm' : 'view');
					}
					else {
						$this->view->feedbackMsg = UiUtil::printErrors( array('update-error') );
						It6_Log::err(
							"User update error.",
							It6_Log::TAG_ADMIN_OPERATION,
							array('data'=>  Zend_Json::encode($values))
						);
					}
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error: '.$e->getMessage()) );
					It6_Log::err(
						"User update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			}
		}
		else {
			$userProfileForm->populate(It6_ArrayWrapper::toNativeArray($user));
			if (self::MODE_BOOKMAKER != $mode) {
				$userProfileForm->getElement('currencyId')->setValue($user['currencyId']);
				$userProfileForm->getElement('languageId')->setValue($user['languageId']);
				$userProfileForm->getElement('countryId')->setValue($user['countryId']);
				$userProfileForm->getElement('sex')->setValue($user['sex']);
				$userProfileForm->getElement('sendNewsletter')->setValue($user['sendNewsletter']);
				$userProfileForm->getElement('canWithdraw')->setValue($user['canWithdraw']);
				$userProfileForm->getElement('isForbiden')->setValue($user['isForbiden']);
			}
		}

		$this->view->user = $user;
		$this->view->userProfileForm = $userProfileForm;
	}



	public function insertAction() {
		$this->_helper->layout->setLayout('empty');

		$userProfileForm	= new Models_Form_UserProfile($this->insertSectionId);


		if ($this->getRequest()->getPost('submit')) {

			if (!$userProfileForm->isValid( $this->getRequest()->getPost() )) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				
			}
			else {
				$values = $userProfileForm->getValues();
				unset($values['save']);
				$values['activationTime'] = It6_Date::toDb($values['activationTime']);
				$values['birthDate'] = It6_Date::toDbAsDate($values['birthDate']);
				if ($newUserId = $this->ws->User->insert($values)) {
					$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );
					$this->view->feedbackMsg .= '<br/><button onclick="viewUserDetail('.$newUserId.')">'.i18n::tr('Continue creating user').'</button>';
					It6_Log::info(
						"User '%newUserId%' was inserted.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('newUserId' => $newUserId, 'data'=>  Zend_Json::encode($values))
					);
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error') );
					It6_Log::err(
						"User insert error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			}
		}

		$this->view->userProfileForm = $userProfileForm;
	}

	public function blockClientCardAction() {
		$this->_helper->layout->setLayout('empty');

		if ( !empty($this->userId) )					
			$user = $this->ws->User->getById($this->userId);
		
		$clientCardForm = new Models_Form_BlockClientCard($this->blockClientCardSectionId, $this->userId);
		
						
		if ($this->getRequest()->getPost('submit')) {
			if (!$clientCardForm->isValid( $this->getRequest()->getPost() ))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			else {
				try {
					$values = $clientCardForm->getValues();
										
					$user = $this->ws->User->getById($values['userId']);
					
					$this->ws->User->blockClientCard($values['userId']);
					$this->view->feedbackMsg = UiUtil::printMessages( array('block-client-card-ok') );
					It6_Log::info(
						"User '%userId%' client card '%cardNumber%' was blocked.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('userId' => $values['userId'],'cardNumber' => $user['clientCardNumber'])
					);
					$this->_forward('view');
				}
				catch ( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('block-client-card-error') );
					It6_Log::err(
						"Block client card error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('userId' => $values['userId'],'cardNumber' => $user['clientCardNumber'])
					);
				}
			}
		}
		$this->view->user = $user;
		
		$this->view->blockClientCardForm = $clientCardForm;		
	}


	public function exportAction() {
		$this->_helper->layout->setLayout('empty');

		$userExportForm	= new Models_Form_UserExport($this->exportSectionId);

		if ($this->getRequest()->getPost('submit')) {
			$values	= $userExportForm->isValid($this->getRequest()->getPost()); //zend does not support running getValues() without runing isValid() first
			$values = $userExportForm->getValues();

			//anonymous
			$filterDef[] = array('?'=> array('anonymous' => '1'), 'OP' => '!=');
			if(!empty($values['filter']['active'])) {
				$filterSubDef = array('OP' => 'OR');
				if(in_array('active', $values['filter']['active']))
					$filterSubDef[] = array('?'=> 'activationTime', 'OP' => 'IS NOT NULL');
				if(in_array('notActive', $values['filter']['active']))
					$filterSubDef[] = array('?'=> 'activationTime', 'OP' => 'IS NULL');
				$filterDef[] = $filterSubDef;
			}
			if(!empty($values['filter']['testing'])) {
				$filterSubDef = array('OP' => 'OR');
				if(in_array('testing', $values['filter']['testing']))
					$filterSubDef[] = array('?'=> array('isTesting' => 'test'), 'OP' => '=');
				if(in_array('notTesting', $values['filter']['testing']))
					$filterSubDef[] = array('?'=> array('isTesting' => 'ne'), 'OP' => '=');
				$filterDef[] = $filterSubDef;
			}
			if(!empty($values['filter']['forbiden'])) {
				$filterSubDef = array('OP' => 'OR');
				if(in_array('forbiden', $values['filter']['forbiden']))
					$filterSubDef[] = array('?'=> array('isForbiden' => '1'), 'OP' => '=');
				if(in_array('notForbiden', $values['filter']['forbiden']))
					$filterSubDef[] = array('?'=> array('isForbiden' => '1'), 'OP' => '!=');
				$filterDef[] = $filterSubDef;
			}
			if(!empty($values['filter']['activatedFrom']))
				$filterDef[] = array('?'=> array('activationTime' => It6_Date::toDbAsDate($values['filter']['activatedFrom'])), 'OP' => '>=');
			if(!empty($values['filter']['activatedTo']))
				$filterDef[] = array('?'=> array('activationTime' => It6_Date::toDbAsDate($values['filter']['activatedTo'])), 'OP' => '<=');

			if(!empty($values['filter']['registrationFrom']))
				$filterDef[] = array('?'=> array('registrationTime' => It6_Date::toDbAsDate($values['filter']['registrationFrom']) . " 00:00:00"), 'OP' => '>=');
			if(!empty($values['filter']['registrationTo']))
				$filterDef[] = array('?'=> array('registrationTime' => It6_Date::toDbAsDate($values['filter']['registrationTo']) . " 23:59:59"), 'OP' => '<=');

			if(!empty($values['filter']['branchText']))
				$filterDef[] = array('?' => array('branchHandle' => $values['filter']['branchText']), 'OP' => '=');
			if(!empty($values['filter']['branchHandle']))
				$filterDef[] = array('?' => array('branchHandle' => $values['filter']['branchHandle']), 'OP' => '=');
			
			$selectedCols = array();
			foreach($values['columns'] as $colName => $colValue) {
				if($colValue == 1)
					$selectedCols[] = $colName;
			}

			$extensions[] = new It6_WsExtension_Client_Filter('filter', $filterDef);
			$extensions[] = new It6_WsExtension_Client_Columns('columns', $selectedCols);
			
			$users	= $this->ws->ext($extensions)->User->getAll();
			$users	= It6_ArrayWrapper::toNativeArray($users);

			$aUsers =array();
			foreach($users as $key=>$value) {
				foreach($value as $user) {
					if ( is_array($user) ) {
						$aUsers[$key][] = $user[0]["balance"];
					} else {
						$aUsers[$key][] = $user;
					}
				}
			}

			$scvFile	= fopen('php://output', 'w');
			$colNames	= implode(',', $selectedCols);
			
			ob_start();
			fwrite($scvFile, $colNames.PHP_EOL);
			foreach($aUsers as $user) {
				fputcsv($scvFile, $user);
			}
			
			$csvOutput = ob_get_clean();

			//ca not use zend headers as it send malformed Content-Disposition and it causes errors in chromium 16
			//$this
				//->getResponse()
				//->clearAllHeaders()
				//->setHeader('Content-Disposition: attachment; filename="users.csv"')
				//->sendHeaders()
				//->setBody($csvOutput)
				//->sendResponse();
				
			header('Content-type: application/force-download');
			header('Content-Disposition: attachment; filename="users.csv"');
			header('Content-Transfer-Encoding: binary');
			echo $csvOutput;
			


			It6_Log::info(
				"User list was exported.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('data'=>  Zend_Json::encode($values))
			);

			exit(1);
		}

		$this->view->userExportForm = $userExportForm;
	}

	public function indexBmAction() {
		$this->indexAction(self::MODE_BOOKMAKER);
	}

	public function viewBmAction() {
		$this->viewAction(self::MODE_BOOKMAKER);
	}

	public function updateBmAction() {
		$this->updateAction(self::MODE_BOOKMAKER);
	}

}
