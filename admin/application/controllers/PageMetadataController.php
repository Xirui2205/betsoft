<?php
class PageMetadataController extends controllers_AdminAbstractController {

	const SECTIONID_INDEX = 310;
	const SECTIONID_VIEW = 311;
	const SECTIONID_UPDATE = 312;

	protected $indexSectionId = self::SECTIONID_INDEX;
	protected $viewSectionId = self::SECTIONID_VIEW;
	protected $updateSectionId = self::SECTIONID_UPDATE;

	private $filterData = array();
	private $paginatorData = array('recsPerPage' => 20);
	private $orderData = array('controllerId ASC');

	private $TBODY_LAYOUT = 'pages-metadata-tbody';
	private $THEAD_LAYOUT = 'pages-metadata-thead';


	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		$this->controllerId = $this->getRequest()->getParam('controllerId');
		$this->langId = $this->getRequest()->getParam('langId');

		$this->jsIncludes->pageMetadataAjax = true;
		$this->jsIncludes->commonAjax = true;

		$this->view->indexSectionId = $this->indexSectionId;
		$this->view->viewSectionId = $this->viewSectionId;
		$this->view->updateSectionId = $this->updateSectionId;

		$this->view->controllerId = $this->controllerId;
		$this->view->langId = $this->langId;
	}


	public function indexAction() {
		$ws = Zend_Registry::get('ws');
		$inData	= $this->getRequest()->getPost();
		$extensions	= array();

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['ccvPaginator']))
			$this->paginatorData = $inData['ccvPaginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		$filter = new It6_WsForm_Filter(array(
			array('controller_id', 'controllerId', 'text', array(array('controllerId', '='))),
			array('lang_id', 'langId', 'text', array(array('langId', '='))),
			array('request_controller', 'requestController', 'text', array(array('requestController', 'LIKE', '%?%'))),
		));
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage'], 'ccvPaginator');
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array('lang_id', 'langId'),
			array('controller_id', 'controllerId'),
			array('request_controller', 'requestController'),
			array('request_action', 'requestAction'),
			array('real_controller', 'realController'),
			array('real_action', 'realAction'),
			array('title', 'title')
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);

		//$pages = $ws->ext($extensions)->ControllerConvert->getAllWhereLimitGroupedByControllerId(array(), true);
		$pages = $ws->ext($extensions)->ControllerConvert->getAll();
		$pages = It6_ArrayWrapper::toNativeArray($pages);

		$this->view->filter = $filter->getLayout(null, $this->filterData);
		$this->view->paginator = $paginator->getLayout(null, null, $extensions['ccvPaginator']->getResponse());
		$this->view->tHead = $table->getTheadLayout($this->THEAD_LAYOUT, $this->orderData);
		$this->view->tBody = $table->getTbodyLayout($this->TBODY_LAYOUT, $pages);
	}


	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$pageMetadata = Zend_Registry::get('ws')->ControllerConvert->getOneWhere(
			array('c_id = ?' => $this->controllerId, 'lang_id = ?' => $this->langId	)
		);
		$this->view->pageMetadata = $pageMetadata;
	}


	public function updateAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_PageMetadata($this->updateSectionId);

		$isSubmit = $this->getRequest()->getPost('submit');
		if (!empty($isSubmit)) $inData = $this->getRequest()->getPost();

		if (!empty($inData)) {
			if ($form->isValid($inData)) {
				$values = $form->getValues();

				try {
					$this->ws->ControllerConvert->updateMetadata($values);
					$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
					It6_Log::info(
						"Page metadata '%controllerId%' was updated.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'controllerId' => $this->controllerId,
							'Data' => Zend_Json::encode($values),
						)
					);

					$pageMetadata = Zend_Registry::get('ws')->ControllerConvert->getOneWhere(
						array('c_id = ?' => $this->controllerId, 'lang_id = ?' => $this->langId	)
					);
					$this->view->pageMetadata = $pageMetadata;
					$this->render('view');

					// promazani cache
					It6_Models_ControllerConvert::clearCache();
					It6_NodeComm::execCommand(It6_NodeComm::CMD_INVALIDATE_CC);
					It6_Memcached::flush();
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error: '.$e->getMessage()) );
					It6_Log::err(
						"Page metadata update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}

			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				$form->populate($inData);
			}
		} else {
			$pageMetadata = Zend_Registry::get('ws')->ControllerConvert->getOneWhere(
				array('c_id = ?' => $this->controllerId, 'lang_id = ?' => $this->langId	)
			);
			$pageMetadata = It6_ArrayWrapper::toNativeArray($pageMetadata);
			$form->populate($pageMetadata);
			$this->view->pageMetadata = $pageMetadata;
		}

		$this->view->pageMetadataForm = $form;
	}
}