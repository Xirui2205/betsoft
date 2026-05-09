<?php

class LivebettingController extends Zend_Controller_Action {

	public function init() {

		/* Initialize action controller here */
		Models_BasicRender::render($this->view,$this->_request);
		$this->view->addHelperPath('views/helpers', 'My_View_Helper');

		#Nacte data z tiketu#
		Models_Ajax_Ticket::get($this->view);
		#User Data#

		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);

		if ( Zend_Registry::isRegistered('mena') ) {
			$this->view->mena = Zend_Registry::get('mena');
		} else {
			$this->view->mena = '';
		}

		#End User Data#
	}

	public function indexAction() {
		It6_GlobalCache::useSession();
		$menu = new Models_Navigation_SportMenu;
		$this->view->sportMenu = $menu->getMenu();
		$this->view->info = Models_MyAccount_Ticket::detail( intval( $this->_request->getParam('t') ) );

		#BEGIN MONTH TICKET#
		Models_Helpers_Panels::monthTicket($this->view);
		if ( !is_array($this->view->month)
				|| count($this->view->month) == 0 ) {

			$this->view->month = array();
		}

		#END MONTH TICKET#

		$this->view->sportForm = Models_LiveBetting_Calendar::getSport();
		$this->view->url = '';

		if ( mb_strlen( trim( $this->_request->getParam('calendar') ) ) != 0 ) {
			$this->view->tab = 'calendar';
			$this->view->obdobi = $this->_request->getParam('calendar');
			$this->view->url =
						'calendar/'
						. $this->_request->getParam('calendar')
						.'/';

			$this->view->data = Models_LiveBetting_Calendar::getCalendarData(
						'calendar',
						$this->_request->getParam('calendar'),
						intval( $this->_request->getParam('page') ));
			
		}
		else if ( mb_strlen(trim($this->_request->getParam('sport'))) != 0 ) {

			$this->view->tab = 'sport';
			$this->view->sport_id = $this->_request->getParam('sport');
			$this->view->url =
	        		'sport/'
	        		. $this->_request->getParam('sport')
	        		. '/';

	        		$this->view->data = Models_LiveBetting_Calendar::getCalendarData(
	        		'sport',
	        		$this->_request->getParam('sport'),
	        		intval( $this->_request->getParam('page') )
	        		);

		}
		else {
			$this->view->tab = 'sport';
			$this->view->sport_id = 'all';
			$this->view->data = Models_LiveBetting_Calendar::getCalendarData(
        			'sport',
        			'all',
			intval( $this->_request->getParam('page') )
			);
		}

		$this->view->pagging = Models_LiveBetting_Calendar::pagging();

		$this->view->date = array();
			foreach( $this->view->data as $k => $h )
				$this->view->date[$h->start][] = $k;
	}


	public function matchAction() {
		It6_GlobalCache::useSession();
		unset($_SESSION['basicTime']);
		unset($_SESSION['betTime']);

		$this->view->n = intval($this->_request->getParam('n'));

		Models_Helpers_Panels::monthTicket($this->view);
		if ( !is_array($this->view->month) || count($this->view->month) == 0 ) {
			$this->view->month = array();
		}

		$this->view->liveOnline = Models_LiveBetting_Calendar::getOnLine(1);
		$this->view->liveComing = Models_LiveBetting_Calendar::getComming(1);

		$menu = new Models_Navigation_SportMenu;
		$this->view->sportMenu = $menu->getMenu();
		$this->view->info = Models_MyAccount_Ticket::detail( intval( $this->_request->getParam('t') ) );

	}

}
