<?php
class AffiliatePartnerController extends It6_Controller_Abstract {

	protected $indexSectionId = 374;
	protected $viewSectionId = 375;
	protected $insertSectionId = 376;
	protected $editSectionId = 377;
	protected $bannersSectionId = 378;
	protected $assignSectionId = 379;
	protected $userSectionId = 385;
	protected $partnerIndexSectionId = 386;
	protected $partnerViewSectionId = 387;
	protected $partnerBannersSectionId = 388;
	protected $partnerTicketsSectionId = 389;
	protected $partnerUsersSectionId = 390;
	protected $partnerActualProvisionSectionId = 391;

	protected $ws;
	protected $where;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 10);
	private $orderData		= array('affiliatePartnerId');

	private $TBODY_LAYOUT = 'affiliate-partner-tbody';
	private $THEAD_LAYOUT = 'affiliate-partner-thead';

	private $TBODY_LAYOUT_PARTNER = 'partner-tbody';
	private $THEAD_LAYOUT_PARTNER = 'partner-thead';

	private $TBODY_LAYOUT_TICKET_PARTNER = 'ticket-partner-tbody';
	private $THEAD_LAYOUT_TICKET_PARTNER = 'ticket-partner-thead';


	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		if (!isset($this->affiliatePartner))
			$this->affiliatePartnerId = $this->getRequest()->getParam('affiliatePartnerId');

		if (!isset($this->affiliateBanner))
			$this->affiliateBannerId = $this->getRequest()->getParam('affiliateBannerId');

		$this->jsIncludes->affiliatePartnerAjax = true;
		$this->jsIncludes->commonAjax = true;

		$this->view->indexSectionId	= $this->indexSectionId;
		$this->view->viewSectionId	= $this->viewSectionId;
		$this->view->insertSectionId = $this->insertSectionId;
		$this->view->editSectionId = $this->editSectionId;
		$this->view->bannersSectionId = $this->bannersSectionId;
		$this->view->assignSectionId = $this->assignSectionId;
		$this->view->userSectionId = $this->userSectionId;
		$this->view->partnerIndexSectionId = $this->partnerIndexSectionId;
		$this->view->partnerViewSectionId = $this->partnerViewSectionId;
		$this->view->partnerBannersSectionId = $this->partnerBannersSectionId;
		$this->view->partnerTicketsSectionId = $this->partnerTicketsSectionId;
		$this->view->partnerUsersSectionId = $this->partnerUsersSectionId;
		$this->view->partnerActualProvisionSectionId = $this->partnerActualProvisionSectionId;

		$this->view->affiliatePartnerId = $this->affiliatePartnerId;
		$this->view->affiliateBannerId = $this->affiliateBannerId;
	}


	public function indexAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

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

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array('id', 'affiliatePartnerId'),
			array('created', 'created', 'dateTime'),
			array('founded', 'adminName'),
			array('name', 'name'),
			array('url_address', 'url'),
		));
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);

		//get type list by WS
		$affiliatePartners = $this->ws->ext($extensions)->AffiliatePartner->getAll();
		$affiliatePartners = It6_ArrayWrapper::toNativeArray($affiliatePartners);

		//$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout($this->THEAD_LAYOUT, $this->orderData);
		$this->view->tBody		= $table->getTbodyLayout($this->TBODY_LAYOUT, $affiliatePartners);
	}


	public function partnerIndexAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

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

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array('id', 'affiliatePartnerId'),
			array('created', 'created', 'dateTime'),
			array('founded', 'adminName'),
			array('name', 'name'),
			array('url_address', 'url'),
		));
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);

		//get type list by WS
		$identity = It6_Session_Admin::getUserData();
		$partner = Webservice_Admin::getPartnerByAdmin($identity["id"]);
		$where = array('id = ?' => $partner->partnerId);
		$affiliatePartners = $this->ws->ext($extensions)->AffiliatePartner->getAllWhere($where);
		$affiliatePartners = It6_ArrayWrapper::toNativeArray($affiliatePartners);

		$this->view->paginator = $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead = $table->getTheadLayout($this->THEAD_LAYOUT_PARTNER, $this->orderData);
		$this->view->tBody = $table->getTbodyLayout($this->TBODY_LAYOUT_PARTNER, $affiliatePartners);
	}


	public function partnerTicketsAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

		//prepare wsForm data from $_POST
		if (!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if (!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if (!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		$exts = array(new It6_WsExtension_Client_Filter('non-internet', array('?' => array('typeId' => '1'), 'OP' => '<>')));
		$exts[] = new It6_WsExtension_Client_Order('order', array('name'));

		//get type list by WS
		$identity = It6_Session_Admin::getUserData();
		$partner = Webservice_Admin::getPartnerByAdmin($identity["id"]);

		if (empty($partner->partnerId)) {
			$partners = Webservice_AffiliatePartner::getAll();
			$partnersArray = array();
			$partnersArray[null] = I18n::tr('--select--');
			foreach ($partners as $part) $partnersArray[$part["affiliatePartnerId"]] = $part["name"];
		}

		$users = Webservice_AffiliatePartner::getRegisteredUsers($partner->partnerId);
		$provisions = array(null => I18n::tr('--select--'), 'ano' => I18n::tr('ano'), 'ne' => I18n::tr('ne'));
		$usersIdArray = array();
		$usersNameArray = array();
		$usersNameArray[null] = I18n::tr('--select--');
		foreach ($users as $user) {
			array_push($usersIdArray, $user["user_id"]);
			$usersNameArray[$user["user_id"]] = $user["jmeno"].' '.$user["prijmeni"];
		}

		$filterArray = array(
			array('ticket_id', 'ticketId', 'text', array(array('ticketId', '=')), 'Zend_Validate_Int'),
			array('user_id', 'userId', 'text', array(array('userId', '=')), 'Zend_Validate_Int'),
			array('reg_name', 'regName', 'select', array(array('userId', '=', '?')), null, null, $usersNameArray),
			array('provision_paid_out', 'affiliateProvisionPaidOut', 'select', array(array('affiliateProvisionPaidOut', '=', '?')), null, null, $provisions),
			array('from-date', 'createdTimeFrom', 'dateTime', array(array('createdTime', '>=')), 'It6_Validate_Date'),
			array('to-date', 'createdTimeTo', 'dateTime', array(array('createdTime', '<=')), 'It6_Validate_Date'),
			array('date_from_payout', 'payoutTimeFrom', 'dateTime', array(array('paidOutTime', '>=')), 'It6_Validate_Date'),
			array('date_to_payout', 'payoutTimeTo', 'dateTime', array(array('paidOutTime', '<=')), 'It6_Validate_Date'),
		);

		if (empty($partner->partnerId)) 
			array_push($filterArray , array('partner', 'affiliatePartnerId', 'select', array(array('affiliatePartnerId', '=', '?')), null, null, $partnersArray));

		//create filter
		$filter = new It6_WsForm_Filter($filterArray);
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array('ticket_id', 'ticketId'),
			array('user_id', 'userId'),
			array('reg_name', 'nameOfUser'),
			array('partner', 'affiliatePartnerName'),
			array('ticket_date_created', 'createdTime'),
			array('stake', 'amount'),
			array('ticket_pay_out_date', 'paidOutTime'),
			array('totalWin', 'realWinAmount'),
			array('provision', 'affiliateProvision'),
			array('provision_paid_out', 'affiliateProvisionPaidOut'),
			array('ticket_status', null),
		));
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);

		$where = array(
			't.vyplacen = ?' => 1,
			't.user_id IN (?)' => $usersIdArray,
			'pt.partner_id IS NOT NULL',
		);
		$tickets = $this->ws->ext($extensions)->Ticket->getAllWhere($where);
		$tickets = It6_ArrayWrapper::toNativeArray($tickets);

		$totalAmount = 0;
		$totalWin = 0;
		foreach ($tickets as $ticket) {
			$totalAmount += $ticket["amount"];
			$totalWin += $ticket["realWinAmount"];
		}

		$this->view->totalAmount = $totalAmount;
		$this->view->totalWin = $totalWin;
		$this->view->provision = ($totalAmount - $totalWin)/10;
		$this->view->filter = $filter->getLayout(null, $this->filterData);
		$this->view->paginator = $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead = $table->getTheadLayout($this->THEAD_LAYOUT_TICKET_PARTNER, $this->orderData);
		$this->view->tBody = $table->getTbodyLayout($this->TBODY_LAYOUT_TICKET_PARTNER, $tickets);
	}


	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$affiliatePartner = Zend_Registry::get('ws')->AffiliatePartner->getById($this->affiliatePartnerId);
		$this->view->affiliatePartner = $affiliatePartner;
	}


	public function partnerViewAction() {
		$this->_helper->layout->setLayout('empty');
		$affiliatePartner = Zend_Registry::get('ws')->AffiliatePartner->getById($this->affiliatePartnerId);
		$this->view->affiliatePartner = $affiliatePartner;
	}


	public function editAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_AffiliatePartner($this->editSectionId);

		$isSubmit = $this->getRequest()->getPost('submit');
		if (!empty($isSubmit)) $inData = $this->getRequest()->getPost();

		if (!empty($inData)) {
			if ($form->isValid($inData)) {
				$values = $form->getValues();
				$values['adminId'] = $_SESSION['admin'];

				try {
					$this->ws->AffiliatePartner->update($values);
					$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
					It6_Log::info(
						"Affiliate partner '%affiliatePartnerId%' was updated.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'affiliatePartnerId' => $this->affiliatePartnerId,
							'Data' => Zend_Json::encode($values),
						)
					);

					$affiliatePartner = Zend_Registry::get('ws')->AffiliatePartner->getById($this->affiliatePartnerId);
					$this->view->affiliatePartner = $affiliatePartner;
					$this->render('view');
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error: '.$e->getMessage()) );
					It6_Log::err(
						"Affiliate partner update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}

			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				$form->populate($inData);
			}
		} else {
			$affiliatePartner = Zend_Registry::get('ws')->AffiliatePartner->getById($this->affiliatePartnerId);
			$affiliatePartner = It6_ArrayWrapper::toNativeArray($affiliatePartner);
			$form->populate($affiliatePartner);
			$this->view->affiliatePartner = $affiliatePartner;
		}

		$this->view->affiliatePartnerForm = $form;
	}


	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_AffiliatePartner($this->insertSectionId);

		if ($this->getRequest()->getPost('submit')) {
			if (!$form->isValid($this->getRequest()->getPost())) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			} else {
				$values = $form->getValues();
				$values['adminId'] = $_SESSION['admin'];

				try {
					$this->affiliatePartnerId = Zend_Registry::get('ws')->AffiliatePartner->insert($values);
					It6_Log::info(
						"Affiliate partner '%affiliatePartnerId%' was inserted.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'affiliatePartnerId' => $this->affiliatePartnerId,
							'newData' => Zend_Json::encode($values),
						)
					);
					$affiliatePartner = Zend_Registry::get('ws')->AffiliatePartner->getById($this->affiliatePartnerId);
					$this->view->affiliatePartner = $affiliatePartner;
					$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));
					$this->render('view');
				}
				catch ( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error: ' . $e->getMessage()) );
					It6_Log::notice(
						"Affiliate partner insert error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values), 'message' => $e->getMessage())
					);
				}
			}
		}

		$this->view->affiliatePartnerForm = $form;
	}


	public function bannersAction() {
		$this->_helper->layout->setLayout('empty');
		$banners = Zend_Registry::get('ws')->AffiliatePartner->getAssignedBanners($this->affiliatePartnerId);
		$this->view->banners = It6_ArrayWrapper::toNativeArray($banners);
		$this->view->affiliatePartnerId = $this->affiliatePartnerId;
		if (!empty($this->affiliateBannerId)) {
			$this->view->affiliateBannerId = $this->affiliateBannerId;
			$this->view->codeBanner = Webservice_AffiliateBanner::getById($this->affiliateBannerId);
		}
	}


	public function partnerBannersAction() {
		$this->_helper->layout->setLayout('empty');
		$banners = Zend_Registry::get('ws')->AffiliatePartner->getAssignedBanners($this->affiliatePartnerId);
		$this->view->banners = It6_ArrayWrapper::toNativeArray($banners);
		$this->view->affiliatePartnerId = $this->affiliatePartnerId;
		if (!empty($this->affiliateBannerId)) {
			$this->view->affiliateBannerId = $this->affiliateBannerId;
			$this->view->codeBanner = Webservice_AffiliateBanner::getById($this->affiliateBannerId);
		}
	}


	public function usersAction() {
		$this->_helper->layout->setLayout('empty');
		$users = Zend_Registry::get('ws')->AffiliatePartner->getRegisteredUsers($this->affiliatePartnerId);
		$this->view->users = It6_ArrayWrapper::toNativeArray($users);
		$this->view->affiliatePartnerId = $this->affiliatePartnerId;
	}


	public function partnerUsersAction() {
		$this->_helper->layout->setLayout('empty');
		$users = Zend_Registry::get('ws')->AffiliatePartner->getRegisteredUsers($this->affiliatePartnerId);
		$this->view->users = It6_ArrayWrapper::toNativeArray($users);
		$this->view->affiliatePartnerId = $this->affiliatePartnerId;
	}


	public function assignAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_AffiliatePartnerAssign($this->assignSectionId);

		$isSubmit = $this->getRequest()->getPost('submit');
		if (!empty($isSubmit)) $inData = $this->getRequest()->getPost();

		if (!empty($inData)) {
			if (!$form->isValid($this->getRequest()->getPost())) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			} else {
				$values = $form->getValues();
				try {
					if ($this->ws->AffiliatePartner->assignBanner($values) != false) {
						$this->view->feedbackMsg = UiUtil::printMessages( array('assign-ok') );
						It6_Log::info(
							"Affiliate banner '%affiliateBannerId%' was assigned to affiliate partner '%affiliatePartnerId%'.",
							It6_Log::TAG_ADMIN_OPERATION,
							array(
								'affiliateBannerId' => $values["affiliateBannerId"],
								'affiliatePartnerId' => $this->affiliatePartnerId,
							)
						);
						$banners = Zend_Registry::get('ws')->AffiliatePartner->getAssignedBanners($this->affiliatePartnerId);
						$this->view->banners = It6_ArrayWrapper::toNativeArray($banners);
						$this->view->affiliatePartnerId = $this->affiliatePartnerId;
						unset($this->view->affiliateBannerId);
						$this->render('banners');
					} else {
						$this->view->feedbackMsg = UiUtil::printErrors( array('duplicit key') );
					}
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('assign-error: '.$e->getMessage()) );
					It6_Log::err(
						"Affiliate partner assign error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			}
		}

		$this->view->affiliatePartnerForm = $form;
	}


	public function partnerActualProvisionAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->actualMonth = date('F');
		$this->view->firstDay = date('d.m.Y 00:00:00', strtotime('first day of this month', time()));
		$this->view->lastDay = date('d.m.Y 23:59:59', strtotime('last day of this month', time()));
		$this->view->provision = Webservice_AffiliatePartner::getActualProvision($this->affiliatePartnerId);
	}
}