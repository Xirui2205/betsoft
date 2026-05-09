<?php

class UserPasswordController extends controllers_UserAbstractController {

	protected $viewSectionId		= 233;
	protected $updateSectionId		= 234;


	protected $userId;
	protected $ws;


	public function init() {
		parent::init();
	}



	public function updateAction() {
		$this->_helper->layout->setLayout('empty');

		if($this->getRequest()->getPost('save')) {
			if(Zend_Registry::get('ws')->UserAction->resetPassword(null, $this->userId)) {
				$this->view->feedbackMsg = UiUtil::printMessages(array('new-pass-sent'));
				It6_Log::info(
					'User password reset',
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'userId' => $this->userId,
						'adminId' => Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN)
					)
				);
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('new-pass-error'));
				It6_Log::info(
					'User password reset not successful',
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'userId' => $this->userId,
						'adminId' => Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN)
					)
				);
			}
		}

		$userPasswordForm = new Models_Form_UserPassword($this->updateSectionId);
		$userPasswordForm->populate(array('userId'	=> $this->userId));

		$this->view->userPasswordForm = $userPasswordForm;
	}
}
