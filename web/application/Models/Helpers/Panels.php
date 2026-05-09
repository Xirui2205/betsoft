<?php
class Models_Helpers_Panels {

	private static $mainMenuActive = "sportbook";

	private static $leftMenuItems = array(
		array(
			'conActId'	=> 35,
			'label'		=> 'profile_user',
			'active'	=> true,
		),
		array(
			'conActId'	=> 51,
			'label'		=> 'withdrawal_request',
			'active'	=> true,
		),
		array(
			'conActId'	=> 52,
			'label'		=> 'deposit_by_card',
			'active'	=> true,
		),
		array(
			'conActId'	=> 53,
			'label'		=> 'deposit_by_bank',
			'active'	=> true,
		),
		array(
			'conActId'	=> 54,
			'label'		=> 'deposit_by_branch',
			'active'	=> true,
		),
		array(
			'conActId'	=> 50,
			'label'		=> 'profile_transactions',
			'active'	=> true,
		),
		array(
			'conActId'	=> 58,
			'label'		=> 'point_transactions',
			'active'	=> true,
		),
		array(
			'conActId'	=> 85,
			'label'		=> 'entry_bonus',
			'active'	=> true,
		),
		array(
			'conActId'	=> 91,
			'label'		=> 'voucher',
			'active'	=> true,
		),
		array(
			'conActId'	=> 87,
			'label'		=> 'ticket_cancel',
			'active'	=> true,
		),
		array(
			'conActId'	=> 103,
			'label'		=> 'setting_limits',
			'active'	=> true,
		)
	);

	private static $conActLoggedIn		= array(35, 51, 52, 53, 54, 50, 58, 85, 87,91, 103);
	private static $conActNotLoggedIn	= array(53, 54);

    //-- todo remove unused
    private static $timeFilterCfg = array(
        /* REQUEST key          => value */
        /*'onlyToday'             => Models_Markets_MarketData::TIME_FILTER_ONLY_TODAY,
        'todayAndTomorow'       => Models_Markets_MarketData::TIME_FILTER_TODAY_AND_TOMOROW,
        'today'                 => Models_Markets_MarketData::TIME_FILTER_TODAY,
        'tomorow'               => Models_Markets_MarketData::TIME_FILTER_TOMOROW,
        'dayAfterTomorow'       => Models_Markets_MarketData::TIME_FILTER_DAY_AFTER_TOMOROW,
        'oneHour'               => Models_Markets_MarketData::TIME_FILTER_1_HOUR,
        'threeHours'            => Models_Markets_MarketData::TIME_FILTER_3_HOURS,
        'sixHours'              => Models_Markets_MarketData::TIME_FILTER_6_HOURS,
        'twelveHours'           => Models_Markets_MarketData::TIME_FILTER_12_HOURS,
        'weekend'               => Models_Markets_MarketData::TIME_FILTER_WEEKEND,
        'week'                  => Models_Markets_MarketData::TIME_FILTER_WEEK,
        'tenMinutes'            => Models_Markets_MarketData::TIME_FILTER_10_MINUTES,
        'twentyMinutes'         => Models_Markets_MarketData::TIME_FILTER_20_MINUTES,
        'thirtyMinutes'         => Models_Markets_MarketData::TIME_FILTER_30_MINUTES,
        'datetimeRange'         => Models_Markets_MarketData::TIME_FILTER_DATETIME_RANGE*/
    );


    static function leftAndRightColAndNews($view) {
		Models_Helpers_Panels::leftCol($view);
		Models_Helpers_Panels::rightCol($view);
		Models_Helpers_Panels::news($view);
		$view->defaultCols = true;
		
		// fix pro sjednocení systému sloupců
		$view->addHelperPath('views/helpers', 'My_View_Helper');
		$menu = Models_Helpers_Panels::sportMenu($view);

		$openUrlArr = $menu->getOpenUrl();

		$urlParts = array(
			$openUrlArr['sport'] => 1,
			$openUrlArr['oblast'] => 2,
			$openUrlArr['udalost'] => 3
		);
		$view->urlParams = array();
		$view->urlParams = $menu->getAllLangUrlParams($urlParts);
	}

	static function leftCol($view) {
		Models_Helpers_Panels::sportMenu($view);
	}

	static function rightCol($view) {
		// init coupon data
		Models_Ajax_Ticket::get($view);

		//Models_Helpers_Panels::monthTicket($view);
		//Models_Helpers_Panels::liveCalendarSmall($view);
		$view->rightCol = true;
	}

	static function news($view) {
		$view->news = Models_Marketing_Promo::news();
	}

	/**
	 * @deprecated Moved to AJAX controller
	 * @param Zend_View $view
	 */
	static function monthTicket($view) {
		// $ws = Zend_Registry::get('ws');
		// $tickets = $ws->Ticket->getMonthTicketComplete();
		// $tickets = It6_ArrayWrapper::toNativeArray($tickets);

		// $view->month = $tickets;
		// if ( !is_array($view->month) || count($view->month) == 0 ) {
			$view->month = array();
		// }

	}

