<?php

class controllers_ParamsAbstractController extends It6_Controller_Abstract {

	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		$this->paramId = $this->getRequest()->getParam('paramId');
		if (!isset($this->paramId))
			$this->paramId = $this->getRequest()->getParam('paramId');

		$this->jsIncludes->commonAjax	 = true;
		$this->jsIncludes->parametryAjax = true;
		$this->view->viewSectionId		 = $this->viewSectionId;
		$this->view->updateSectionId	 = $this->updateSectionId;
		if (isset($this->blockClientCardSectionId)) {
			$this->view->blockClientCardSectionId = $this->blockClientCardSectionId;
		}
		$this->view->paramId				= $this->paramId;

		if(isset($this->indexSectionId))
			$this->view->indexSectionId = $this->indexSectionId;

	}

}