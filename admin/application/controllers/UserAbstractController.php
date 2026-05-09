<?php

class controllers_UserAbstractController extends It6_Controller_Abstract {

	public function init() {
		parent::init();

		$this->ws = Zend_Registry::get('ws');
		$this->userId = $this->getRequest()->getParam('userId');
		if (!isset($this->userId))
			$this->userId = $this->getRequest()->getParam('user_id');

		$this->jsIncludes->commonAjax	= true;
		$this->jsIncludes->userAjax		= true;
		$this->view->viewSectionId		= $this->viewSectionId;
		$this->view->updateSectionId	= $this->updateSectionId;
		$this->view->banUserSectionId	= $this->banUserSectionId;
		if (isset($this->blockClientCardSectionId)) {
			$this->view->blockClientCardSectionId = $this->blockClientCardSectionId;
		}
		$this->view->userId				= $this->userId;

		if(isset($this->indexSectionId))
			$this->view->indexSectionId = $this->indexSectionId;

	}

}
