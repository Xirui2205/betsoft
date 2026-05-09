<?php

class AdminPrivilegesController extends controllers_AdminAbstractController {

	public $viewSectionId = 285;

	public function init() {
		parent::init();
	}

	public function indexAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->adminId = $this->adminId;

		$db = Zend_Registry::get('zdb_admin');
		
		$role = 'user:'.$this->adminId;
		$data = array();
		$data['role'] = It6_Models_Acl::getParentRoles($role,$db);

			$this->view->editable = true;
			$data['role'] = $data['role'];

			$form = new Models_Form_AdminPrivileges($this->viewSectionId, $this->adminId);
			$form->populate($data);

			if ($this->getRequest()->getPost('submit')) {
				$data = $this->getRequest()->getPost();
				
				if ($form->isValid($data)) {
					try {
						$this->ws->AccessController->setParentRole($this->adminId, $data['role'], true, true);
						$this->view->feedbackMsg = UiUtil::printMessages('Privileges has been edited.');
					}
					catch (Exception $e) {
						It6_Log::notice('Admin edit privileges failed: %message%',It6_Log::TAG_ADMIN_OPERATION, array('data' => $data,'message' => $e->getMessage()));
						It6_Log::err($e);
						$this->view->feedbackMsg = UiUtil::printErrors($e->getMessage());
						
					}
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors('not-valid');
				}
			}
	
			$form->populate($data);
			$this->view->form = $form;
		
	}

}
