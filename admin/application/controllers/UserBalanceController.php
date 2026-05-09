<?php

class UserBalanceController extends controllers_UserAbstractController {

	protected $viewSectionId		= 231;
	protected $updateSectionId		= 232;

	protected $userId;
	protected $ws;


	public function init() {
		parent::init();
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');

		$this->view->user = $this->ws->User->getById( (integer)$this->userId );
	}
}
