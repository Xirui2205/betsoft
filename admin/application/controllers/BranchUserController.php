<?php

class BranchUserController extends controllers_BranchAbstractController {

	var $viewSectionId = 183;
	var $assignSectionId = 184;
	var $ws;

	public function init() {
		parent::init();
		$this->userId = $this->getRequest()->getPost('userId');
	}

	public function indexAction() {
	}

	public function viewAction() {
		$this->_helper->layout->setLayout('empty');

		$this->view->users			= $this->ws->User->getByBranchId( (integer)$this->branchId );
		$this->view->branchesCombo	= Models_Branch::getBranchesComboBox();
		$this->view->branchId		= $this->branchId;

		if ($this->userId) {
			$this->view->user = $this->ws->User->getById( (integer)$this->userId );
		}
	}

	public function assignAction() {
		$this->_helper->layout->setLayout('empty');
		if ($this->getRequest()->getPost('usersToAssign')) {
			$this->_helper->layout->setLayout('empty');
			$userIds = $this->getRequest()->getPost('usersToAssign');
			$newBranchId = $this->getRequest()->getPost('newBranchId');

			if ($this->ws->User->setUserBranchId( $userIds, (integer)$newBranchId ))
				$this->view->feedbackMsg = UiUtil::printMessages( I18n::tr('user_assign_ok') );
			else
				$this->view->feedbackMsg = UiUtil::printErrors( I18n::tr('user_assign_error') );
		}

		$this->view->branchId = $this->branchId;
		$this->view->branchesCombo = Models_Branch::getBranchesComboBox();
		$this->view->users = $this->ws->User->getByBranchId( (integer)$this->branchId );

	}

}
