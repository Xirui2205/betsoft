<?php

class IndexController extends Zend_Controller_Action {

	const COOKIE_SUPER_HP = 'SuperHpVisited';

	public function init() {
		$this->view->addHelperPath('views/helpers/', 'My_View_Helper');
		Models_BasicRender::render($this->view, $this->_request);

		#Nacte data z tiketu#
		Models_Ajax_Ticket::get($this->view);
		#User Data#

		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);

		require_once "class/class.Date.php";

		/* Initialize action controller here */

		/*  $response = $this->getResponse();
		 $response->insert('sidebar', $this->view->render('sidebar.phtml'));
		 <?php echo $this->layout()->sidebar; ?>
		 */
	}

	// nový index - welcome (dříve tady bylo to, co je v index/sportsbook)
	public function indexAction() {
		$this->view->oneColumn = true;
	}
	
	public function anonymSlipAction() {
		$key = 'ticket_search_not_found_'.It6_Php::getRemoteAddr();

		$value = It6_GlobalCache::getKey($key);
		if ( empty($value) )
			$value = 0;
		
		if ( $value > static::MAX_TICKET_SEARCH_NOT_FOUNDS ) {
			$this->view->err = 'ticket_search_blocked';
		}
		else {
			$handle	= $this->_request->getParam('t');
	
			Models_MyAccount_Ticket::getTicketByHandle($this->view, $handle);
	
			if ( empty($this->view->ticket['state']) ) {
				Models_MyAccount_LiveTicket::getTicketByHandle($this->view, $handle);
				if ( !is_array($this->view->ticket) || count($this->view->ticket) == 0 ) {
					$this->view->err = 'ticket_not_found';
					It6_GlobalCache::setKey($key,$value + 1,static::MAX_TICKET_SEARCH_NOT_FOUNDS_TIME_WINDOW);
				}
				else {
					$this->view->live = true;
				}
			}
			else {
				$this->view->live = false;
			}
		}

		Models_Helpers_Panels::leftAndRightColAndNews($this->view);

	}
	
	// původní index/index
	public function sportsbookAction() {
		It6_GlobalCache::maxExpiration(60);

		$paramId = 105; // web.superHp.enabled
		$superHp = It6_GlobalCache::getKey(It6_GlobalCache::KEY_PREFIX_GPARAM . $paramId, $fetched);
		if (!$fetched) {
			$superHp = Zend_Registry::get('ws')->Parameter->getGlobalParameter('web.superHp.enabled');
			It6_GlobalCache::setKey(It6_GlobalCache::KEY_PREFIX_GPARAM . $paramId, $superHp);
		}
		if ($superHp && empty($_COOKIE[self::COOKIE_SUPER_HP])) {
			$t = localtime(time() + 24*3600, true);
			setcookie(self::COOKIE_SUPER_HP, 1, mktime(0, 0, 0, $t['tm_mon'] + 1, $t['tm_mday'], $t['tm_year'] + 1900), '/', WEBHOST);
			$gAdParams = It6_Models_ControllerConvert::getGoogleAdParams();
			$this->_redirect($this->view->UrlSet(44, $gAdParams));
		}

		//Models_BasicRender::render($this->view,$this->_request);
		//$menu = new Models_Navigation_SportMenu;
		$this->view->promo			= Models_Marketing_Promo::getPromos(1, $_SESSION['lang_id']);
		$banners					= Models_Marketing_Promo::getBanners(1, $_SESSION['lang_id']);
		$this->view->bannersLeft	= $banners[1];
		$this->view->bannersRight	= $banners[2];
		$this->view->news = Models_Marketing_Promo::news();
		
		$menu = Models_Helpers_Panels::sportMenu($this->view);

		$openUrlArr = $menu->getOpenUrl();
		$sport		= $menu->getUrl($openUrlArr['sport'], 1);
		$oblast		= $menu->getUrl($openUrlArr['oblast'], 2);
		$udalost	= $menu->getUrl($openUrlArr['udalost'], 3);


		$urlParts = array(
			$openUrlArr['sport'] => 1,
			$openUrlArr['oblast'] => 2,
			$openUrlArr['udalost'] => 3
		);
		$this->view->urlParams = array();
		$this->view->urlParams = $menu->getAllLangUrlParams($urlParts);
		
		//$get = Models_Ajax_Ticket::get($this->view);
		//Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		
		$this->view->isSupertip = Models_Markets_MarketData::isSupertip();
	}
	
		
	}
