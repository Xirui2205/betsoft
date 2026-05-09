<?php

class SearchController extends Zend_Controller_Action {

	public function init() {
		$this->view->addHelperPath('views/helpers', 'My_View_Helper');
		Models_BasicRender::render($this->view,$this->_request);

		#Nacte data z tiketu#
		Models_Ajax_Ticket::get($this->view);
		
		It6_GlobalCache::turnOff();
	}

	public function indexAction() {

		if ( !empty($_GET['ajax']) && 'next' == $_GET['ajax']) {
			$this->view->isAjax = true;
			$this->_helper->layout->disableLayout();
		}
		else
			$this->view->isAjax = false;
		
		#Sport menu#
		$menu = new Models_Navigation_SportMenu;
		$this->view->sportMenu   = $menu->getMenu();
		$this->view->OblastIP    = $menu->getOblastIP();
		$this->view->isOpen = $menu->isOpen;
		#End Sport menu#

		#Market data#
		Models_Markets_MarketData::init($menu->getOpenUrl());
		
		$this->view->nextOddsUrls = Models_Markets_MarketData::$nextOddsUrls;
		
		$this->view->type = Models_Markets_MarketData::getTypes();

		$this->view->odds = Models_Markets_MarketData::getOdds();
		 
		$this->view->name = '';
		#End Market data#

		#User Data#
		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);
		if ( Zend_Registry::isRegistered('mena') ) { 
			$this->view->mena = Zend_Registry::get('mena');
		}
		else {
			$this->view->mena = '';	
		}
		#End User Data#

		$this->view->page = Models_Markets_MarketData::$page;
		$this->view->isLast = Models_Markets_MarketData::$isLast;
		$this->view->types = Models_Markets_MarketData::$types;

		$menu = Models_Helpers_Panels::sportMenu($this->view, $this->view->isAjax);

		//echo "<pre>";print_r($this->view->odds);exit;
	}

}
