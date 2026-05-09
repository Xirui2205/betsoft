<?php

class ForgottenpasswordController extends Zend_Controller_Action {

	public function indexAction() {
		It6_GlobalCache::turnOff(); // because of CAPTCHA
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		Models_BasicRender::render($this->view,$this->_request);
		
		$form = new Models_Form_Forgottenpassword($this->view);

		if ( $this->view->log == 2 || $this->view->log == 3 ) {
			header('Location: '.PROTOCOL. $_SERVER['HTTP_HOST']);
			exit;
		}

		$inData = $this->getRequest()->getPost();
		if (!empty($inData['submit'])) {

			if($form->isValid($this->getRequest()->getPost())) {
				if ($this->checkAttemptsCount()) {
					$this->saveResetingIp();
					if($this->view->feedbackMsg = It6_Models_Forgottenpass::newPassByNick($inData['email'], $inData['nick']))
						$this->view->feedbackMsg = It6_FeedbackMsg::printNotice('password_email_sent');
					else
						$this->view->feedbackMsg = It6_FeedbackMsg::printError('email_or_nick_wrong');
				}
				else {
					$this->view->feedbackMsg = It6_FeedbackMsg::printError('reset_too_many_attempts');
				}
			}
			else
				$this->view->feedbackMsg = It6_FeedbackMsg::printError('error_form_not_valid_general');
		}

		$this->view->forgottenPassForm = $form->render();
	}
	
	private function saveResetingIp() {
		$ip = ip2long(It6_Php::getRemoteAddr());
		if ( false !== $ip ) {
			$db = Zend_Registry::get('db');
			$sql = sprintf("INSERT INTO reset_password_attempts (ipv4) VALUES (%u)", $ip);
			$result = $db->query($sql);
			$db->closeConnection();
			return true;
		}
		else {
			throw new Exception('IP address has invalid format');
		}
	}
	
	private function checkAttemptsCount() {
		$ws = Zend_Registry::get('ws');
		$time_limit = $ws->Parameter->getGlobalParameter('resetPassword.time.limit');
		$attempts_limit = $ws->Parameter->getGlobalParameter('resetPassword.attempts.limit');
		
		$ip = ip2long(It6_Php::getRemoteAddr());
		
		$db = Zend_Registry::get('db');
		$rows = $db->select()
					->from('reset_password_attempts')
					->where('ipv4 = ?', $ip)
					->where('timestamp >= ?', It6_Date::dbNow((-3600 * $time_limit)))
					->query()->fetchAll();
		$db->closeConnection();
		
		return ( count($rows) < $attempts_limit ) ? true : false;
	}
}
