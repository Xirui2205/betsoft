<?php

class RssController extends Zend_Controller_Action {

	public function indexAction() {
		It6_GlobalCache::turnOff();
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		Models_BasicRender::render($this->view,$this->_request);

		$this->_helper->layout->disableLayout();
		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);
		$this->view->n = intval($this->_request->getParam('n'));

		if(isset($this->view->n)){
			Models_Helpers_Panels::news($this->view);
		}

		$feed = Models_Helpers_Rss::getFeed();

		$feed->saveXml();
		$feed->send();
		exit;
	}
}
