<?php

class SportsbookController extends Zend_Controller_Action {

	private $filterConv = array(
		'asc'	=> 'ASC',
		'desc'	=> 'DESC'
	);


	public function init() {
		$this->view->addHelperPath('views/helpers', 'My_View_Helper');
				
		if ( !empty($_GET['ajax']) && 'next' == $_GET['ajax']) {
			$this->view->isAjax = true;
			$this->_helper->layout->disableLayout();
		}
		elseif (isset($_REQUEST["ajax"]) && $_REQUEST["ajax"] == 1){
			$this->view->isAjax = true;
			$this->_helper->layout->disableLayout();
		}
		else
			$this->view->isAjax = false;

		if ( !$this->view->isAjax ) {
			Models_BasicRender::render($this->view,$this->_request);

			#Nacte data z tiketu#
			Models_Ajax_Ticket::get($this->view);
			
			Models_Helpers_Panels::rightCol($this->view);
		}
		
	}

	public function indexAction() {

		$menu = Models_Helpers_Panels::sportMenu($this->view, true); //$this->view->isAjax);

		$openUrlArr = $menu->getOpenUrl();
		$sport		= $menu->getUrl($openUrlArr['sport'], 1);
		$oblast		= $menu->getUrl($openUrlArr['oblast'], 2);
		$udalost	= $menu->getUrl($openUrlArr['udalost'], 3);

		$urlParts = array(
			$openUrlArr['sport'] => 1,
			$openUrlArr['oblast'] => 2,
			$openUrlArr['udalost'] => 3
		);

		Zend_Registry::get('fl')->info($openUrlArr);

		$this->view->urlParams = $menu->getAllLangUrlParams($urlParts);
		$this->view->rangeValues =  Models_Helpers_Panels::getRangeValues();
		$ws = Zend_Registry::get('ws');
		$title = Models_BasicRender::title();
		$description = Models_BasicRender::description();
		$keywords = Models_BasicRender::keywords();

		if ( !empty($openUrlArr['sport'])) {
			$sport2 = $ws->Sport->getById($openUrlArr['sport']);
			$trans_sport = Zend_Registry::get('translate')->trans($sport2['name']);
			$this->view->title = $trans_sport . ' | ' . $title;
			$this->view->h1 = $trans_sport;
			$this->view->description = str_replace("#replace_desc#", $trans_sport, $description);
			$this->view->keywords = str_replace("#replace#", $trans_sport, $keywords);
		}

		if ( !empty($openUrlArr['oblast'])) {
			$region2 = $ws->Region->getById($openUrlArr['oblast']);
			$this->view->title = $trans_sport . ' - ' . $region2['name'] . ' | ' . $title;
			$this->view->h1 .= ' - ' . $region2['name'];
			$this->view->description = str_replace("#replace_desc#", $trans_sport . ' - ' . $region2['name'], $description);
			$this->view->keywords = str_replace("#replace#", $trans_sport . ' - ' . $region2['name'], $keywords);
		}

		if ( !empty($openUrlArr['udalost'])) {
			$event2 = $ws->Event->getById($openUrlArr['udalost']);
			$this->view->title = $trans_sport . ' - ' . $region2['name'] . ' - ' . $event2['name'] . ' | ' . $title;
			$this->view->h1 .= ' - ' . $event2['name'];
			$this->view->description = str_replace("#replace_desc#", $trans_sport . ' - ' . $region2['name'] . ' - ' . $event2['name'], $description);
			$this->view->keywords = str_replace("#replace#", $trans_sport . ' - ' . $region2['name'] . ' - ' . $event2['name'], $keywords);
		}

		/* deprecated
		switch ( $this->view->filteredOffer ) {
		case 'today':
			$this->view->realUrl = $this->view->UrlSet(4);
			$this->view->showToday = false;
			break;
		case 'todayAndTomorrow':
			$this->view->realUrl = $this->view->UrlSet(5);
			$this->view->showToday = true;
			break;
		case 'todaySoccer':
			$this->view->realUrl = $this->view->UrlSet(6);
			$this->view->showToday = false;
			break;
		case 'todayTennis':
			$this->view->realUrl = $this->view->UrlSet(7);
			$this->view->showToday = false;
			break;
		default:
			$this->view->realUrl = '/cs/sazky/'.$sport.$oblast.$udalost;
			$this->view->showToday = true;
			break;
		}
		*/

		if(!empty($_REQUEST['orderDirection']))
			$orderDirection = $this->filterConv[$_REQUEST['orderDirection']];
		else
			$orderDirection = null;


		//Pripraveno pro pouziti global cache frame sport-odds
		//$this->view->oddsQueryString = $_SERVER['QUERY_STRING'] . "&sport=$sport&region=$oblast&event=$udalost";
/*
		else {
			if ($this->view->filteredOffer = 'today') {
				$onlyToday = TRUE;
				$this->view->onlyToday = 1;
			} else {
				$onlyToday = FALSE;
				$this->view->onlyToday = 0;
			}
		}
*/

	/*	if ($this->view->filteredOffer = 'today') {
			$onlyToday = TRUE;
			$this->view->onlyToday = 1;
		}*/
		/*else {
			$onlyToday = FALSE;
			$this->view->onlyToday = 0;
		}*/

		Models_Helpers_Panels::timeFilter($this->view);

		Models_Markets_MarketData::init($openUrlArr, null, $orderDirection, $this->view->timeFilter);
		$this->view->type = Models_Markets_MarketData::getTypes($this->view->timeFilter);
		$this->view->odds = Models_Markets_MarketData::getOdds();
		$this->view->nextOddsUrls = Models_Markets_MarketData::$nextOddsUrls;

		$this->view->page = Models_Markets_MarketData::$page;
		$this->view->isLast = Models_Markets_MarketData::$isLast;
		$this->view->types = Models_Markets_MarketData::$types;
/*
		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);
		if ( Zend_Registry::isRegistered('mena') )
			$this->view->mena = Zend_Registry::get('mena');
		else
			$this->view->mena = '';
*/
}


