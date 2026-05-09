<?php

class EditProfileController extends It6_Controller_Abstract {

	const INDEX_SECTION_ID = 280;

	public function indexAction() {
		$this->view->form = new Models_Form_EditProfile(static::INDEX_SECTION_ID);
		
		$this->view->feedbackMsg = '';
		
		if ($this->getRequest()->getPost('submit')) {
			$data = $this->getRequest()->getPost();
			if ($this->view->form->isValid($data)) {
				$ws = Zend_Registry::get('ws');
				if ( !empty($data['pass'] )) {
					if ( $data['pass'] != $data['pass_again'] ) {
						$this->view->feedbackMsg = UiUtil::printErrors(array('Password is not equal to password again.'));
						It6_Log::notice('Password change failed: Password is not equal to password again.',It6_Log::TAG_ADMIN_OPERATION);
					}
					else {
						$acl = Zend_Registry::get('acl');
						$adminId = $acl->getIdentity(It6_Acl::IDNAME_ADMIN);
						try {
							
							if ( $ws->Admin->changePassword($adminId, $data['old_pass'],$data['pass']) ) { 
								It6_Log::info('Password changed.',It6_Log::TAG_ADMIN_OPERATION);
								$this->view->feedbackMsg = UiUtil::printMessages(array('Password changed seccessfuly.'));
							}
							else {
								It6_Log::notice('Password change failed: Wrong old password.',It6_Log::TAG_ADMIN_OPERATION);
								$this->view->feedbackMsg = UiUtil::printErrors(array('Password change failed: Wrong old password.'));
							}
						}
						catch (Exception $e) {
							$this->view->feedbackMsg = UiUtil::printErrors(array($e->getMessage()));
							It6_Log::notice('Password change failed: %reason%',It6_Log::TAG_ADMIN_OPERATION, array('reason' => $e->getMessage()));
						}

					}
				}
			}
		}
	}

}