    static function timeFilter($view) {
        if ( !in_array(Zend_Registry::get('c_id'), array(2, 84, 86, 96)) ) { // this is really inconvenient
            // controller Sportsbook action index (2),
            // controller Ajax action sportsbook (84), sport-menu (86), sport-menu-all (96)
			$view->timeFilter = null;
			$view->onlyToday = 0;
			$view->todayAndTomorow = 0;
			$view->all = 0;
		} else {
            
            $timeFilterSet = false;
            foreach (self::$timeFilterCfg as $requestKey => $value) {
                if (isset($_REQUEST[$requestKey]) || 
                        isset($_REQUEST['qselect']) && $_REQUEST['qselect'] === $requestKey) {
                    
                    $_GET[$requestKey] = 1;
                    $view->timeFilter = $value;
                    
                    $view->{$requestKey} = 1;
                    
                    $view->onlyToday = ($requestKey !== 'onlyToday') ? 0 : 1;
                    $view->todayAndTomorow = ($requestKey !== 'todayAndTomorow') ? 0 : 1;
                    $view->all = ($requestKey !== 'all') ? 0 : 1;
                    
                    $timeFilterSet = true;
                    break;
                }
            }
            if (!$timeFilterSet) {
                $view->timeFilter = null;
                $view->todayAndTomorow = 0;
                $view->onlyToday = 0;
                $view->all = 1;
            }
        }
        
		$view->typeFilter = '';
		/*if(isset($_REQUEST['type'])) {
			foreach ($_REQUEST['type'] as $type)
				$view->typeFilter .= '&type[]='.$type;
		}

		if (isset($_SESSION["rateMin"]) && isset($_SESSION["rateMax"])){
			$view->rateMin = str_replace(",", ".", $_SESSION["rateMin"]);
			$view->rateMax = str_replace(",", ".", $_SESSION["rateMax"]);
		}

		$view->dateFrom = '';
		$view->dateTo = '';
		if ($view->timeFilter === Models_Markets_MarketData::TIME_FILTER_DATETIME_RANGE) {
			$view->dateFrom = isset($_REQUEST["dateFrom"]) ? $_REQUEST["dateFrom"] : '';
			$view->dateTo = isset($_REQUEST["dateTo"]) ? $_REQUEST["dateTo"] : '';
		}*/
	}
    
	static function sportMenu($view, $justMenuObject = false) {
		
		static::timeFilter($view);
        
		//$view->fastUrl = Zend_Registry::get('c_id') != 2 ? $view->UrlSet(2) : '';
		$view->fastUrl = $view->UrlSet(2);
		$view->realUrl = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        
		$menu = new Models_Navigation_SportMenu;
		$view->menu = $menu;
		if ( !$justMenuObject ) {
			/* deprecated
			$view->tenisTodayCount = $menu->getTodayTenisCount();
			$view->fotbalTodayCount = $menu->getTodayFotbalCount();
			*/
			$view->allCount = $menu->getAllCount();
			$view->todayCount = $menu->getTodayCount();
			$view->todayAndTomorowCount = $menu->getTodayAndTomorowCount();
			/* deprecated
			$view->quickOfferCountTotal = $view->tenisTodayCount + $view->fotbalTodayCount
				+ $view->todayCount + $view->todayAndTomorowCount;
			*/
			
			$view->sportMenu = $menu->getMenu($view->timeFilter);
			$view->OblastIP    = $menu->getOblastIP();
			$view->isOpen = $menu->isOpen;
		}


		$openUrlArr = $menu->getOpenUrl();

		$view->allSports = ( in_array(Zend_Registry::get('c_id'), array(2, 86)) && 0 == $openUrlArr['sport'] && 0 == $openUrlArr['oblast'] && 0 == $openUrlArr['udalost'] );

		$view->allSportsUrl = $view->UrlSet(2);
		$view->openMenuPath = $menu->getOpenPath();
        
        /*$timeFilterApplied = false;
        foreach (self::$timeFilterCfg as $requestKey => $value) {
            if ($view->timeFilter == $value) {
                $view->allSportsUrl .= '?'.$requestKey.'=1';
                $view->openMenuPath .= '?'.$requestKey.'=1';
                
				if ($value == Models_Markets_MarketData::TIME_FILTER_DATETIME_RANGE) {
					$view->allSportsUrl .= '&dateFrom='.urlencode($view->dateFrom).'&dateTo='.urlencode($view->dateTo);
					$view->openMenuPath .= '&dateFrom='.urlencode($view->dateFrom).'&dateTo='.urlencode($view->dateTo);
				}
				
                $timeFilterApplied = true;
                break;
            }
        }
		if (!$timeFilterApplied) {
            if (isset($view->rateMin) && isset($view->rateMax)) {
                $view->allSportsUrl .= '?rateMax=' . $view->rateMax . '&rateMin=' . $view->rateMin;
                $view->openMenuPath .= '?rateMax=' . $view->rateMax . '&rateMin=' . $view->rateMin;
            }
        }*/
        
		It6_GlobalCache::maxExpiration( max(0, $menu->getExpiration() - time()) );

		//Zend_Registry::get('fl')->info($menu);

		return $menu;
	}

