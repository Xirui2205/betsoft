<?php

class NewsController extends Zend_Controller_Action {


	public function indexAction() {
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		Models_BasicRender::render($this->view,$this->_request);

		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);
		$this->view->n = intval($this->_request->getParam('n'));

		if(isset($this->view->n)){
			Models_Helpers_Panels::news($this->view);
		}

	}


}

?>
