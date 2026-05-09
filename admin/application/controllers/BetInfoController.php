<?php

class BetInfoController extends It6_Controller_Abstract {

	var $rateOverTimeSectionId = 278;


	public function init() {
		parent::init();

		$this->ws = Zend_Registry::get('ws');
	}



	public function rateOverTimeAction() {
		$this->_helper->layout->setLayout('catalog');
		$betId = $this->getRequest()->getParam('betId');

		$bet = Zend_Registry::get('ws')->Bet->getById($betId);
		$betOdds = Zend_Registry::get('ws')->BetOdds->getByBetId($betId);

		Models_BetInfo::getGraph($bet, $betOdds);

		$this->view->betOdds	= $betOdds;
		$this->view->betId		= $bet['betId'];
		$this->view->betName	= $bet['name'];
	}
}
