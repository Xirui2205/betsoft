<?php

class controllers_AdminAbstractController extends It6_Controller_Abstract {

	public function init() {
		$this->ws = Zend_Registry::get('ws');
		$this->adminId = $this->getRequest()->getPost('adminId');

		parent::init();

		$this->jsIncludes->commonAjax = true;
		$this->jsIncludes->adminAjax = true;

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
