<?php
/**
 * Provide quick unsubscribe option (without login)
 */
class UnsubscribeController extends Zend_Controller_Action {
	private $ws;
	private $translate;
	
	public function init() {
		$this->ws = Zend_Registry::get('ws');
		$this->translate = Zend_Registry::get('translate');
	}
	
	public function indexAction() {
		Models_BasicRender::render($this->view,$this->_request);
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		
		$email = $this->_getParam('email', '');
		$unsubscribeHash = $this->_getParam('x', '');
		
		$extensions = array(new It6_WsExtension_Client_Columns('columns', array('userId')));
		$user = $this->ws->ext($extensions)->User->getOneBy($email, 'email');
				
		if (!isset($_POST['unsubscribe'])) {
			// show form
			$this->view->email = $email;
			$this->view->unsubscribeHash = $unsubscribeHash;
		} else {
			// perform unsubscribe
			if ($unsubscribeHash == It6_Models_Newsletter::getHash($user['userId'], $email)
					&& $this->ws->User->update(array('userId' => $user['userId'],
													 'sendNewsletter' =>0))) {
				$this->view->resultMessage = $this->translate->trans('unsubscribe_ok');
			} else {
				$this->view->resultMessage = $this->translate->trans('unsubscribe_wrong');
			}
			
			$this->render('result');
		}
	}
}