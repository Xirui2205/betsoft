<?php

class ClientCardController extends It6_Controller_Abstract {

	protected $indexSectionId		= 334;
	protected $editSectionId		= 335;
	protected $insertSectionId		= 336;
	protected $blockSectionId		= 336;
	protected $viewSectionId		= 340;
	protected $unblockSectionId		= 341;

	protected $ws;

	protected $where;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 10);
	private $orderData		= array('clientCardId');

	private $TBODY_LAYOUT = 'client-card-tbody';
	private $THEAD_LAYOUT = 'client-card-thead';
	private $editableFields = array('branchId', 'set');


	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		if(!isset($this->cardId)) {
			$this->clientCardId = $this->getRequest()->getParam('clientCardId');
		}

		$this->jsIncludes->clientCardAjax = true;
		$this->jsIncludes->commonAjax = true;

		$this->view->indexSectionId	= $this->indexSectionId;
		$this->view->editSectionId = $this->editSectionId;
		$this->view->insertSectionId = $this->insertSectionId;
		$this->view->blockSectionId	= $this->blockSectionId;
		$this->view->viewSectionId	= $this->viewSectionId;
		$this->view->unblockSectionId	= $this->unblockSectionId;

		$this->view->clientCardId = $this->clientCardId;
	}


	public function indexAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

		if (!isset($inData['filter']['used'])) $inData['filter']['used'] = '1';
		if (!isset($inData['filter']['unused'])) $inData['filter']['unused'] = '1';

		if ($inData['filter']['used'] == 0 && $inData['filter']['unused'] == 0) {
			$this->where = array('clientCardId = ?' => 'null');
		} else {
			$this->where = array('clientCardId <> ?' => 'null');
		}

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		//create filter
		$exts = array(new It6_WsExtension_Client_Filter('non-internet', array('?' => array('typeId' => '1'), 'OP' => '<>')));
		$exts[] = new It6_WsExtension_Client_Order('order', array('name'));
		$branches = $this->ws->ext($exts)->Branch->getAll();
		$branches = It6_ArrayWrapper::toNativeArray($branches);
		$branches = It6_ArrayWrapper::toMultioption($branches, 'branchId', 'name');
		$branchesAnyNone = array('0' => I18n::tr('any'), 'null' => I18n::tr('null')) + $branches;
		$branchesNone = array('none' => I18n::tr('none')) + $branches;

		$filter = new It6_WsForm_Filter(array(
			array('id_new_line_separated', 'clientCardId', array('textarea' => function($data){return explode("\r\n", $data);}), array(array('clientCardId', 'IN (?)'))),
			array('used', 'used', 'checkbox', array(array('used', 'IS NOT', 'NULL')), null, null, null, array('OP', 'OR')),
			array('unused', 'unused', 'checkbox', array(array('used', 'IS', 'NULL')), null, null, null, array('OP', 'OR')),
			array('generated_from', 'generatedFrom', 'dateTime', array(array('generated', '>=')), 'It6_Validate_Date'),
			array('generated_to', 'generatedTo', 'dateTime', array(array('generated', '<=')), 'It6_Validate_Date'),
			array('branch', 'branchId', 'select', array(array('branchId', '=')), null, null, $branchesAnyNone),
		));
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array('id', 'clientCardId'),
			array('generated', 'generated', 'dateTime'),
			array('admin_id', 'adminId'),
			array('branch', 'branchName'),
			array('set', 'set'),
			array('used', 'used', 'dateTime'),
			array('blocked', 'blocked', 'dateTime'),
		));
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);

		//get type list by WS
		$clientCards = $this->ws->ext($extensions)->ClientCard->getAllWhereCond($this->where);
		$clientCards = It6_ArrayWrapper::toNativeArray($clientCards);

		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout($this->THEAD_LAYOUT, $this->orderData);
		$this->view->tBody		= $table->getTbodyLayout($this->TBODY_LAYOUT, $clientCards);
		$this->view->branches	= $branchesNone;
	}


	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$exts = array();
		$clientCard = Zend_Registry::get('ws')->ClientCard->getById($this->clientCardId);
		$this->view->clientCard = $clientCard;
	}


	public function editAction() {
		$this->_helper->layout->setLayout('empty');

		$form = new Models_Form_ClientCard($this->editSectionId);
		$isSubmit = $this->getRequest()->getPost('submit');
		if (!empty($isSubmit)) $inData = $this->getRequest()->getPost();

		if (!empty($inData)) {
			if ($form->isValid($inData)) {
				$values = $form->getValues();

				foreach ($values as $key => $val) {
					if (!in_array($key, $this->editableFields) && $key != 'clientCardId' ) {
						unset($values[$key]);
					}
				}

				$values['adminId'] = $_SESSION['admin'];
				if ($values['branchId'] == 0) $values['branchId'] = new Zend_Db_Expr('NULL');

				try {
					$this->ws->ClientCard->update($values);
					$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
					It6_Log::info(
						"Client card '%clientCardId%' was updated.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'clientCardId' => $this->clientCardId,
							'Data' => Zend_Json::encode($values),
						)
					);

					$clientCard = Zend_Registry::get('ws')->ClientCard->getById($this->clientCardId);
					$this->view->clientCard = $clientCard;
					$this->render('view');
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error: '.$e->getMessage()) );
					It6_Log::err(
						"Clent card update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			}
		}

		$clientCard = Zend_Registry::get('ws')->ClientCard->getById($this->clientCardId);
		$clientCard = It6_ArrayWrapper::toNativeArray($clientCard);
		$clientCard['generated'] = It6_Date::fromDb($clientCard['generated']);
		$clientCard['used'] = It6_Date::fromDb($clientCard['used']);
		$clientCard['blocked'] = It6_Date::fromDb($clientCard['blocked']);
		$clientCard['clientCardIdCopy'] = $clientCard['clientCardId'];
		$form->populate($clientCard);
		$this->view->clientCard = $clientCard;
		$this->view->showBlock = empty($clientCard['blocked']) ? true : false;
		$this->view->clientCardForm = $form;
	}


	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$clientCardInsert = new Models_Form_ClientCardInsert($this->insertSectionId);

		if ($this->getRequest()->getPost('submit')) {
			if (!$clientCardInsert->isValid($this->getRequest()->getPost())) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			} else {
				$values = $clientCardInsert->getValues();
				$values['adminId'] = $_SESSION['admin'];
				$values['generated'] = It6_Date::dbNow();

				try {
					if (is_numeric($this->clientCardId = Zend_Registry::get('ws')->ClientCard->insert($values))) {
						$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));
						It6_Log::info(
							"Client card '%clientCardId%' was inserted.",
							It6_Log::TAG_ADMIN_OPERATION,
							array(
								'clientCardId' => $this->clientCardId,
								'newData' => Zend_Json::encode($values),
							)
						);

						$clientCard = Zend_Registry::get('ws')->ClientCard->getById($this->clientCardId);
						$this->view->clientCard = $clientCard;
						$this->render('view');
					} else {
						$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error: duplicity_primary_key'));
					}
				}
				catch ( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error: ' . $e->getMessage()) );
					It6_Log::notice(
						"Type insert error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values), 'message' => $e->getMessage())
					);
				}
			}
		}

		$this->view->clientCardInsertForm = $clientCardInsert;
	}


	public function blockAction() {
		$this->_helper->layout->setLayout('empty');	

		try {
			if ($this->ws->ClientCard->block($this->clientCardId)) {
				$this->view->feedbackMsg = UiUtil::printMessages(array('client_card_block_ok'));
				It6_Log::info(
					"Client card '%clientCardId%' was blocked.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'clientCardId' => intval($this->clientCardId),
						'adminId' => intval($_SESSION['admin'])
					)
				);
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('client_card_already_blocked'));
			}
			$clientCard = Zend_Registry::get('ws')->ClientCard->getById($this->clientCardId);
			$this->view->clientCard = $clientCard;
			$this->render('view');
		}
		catch ( Exception $e ) {
			$this->view->feedbackMsg = UiUtil::printErrors( array('block-error: ' . $e->getMessage()) );
			It6_Log::notice(
				"Client card block error.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'clientCardId'=> intval($this->clientCardId),
					'message' => $e->getMessage(),
					'adminId' => intval($_SESSION['admin'])
				)
			);
		}
	}


	public function unblockAction() {
		$this->_helper->layout->setLayout('empty');

		try {
			if ($this->ws->ClientCard->unblock($this->clientCardId)) {
				$this->view->feedbackMsg = UiUtil::printMessages(array('client_card_unblock_ok'));
				It6_Log::info(
					"Client card '%clientCardId%' was unblocked.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'clientCardId' => intval($this->clientCardId),
						'adminId' => intval($_SESSION['admin'])
					)
				);
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('client_card_already_unblocked'));
			}
			$clientCard = Zend_Registry::get('ws')->ClientCard->getById($this->clientCardId);
			$this->view->clientCard = $clientCard;
			$this->render('view');
		}
		catch ( Exception $e ) {
			$this->view->feedbackMsg = UiUtil::printErrors( array('unblock-error: ' . $e->getMessage()) );
			It6_Log::notice(
				"Client card block error.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'clientCardId'=> intval($this->clientCardId),
					'message' => $e->getMessage(),
					'adminId' => intval($_SESSION['admin'])
				)
			);
		}
	}


	public function cardsToBranchAction() {
		$this->_helper->layout->setLayout('empty');	
		$inData = $this->getRequest()->getPost();

		try {
			if (!empty($inData['selectedItems'])) {
				if ($inData['cardsToBranchId'] == 'none') $inData['cardsToBranchId'] = new Zend_Db_Expr('NULL');
				if ($this->ws->ClientCard->cardsToBranch($inData['cardsToBranchId'], $inData['selectedItems'])) {
					$this->view->feedbackMsg = UiUtil::printMessages(array(i18n::tr('client_card_branch_binding_ok')));
					It6_Log::info(
						"Client cards were attachet to branch '%branch%'.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'branch' => $inData['cardsToBranchId'],
							'clientCards' => $inData['selectedItems'],
							'adminId' => $_SESSION['admin']
						)
					);
				} else {
					$this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('client_card_is_used')));
				}
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('no_selected_client_cards')));
			}
		}
		catch ( Exception $e ) {
			$this->view->feedbackMsg = UiUtil::printErrors( array('cards_to_branch-error: ' . $e->getMessage()) );
			It6_Log::notice(
				"Attaching client cards to branch failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'message' => $e->getMessage(),
					'branch' => intval($inData['cardsToBranchId']),
					'clientCards' => $inData['selectedItems'],
					'adminId' => intval($_SESSION['admin'])
				)
			);
		}
	}


	public function blockMoreCardsAction() {
		$this->_helper->layout->setLayout('empty');
		$inData = $this->getRequest()->getPost();

		try {
			if (!empty($inData['selectedItems'])) {
				if ($this->ws->ClientCard->blockMoreCards($inData['selectedItems'])) {
					$this->view->feedbackMsg = UiUtil::printMessages(array(i18n::tr('client_cards_block_ok')));
					It6_Log::info(
						"Client cards were blocked.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'clientCards' => $inData['selectedItems'],
							'adminId' => intval($_SESSION['admin'])
						)
					);
				} else {
					$this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('client_cards_block_error')));
				}
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('no_selected_client_cards')));
			}
		}
		catch ( Exception $e ) {
			$this->view->feedbackMsg = UiUtil::printErrors(array('block_more_cards-error: ' . $e->getMessage()));
			It6_Log::notice(
				"Attaching client cards to branch failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'message' => $e->getMessage(),
					'clientCards' => $inData['selectedItems'],
					'adminId' => intval($_SESSION['admin'])
				)
			);
		}
	}


	public function unblockMoreCardsAction() {
		$this->_helper->layout->setLayout('empty');
		$inData = $this->getRequest()->getPost();

		try {
			if (!empty($inData['selectedItems'])) {
				if ($this->ws->ClientCard->unblockMoreCards($inData['selectedItems'])) {
					$this->view->feedbackMsg = UiUtil::printMessages(array(i18n::tr('client_cards_unblock_ok')));
					It6_Log::info(
						"Client cards were unblocked.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'clientCards' => $inData['selectedItems'],
							'adminId' => intval($_SESSION['admin'])
						)
					);
				} else {
					$this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('client_cards_unblock_error')));
				}
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('no_selected_client_cards')));
			}
		}
		catch ( Exception $e ) {
			$this->view->feedbackMsg = UiUtil::printErrors(array('unblock_more_cards-error: ' . $e->getMessage()));
			It6_Log::notice(
				"Attaching client cards to branch failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'message' => $e->getMessage(),
					'clientCards' => $inData['selectedItems'],
					'adminId' => intval($_SESSION['admin'])
				)
			);
		}
	}
}