<?php

class UseractionController extends Zend_Controller_Action {


	public function indexAction() {
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		Models_BasicRender::render($this->view,$this->_request);

		$actionHash = $this->_request->getQuery('action');

		$prompt = Models_MyAccount_Useraction::getPrompt($actionHash);
		if($prompt !== false) {
			$this->view->showForm		= false;
			$this->view->prompt 		= $prompt;
			$this->view->actionHash 	= $actionHash;
		}
	}



	public function resultAction() {
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		Models_BasicRender::render($this->view,$this->_request);

		$actionHash = $this->getRequest()->getPost('actionHash');
		if(Zend_Registry::get('ws')->UserAction->resetPassword($actionHash))
			$this->view->feedbackMsg = It6_FeedbackMsg::printNotice('password-sent');
		else
			$this->view->feedbackMsg = It6_FeedbackMsg::printError('password-not-sent');
	}

}
