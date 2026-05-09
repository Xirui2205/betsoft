<?php

class controllers_BranchAbstractController extends It6_Controller_Abstract {

	public function init() {
		$this->ws = Zend_Registry::get('ws');
		$this->branchId = $this->getRequest()->getPost('branchId');
        
		parent::init();

		$this->jsIncludes->commonAjax = true;
		$this->jsIncludes->branchAjax = true;

		if(isset($this->indexSectionId))
			$this->view->indexSectionId = $this->indexSectionId;
		if(isset($this->viewSectionId))
			$this->view->viewSectionId = $this->viewSectionId;
		if(isset($this->insertSectionId))
			$this->view->insertSectionId = $this->insertSectionId;
		if(isset($this->updateSectionId))
			$this->view->updateSectionId = $this->updateSectionId;

	}

}
