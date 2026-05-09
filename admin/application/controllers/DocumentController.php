<?php

class DocumentController extends It6_Controller_Abstract {

	const INDEX_SECTION_ID = 395;
	const INSERT_SECTION_ID = 396;
	const VIEW_SECTION_ID = 397;
	const UPDATE_SECTION_ID = 398;


	public function init(){

		parent::init();

		$this->view->indexSectionId = self::INDEX_SECTION_ID;
		$this->view->insertSectionId = self::INSERT_SECTION_ID;
		$this->view->viewSectionId = self::VIEW_SECTION_ID;
		$this->view->updateSectionId = self::UPDATE_SECTION_ID;

		$this->jsIncludes->commonAjax = true;
		
	}

	public function indexAction() {

		$extensions	= array();
        		
		


		//create table
		$tableCfg = array(
			array(),
            array('name', 'name'),
            array('url', 'url'),
		);
		
		$table = new It6_WsForm_Table($tableCfg);
		$table->getColumnsExtension($extensions);

		if ($this->getRequest()->getParam("delete")){
			$documentId = key($this->getRequest()->getParam("delete"));

			Webservice_Document::delete($documentId);
		}

 		$documents = Webservice_Document::getAll();

		$this->view->tHead		= $table->getTheadLayout();
        $this->view->tBody      = $table->getTbodyLayout("document-tbody", $documents);
	}

	public function insertAction(){
		$this->_helper->layout->setLayout('empty');
		$insertForm = new Models_Form_Document(self::INSERT_SECTION_ID);

		if($this->getRequest()->getPost("submit") == 1) {
			if (!$insertForm->isValid( $this->getRequest()->getPost() )) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				
			}
			else {
				$values = $insertForm->getValues();
				
				if (Webservice_Document::insert($values)) {
					$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error') );
				}
			}
		}

		$this->view->form = $insertForm; 
	}

	public function viewAction() {
	 if($this->getRequest()->getParam("document_id")) {

	 	$this->view->document = Webservice_Document::getById($this->getRequest()->getParam("document_id"));

	 }
	}

	public function updateAction() {
		$updateForm = new Models_Form_Document(self::UPDATE_SECTION_ID);
		
		if($this->getRequest()->getParam("submit")){
			if (!$updateForm->isValid( $this->getRequest()->getPost() )) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				
			}
			else {
				$values = $updateForm->getValues();
				
				if (Webservice_Document::update($values)) {
					$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error') );

				}
			}
		}
		elseif($this->getRequest()->getParam("documentId")) {
			$document = It6_ArrayWrapper::toNativeArray(Webservice_Document::getById($this->getRequest()->getParam("documentId")));
			$updateForm->populate($document);
		}
		$this->view->documentId = $this->getRequest()->getParam("documentId");

		$this->view->form = $updateForm;
	}

}