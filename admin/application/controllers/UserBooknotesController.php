<?php

class UserBooknotesController extends controllers_UserAbstractController {

	protected $viewSectionId		= 235;
	protected $updateSectionId		= 236;

	protected $userId;
	protected $ws;


	public function init() {
		parent::init();
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_SUPERVISION))
			$this->view->supervisionLogged = true;
		else
			$this->view->supervisionLogged = false;
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');

		//TODO: pulling out too much data - should use some column restriction
		$user = $this->ws->User->getById( (integer)$this->userId );
		$this->view->bookNote = nl2br($user['noteBookmaker']);
	}



	public function updateAction() {
		$this->_helper->layout->setLayout('empty');

		if($this->getRequest()->getPost('save')) {

			$userKolekce = new Models_Userkolekce;
			$result = $userKolekce->CreatePoznamkaBook($this->userId);

			if($result === true) {
				$this->view->feedbackMsg = UiUtil::printMessages(array('booknote-insert-ok'));
				$this->_forward('view');
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('booknote-insert-error'.$result));
		}

		$user = $this->ws->User->getById( (integer)$this->userId );
		$this->view->noteBookmaker = $user['noteBookmaker'];
	}
}
