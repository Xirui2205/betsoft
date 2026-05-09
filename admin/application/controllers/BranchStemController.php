<?php
class BranchStemController extends It6_Controller_Abstract {
	
	private $indexSectionId		= 326;
	private $insertSectionId	= 327;
	private $updateSectionId	= 329;
	
	protected $stemId;
	protected $ws;
	
	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 20);
	private $orderData		= array('stemId ASC');
	
	private static $TBODY_LAYOUT = 'stem-tbody';
	
	public function init() {
		parent::init();
		$this->jsIncludes->commonAjax	= true;
		$this->ws = Zend_Registry::get('ws');
		
		if(!isset($this->stemId))
			$this->stemId = $this->getRequest()->getParam('stemId');

		$this->view->indexSectionId		= $this->indexSectionId;
		$this->view->insertSectionId	= $this->insertSectionId;
		$this->view->updateSectionId	= $this->updateSectionId;
		
		$this->view->stemId = $this->stemId;
	}
	
	public function indexAction() {
		$inData			= $this->getRequest()->getParams();
		$extensions		= array();
		
		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);
		
		$adminFilter = array(
				'role2 IN (?)' => array('branch-technician', 'branch-admin'),
		);
		$admins = $this->ws->ext($extensions)->Admin->getAllWhereOrder($adminFilter, array('surname'));
		$admins = It6_ArrayWrapper::toAssocArray($admins, 'adminId', '%lastName% '.'%firstName%', array(0 => '--None--'));
		
		$filter = new It6_WsForm_Filter(array(
				array(i18n::tr('stem-name'), 'name', 'text', array(array('name', '='))),
				array(i18n::tr('stem-admin'), 'adminId', 'select', array(array('adminId', '=')), null, null, $admins),
		));
		$filter->getExtension($this->filterData, $extensions);
		
		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);
		
		//create table
		$table = new It6_WsForm_Table(array(
				array(),
				array(i18n::tr('stem-name'), 'name'),
				array(i18n::tr('stem-admin'), 'adminId'),
				array(i18n::tr('stem-desc'), 'desc'),
				array(i18n::tr('stem-filename'), 'fileName'),
				array(i18n::tr('stem-sendmail'), 'sendMail'),
		));
		$table->getOrderExtension($this->orderData, $extensions);
		
		//get stem list by WS
		$stems = $this->ws->ext($extensions)->Stem->getAll();
		$stems = It6_ArrayWrapper::toNativeArray($stems);
		
		
		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $stems);
	}
	
	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$stemForm = new Models_Form_BranchStem($this->insertSectionId, 0, true);
		
		if ($this->getRequest()->getPost('submit')) {
			if (!$stemForm->isValid( $this->getRequest()->getPost() ))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			else {
				$values = $stemForm->getValues();
				unset($values['submit']);
		
				try {
					if ($this->stemId = $this->ws->Stem->insert($values)) {
						$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );
						It6_Log::info(
								"Stem '%stemId%' was inserted.",
								It6_Log::TAG_ADMIN_OPERATION,
								array('stemId' => $this->stemId, 'newData'=>  Zend_Json::encode($values))
						);
					}
					else
						throw new Exception('Something went wrong - stem insertion');
				}
				catch ( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error: ' . $e->getMessage()) );
					It6_Log::notice(
							"Stem insert error.",
							It6_Log::TAG_ADMIN_OPERATION,
							array('data'=>  Zend_Json::encode($values), 'message' => $e->getMessage())
					);
				}
			}
		}
		
		$this->view->stemForm = $stemForm;
	}
	
	public function updateAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_BranchStem($this->updateSectionId, $this->stemId, false);
		
		if ($this->stemId) {
			$data = $this->ws->Stem->getById($this->stemId);
			$data = It6_ArrayWrapper::toNativeArray($data);
			$form->populate(It6_ArrayWrapper::toNativeArray($data));
		}
		
		if ($this->getRequest()->getPost('submit')) {
			$data = $this->getRequest()->getPost();
		
			if ($form->isValid($data)) {
				unset($data['submit']);
		
				if ($this->ws->Stem->update($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('update-ok'));
					It6_Log::notice(
							"Stem update %stem% success.",
							It6_Log::TAG_ADMIN_OPERATION,
							array('stem'=>$data['name'],'data' => $data)
					);
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
					It6_Log::warn(
							"Error updating stem %stem%",
							It6_Log::TAG_ADMIN_OPERATION,
							array('stem'=>$data['name'],'data' => $data)
					);
				}
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));

			$form->populate($data);
		}
		$this->view->form = $form;
	}
}