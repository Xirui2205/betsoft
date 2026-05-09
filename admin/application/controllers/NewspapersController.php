<?php

class NewspapersController extends It6_Controller_Abstract {

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 20);
	private $orderData		= array('validTo DESC');
	
	private static $TBODY_LAYOUT = 'newspapers-tbody';

	protected $_redirector = null;

	public function init(){
		parent::init();
		$this->_redirector = $this->_helper->getHelper('Redirector');
	}

	public function indexAction() {
		$ws = Zend_Registry::get("ws");
		$inData			= $this->getRequest()->getParams();
		$extensions		= array();
		
		$form = new Models_Form_Newspapers();

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);
		
		
		$filter = new It6_WsForm_Filter(array(
				array(i18n::tr('Valid to from'), 'validToFrom', 'date', array(array('validTo','>=', '?')), 'It6_Validate_Date',null,'start'),
				array(i18n::tr('Valid to to'), 'validToTo', 'date', array(array('validTo','<=','?')), 'It6_Validate_Date',null,'end'),
		));
		$filter->getExtension($this->filterData, $extensions);
		
		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);
		
		//create table
		$table = new It6_WsForm_Table(array(
				array(),
				array(),
				array(i18n::tr('Valid to '), 'validTo'),
				array(i18n::tr('Newspapers'), 'filename')
		));
		$table->getOrderExtension($this->orderData, $extensions);		

		if (isset($inData["new"])) {
			if (!$form->isValid( $this->getRequest()->getPost() )) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				
			}
			else{
				$ret = $ws->Newspapers->insert($this->getRequest()->getPost());
				$this->_redirect('?section=351');

			}
		}
		elseif (isset($inData["delete"])) {
			$ws->Newspapers->delete(key($inData["delete"]));
		    It6_GlobalCache_Invalidator::invalidateFooterFrame();
		}

		//get stem list by WS
		$newspapers = $ws->ext($extensions)->Newspapers->getAll();
		$newspapers = It6_ArrayWrapper::toNativeArray($newspapers);
		
		//$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $newspapers);
		$this->view->form 		= $form;
	}

}