	static function liveCalendarSmall($view) {
		$view->liveOnline = Models_LiveBetting_Calendar::getOnLine(1);
		$view->liveComing = Models_LiveBetting_Calendar::getComming(1);
	}

	static function getPersonalMenuItems() {
		$outData	= array();
		$userId		= Zend_Registry::get('user_id');
		
		if(!empty($userId))
			$conActIds = self::$conActLoggedIn;
		else
			$conActIds = self::$conActNotLoggedIn;
		
		
		if(ALLOW_WEB_CANCEL_TICKET == 0) {
			$conActIds = array_filter($conActIds, function($element) {
				return ($element != 87);
			});
		} 
		
		foreach(self::$leftMenuItems as $item) {
			if(in_array($item['conActId'], $conActIds))
				$outData[] = $item;
		}
		
		return $outData;
	}

	public static function getRangeValues() {
		$range = Webservice_Parameter::getGlobalParameter('web.JqueryRangeSliderValues');
		$array = explode(',', $range);
		$error = false;

		foreach ($array as $value) {
			if (!is_numeric($value)) $error = true;
		}

		if ($error == false) {
			return $range;
		} else {
			// defaultni hodnoty
			$values = array();
			$i = 0.9;
			while ($i < 100) {
				if (round($i, 1) < 4) {
					$i += 0.1;
					$values[] = $i;
				} else if ($i <= 10) {
					$i += 1;
					$values[] = $i;
				} else if ($i > 10 && $i < 30) {
					$i = 30;
					$values[] = $i;
				} else if ($i == 30) {
					$i = 50;
					$values[$i] = $i;
				} else if ($i == 50) {
					$i = 100;
					$values[$i] = $i;
				}
			}
			return implode(", ", $values);
		}
	}
	
	public static function setUrlFilterParams($timeFilter) {
		if ($timeFilter == null) {
			return '';
		}
		
		/*foreach (self::$timeFilterCfg as $requestKey => $value) {
			if ($timeFilter == $value) {
				if ($timeFilter !== Models_Markets_MarketData::TIME_FILTER_DATETIME_RANGE) {
					return '?'.$requestKey.'=1';
				} else {
					return '?'.$requestKey.'=1&dateFrom='.urlencode($_REQUEST['dateFrom']).'&dateTo='.urlencode($_REQUEST['dateTo']);
				}
			}
		}*/
		
		return '';
	}
	
	public static function getOfferPosfixFromTimeFilter($timeFilter) {
		$filterKey_name = array(
			'onlyToday'             => 'today_quick_offer',
			'today'                 => 'today_quick_offer',
			'tomorow'               => 'tomorow_quick_offer',
			'oneHour'               => '1_hour_quick_offer',
			'threeHours'            => '3_hours_quick_offer',
			'sixHours'              => '6_hours_quick_offer',
			'twelveHours'           => '12_hours_quick_offer',
			'weekend'               => 'weekend_quick_offer',
			'week'                  => 'week_quick_offer',
			'datetimeRange'         => 'datetimerange_quick_offer'
		);
		foreach (self::$timeFilterCfg as $requestKey => $value) {
			if ($timeFilter == $value) {
				return ': '.mb_strtolower(Zend_Registry::get('translate')->trans($filterKey_name[$requestKey]), 'UTF-8');
			}
		}
		
		return '';
	}
	
	public static function getTimeFilterHidden() {
		$result = '';
		foreach (self::$timeFilterCfg as $requestKey => $value) {
			if (isset($_REQUEST[$requestKey])) {
				$result .= '<input type="hidden" name="'.$requestKey.'" value="1" />';
				
				$result .= isset($_REQUEST['dateFrom']) ? '<input type="hidden" name="dateFrom" value="'.htmlspecialchars($_REQUEST['dateFrom'], ENT_NOQUOTES, "UTF-8").'" />' : '';
				$result .= isset($_REQUEST['dateTo']) ? '<input type="hidden" name="dateTo" value="'.htmlspecialchars($_REQUEST['dateTo'], ENT_NOQUOTES, "UTF-8").'" />' : '';
				
			}
		}
		return $result;
	}
	
	public static function getRateFilterHidden() {
		$result = '';
		if (isset($_REQUEST['rateMin'])) {
			$result .= '<input type="hidden" name="rateMin" value="'.htmlspecialchars($_REQUEST['rateMin'], ENT_NOQUOTES, "UTF-8").'" />';
		}
		if (isset($_REQUEST['rateMax'])) {
			$result .= '<input type="hidden" name="rateMax" value="'.htmlspecialchars($_REQUEST['rateMax'], ENT_NOQUOTES, "UTF-8").'" />';
		}
		return $result;
	}
}