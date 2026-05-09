<?php

class LoginController extends Zend_Controller_Action {

	function indexAction() {
		Zend_Layout::getMvcInstance()->setLayout('login');
	}

}
