<?php
class AffiliateBannerController extends It6_Controller_Abstract {

	protected $indexSectionId = 380;
	protected $viewSectionId = 381;
	protected $insertSectionId = 382;
	protected $editSectionId = 383;
	protected $viewImageSectionId = 384;

	protected $ws;
	protected $where;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 10);
	private $orderData		= array('affiliateBannerId');

	private $TBODY_LAYOUT = 'affiliate-banner-tbody';
	private $THEAD_LAYOUT = 'affiliate-banner-thead';


	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		if (!isset($this->affiliateBanner))
			$this->affiliateBannerId = $this->getRequest()->getParam('affiliateBannerId');

		$this->jsIncludes->affiliateBannerAjax = true;
		$this->jsIncludes->commonAjax = true;

		$this->view->indexSectionId	= $this->indexSectionId;
		$this->view->editSectionId = $this->editSectionId;
		$this->view->insertSectionId = $this->insertSectionId;
		$this->view->viewSectionId	= $this->viewSectionId;
		$this->view->viewImageSectionId	= $this->viewImageSectionId;

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
			array('id', 'affiliateBannerId'),
			array('article_title', 'imageTitle'),
			array('file_name', 'fileName'),
			array('ALT', 'alt'),
			array('type_open', 'target'),
			array('valid_from', 'validFrom'),
			array('valid_to', 'validTo'),
		));
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);

		//get type list by WS
		$affiliateBanners = $this->ws->ext($extensions)->AffiliateBanner->getAll();
		$affiliateBanners = It6_ArrayWrapper::toNativeArray($affiliateBanners);

		//$this->view->filter	= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout($this->THEAD_LAYOUT, $this->orderData);
		$this->view->tBody		= $table->getTbodyLayout($this->TBODY_LAYOUT, $affiliateBanners);
	}


	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$affiliateBanner = Zend_Registry::get('ws')->AffiliateBanner->getById($this->affiliateBannerId);
		$this->view->affiliateBanner = $affiliateBanner;
	}


	public function viewImageAction() {
		$this->_helper->layout->setLayout('empty');
		$affiliateBanner = Zend_Registry::get('ws')->AffiliateBanner->getById($this->affiliateBannerId);
		$this->view->affiliateBanner = $affiliateBanner;
	}


	public function editAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_AffiliateBanner($this->editSectionId);

		$isSubmit = $this->getRequest()->getPost('submit');
		if (!empty($isSubmit)) $inData = $this->getRequest()->getPost();

		if (!empty($inData)) {
			if ($form->isValid($inData)) {
				$values = $form->getValues();

				$values['adminId'] = $_SESSION['admin'];

				try {
					$this->ws->AffiliateBanner->update($values);
					$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
					It6_Log::info(
						"Affiliate banner '%affiliateBannerId%' was updated.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'affiliateBannerId' => $this->affiliateBannerId,
							'Data' => Zend_Json::encode($values),
						)
					);

					$affiliateBanner = Zend_Registry::get('ws')->AffiliateBanner->getById($this->affiliateBannerId);
					$this->view->affiliateBanner = $affiliateBanner;
					$this->render('view');
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error: '.$e->getMessage()) );
					It6_Log::err(
						"Affiliate banner update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			}
		}

		$affiliateBanner = Zend_Registry::get('ws')->AffiliateBanner->getById($this->affiliateBannerId);
		$affiliateBanner = It6_ArrayWrapper::toNativeArray($affiliateBanner);
		$form->populate($affiliateBanner);
		$this->view->affiliateBanner = $affiliateBanner;
		$this->view->affiliateBannerForm = $form;
	}


	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_AffiliateBanner($this->insertSectionId);
		$data = $this->getRequest()->getPost();

		if (!empty($data)) {
			if (!$form->isValid($this->getRequest()->getPost())) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			} else {
				$values = $form->getValues();
				$values['adminId'] = $_SESSION['admin'];
				$values["validTo"] = It6_Date::toDbAsDate($values['validTo']);
				$values["validFrom"] = It6_Date::toDbAsDate($values['validFrom']);

				try {
					$this->affiliateBannerId = Zend_Registry::get('ws')->AffiliateBanner->insert($values);
					if (!empty($this->affiliateBannerId)) {
						It6_Log::info(
							"Affiliate banner '%affiliateBannerId%' was inserted.",
							It6_Log::TAG_ADMIN_OPERATION,
							array(
								'affiliateBannerId' => $this->affiliateBannerId,
								'newData' => Zend_Json::encode($values),
							)
						);
					}
					$affiliateBanner = Zend_Registry::get('ws')->AffiliateBanner->getById($this->affiliateBannerId);
					$this->view->affiliateBanner = $affiliateBanner;
					$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));
					$this->render('view');
				}
				catch ( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error: ' . $e->getMessage()) );
					It6_Log::notice(
						"Affiliate banner insert error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values), 'message' => $e->getMessage())
					);
				}
			}
		}

		$this->view->affiliateBannerForm = $form;
	}
}