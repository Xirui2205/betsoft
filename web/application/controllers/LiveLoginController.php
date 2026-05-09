<?php

class LiveLoginController extends Zend_Controller_Action {

public function init(){
	//$this->_helper->layout->disableLayout();
	$this->_helper->layout->setLayout('simple');
	It6_GlobalCache::turnOff();
	$this->view->lang_id = $_SESSION['lang_id'];
}

public function indexAction() {
	if (!empty($_POST['matchId']))
		$matchId = $_POST['matchId'];
	else if (!empty($_GET['matchId']))
		$matchId = $_GET['matchId'];
	else
		$matchId = false;

	$this->view->matchId = $matchId;
	if (isset($_POST['pass'])) {
		if (2 == $GLOBALS['ses_status'] || 3 == $GLOBALS['ses_status']) {
			$this->view->authenticated = true;
			$url = It6_Models_Livebetting::getUrl($matchId);
			$this->view->redirectUrl = htmlspecialchars($url);
			$this->_redirect($url);
		}
		else {
			$this->view->authenticated = false;
			$this->view->errMsg = (empty($GLOBALS['ses_errmsg'])
				? $this->view->trans('auth_failed')
				: $GLOBALS['ses_errmsg']
			);
		}
	}
	else {
		$this->view->authenticated = null;
	}
}

} // class LiveLoginController
