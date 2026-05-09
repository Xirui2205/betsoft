<?php

class LoginController extends Zend_Controller_Action {


	public function init() {
		$this->tr = Zend_Registry::get('translate');
		$this->ws = Zend_Registry::get('ws');
		$this->userId = (Zend_Registry::isRegistered('user_id') ? Zend_Registry::get('user_id') : 0);
		if (!empty($this->userId))
			$this->_redirect($this->view->urlSet(1));
		
		$this->view->error = $this->tr->trans('ticket_er_1');
	}


	public function indexAction() {
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		$loginForm = new Models_Form_Login();
		$loginForm->populate(array(
			'redirectTo' => $this->getRequest()->getParam('redirect'),
		));
		
		$this->view->loginForm = $loginForm;
		Models_BasicRender::render($this->view, $this->_request);
	}
}
