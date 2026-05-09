<?php

class MissingTranslationController extends It6_Controller_Abstract {

	protected $indexSectionId = 274;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('translateKey ASC');

	private static $TBODY_LAYOUT = 'missing-translate-tbody';



	public function init() {
		parent::init();
		$this->view->indexSectionId = $this->indexSectionId;
	}



	public function indexAction() {
		$ws			= Zend_Registry::get('ws');
		$inData		= $this->getRequest()->getPost();
		$extensions	= array();

		if(isset($inData['delete'])) {
			$deleteKey = array_keys($inData['delete']);
			if($ws->MissingTranslation->delete(reset($deleteKey)))
				$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error'));
		}


		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);


		//create filter
		$langOptions	= Models_Utils::getSelectOptions($ws->Language->getAll(), 'languageId', 'name');
		$langOptions[0]	= I18n::tr('Any');
		ksort($langOptions);

		$filter = new It6_WsForm_Filter(array(
			array('Translate Key', 'translateKey', 'text', array(array('translateKey', 'LIKE', '%?%'))),
			array('Language', 'languageId', 'select', array(array('languageId', '=')), null, null, $langOptions)
		));
		$filter->getExtension($this->filterData, $extensions);


		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);


		//create table
		$table = new It6_WsForm_Table(array(
			array(),
			array('Translate Key', 'translateKey'),
			array('Language ID', 'languageId'),
			array('Language Name', 'languageName'),
			array('Status', 'status'),
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);


		//get missing translations list by WS
		$translations = $ws->ext($extensions)->MissingTranslation->getAllGrouped();
		$translations = It6_ArrayWrapper::toNativeArray($translations);


		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $translations);
	}
}
