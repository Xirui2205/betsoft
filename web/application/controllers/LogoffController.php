<?php

class LogoffController extends Zend_Controller_Action
{

	public function init()
	{
		 
		$this->view->addHelperPath('views/helpers/', 'My_View_Helper');
		 
		#Nacte data z tiketu#
		Models_Ajax_Ticket::get($this->view);
		#User Data#
		 
		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);
		 
		 
		 
		require_once "class/class.Date.php";
		 

	}

	public function doAction() {
		try {
			$jsonCallback = isset($_REQUEST['jsonp_callback']) ? $_REQUEST['jsonp_callback'] : null;
			It6_GlobalCache::turnOff();
			It6_GlobalCache_Invalidator::invalidateLogoutFrame();
			Models_Helpers_Logoff::off();
			if(empty($jsonCallback)) {
				$url = $this->view->urlSet(1);
				$this->getResponse()->setRedirect($url); // /lang/odhlasen/
				$this->view->url = $url;
			}
			// cookie is controlling cache, must prevent use of cached data for session
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params['path'], $params['domain'],
				$params['secure'], $params['httponly']
			);
			session_destroy();
			if(!empty($jsonCallback)) {
				$this->_helper->layout()->disableLayout();
				$this->_helper->viewRenderer->setNoRender(true);
				$response = array('status'=> 1);
				echo $script = $jsonCallback.'('.json_encode($response).')';
			}
		}
		catch(Exception $e){
			if(!empty($jsonCallback)) {
				$this->_helper->layout()->disableLayout();
				$this->_helper->viewRenderer->setNoRender(true);
				$response = array('status'=> 0);
				echo $script = $jsonCallback.'('.json_encode($response).')';
			}
		}
	}
	
	public function indexAction()
	{
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		Models_BasicRender::render($this->view,$this->_request); //musi byt zde az druhy

	}

}