	public function detailAction() {
		#Sport menu#
		//$menu = new Models_Navigation_SportMenu;
		//$this->view->sportMenu   = $menu->getMenu();
		#End Sport menu#
		
		$menu = Models_Helpers_Panels::sportMenu($this->view, $this->view->isAjax);

		$uri = $this->_request->getRequestUri();

		Models_Ajax_Ticket::get($this->view);
		Models_Helpers_Panels::rightCol($this->view);
		#Market data#
		$bet_id = Models_Helpers_Help::parseDetailUrl( $uri );

		Models_Markets_MarketData::init( $menu->getOpenUrl(), $bet_id, null, null, null, TRUE );

		$this->view->type = Models_Markets_MarketData::getTypes();
		$this->view->odds = Models_Markets_MarketData::getOdds();
		$this->view->name = Models_Markets_MarketData::getName() .' / ';
		$this->view->types = Models_Markets_MarketData::$types;

		#End Market data#
		$this->view->isDetailView = true;
	}

	public function ticketAction() {
		It6_GlobalCache::useSession();
		It6_GlobalCache::maxExpiration(MAX_LOGIN_TIMEOUT);
		//if ( Zend_Registry::isRegistered('user_id') )
		//	It6_GlobalCache::setUserTag(Zend_Registry::get('user_id'));
		$this->_helper->layout->disableLayout();


	}


	public function todayAction() {
		$this->filteredOffer('today');
	}

	public function todayAndTomorrowAction() {
		$this->filteredOffer('todayAndTomorrow');
	}

	public function todaySoccerAction() {
		$this->filteredOffer('todaySoccer');
	}

	public function todayTennisAction() {
		$this->filteredOffer('todayTennis');
	}

	public function sportMenuAction() {
		$this->_helper->layout->disableLayout();
		$menu = Models_Helpers_Panels::sportMenu($this->view);
	}

	public function sportOddsAction() {

		//TODO Tato akce se zatim se nepouziva
		$this->_helper->layout->disableLayout();

		if(!empty($_GET['orderDirection']))
			$orderDirection = $this->filterConv[$_GET['orderDirection']];
		else
			$orderDirection = null;

		Models_Helpers_Panels::timeFilter($this->view);

		$openUrlArr = array();
		$openUrlArr['sport'] = array_key_exists('sport', $_GET) ? $_GET['sport'] : null;
		$openUrlArr['oblast'] = array_key_exists('region', $_GET) ? $_GET['region'] : null;
		$openUrlArr['udalost'] = array_key_exists('event', $_GET) ? $_GET['event'] : null;

		Models_Markets_MarketData::init($openUrlArr, null, $orderDirection, $this->view->timeFilter);
		$this->view->type = Models_Markets_MarketData::getTypes();
		$this->view->odds = Models_Markets_MarketData::getOdds();

		$this->view->page = Models_Markets_MarketData::$page;
		$this->view->isLast = Models_Markets_MarketData::$isLast;
		$this->view->types = Models_Markets_MarketData::$types;
	}

	public function filteredOffer($action) {

		$this->view->filteredOffer = $action;
		$this->indexAction();
		/*
		#Sport menu#
		$menu = new Models_Navigation_SportMenu;
		$this->view->sportMenu   = $menu->getMenu();
		$this->view->OblastIP    = $menu->getOblastIP();
		$this->view->isOpen = $menu->isOpen;
		#End Sport menu#

		#Market data#
		Models_Markets_MarketData::init($menu->getOpenUrl());
		$this->view->type = Models_Markets_MarketData::getTypes();
		$this->view->odds = Models_Markets_MarketData::getOdds();
		$this->view->name = '';
		#End Market data#

		#User Data#
		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);
		if ( Zend_Registry::isRegistered('mena') )
			$this->view->mena = Zend_Registry::get('mena');
		else
			$this->view->mena = '';
		#End User Data#
		*/
	}
	
}