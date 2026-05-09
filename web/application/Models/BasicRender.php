<?php
class Models_BasicRender {

	/**
	* spojeni na databazi game
	* @access public
	* @var DB
	*/
	public static $guSth;

	/**
	* spojeni na databazi game
	* @access public
	* @var DB
	*/
	public static $guSth2;

	/**
	* iso kody jazyka
	* @access public
	* @var array
	*/
	public static $iso;

	/**
	* zakladni render
	* @param object $view
	* @return voir
	*/
	public static function render($view) {

		$db = Zend_Registry::get('db');
		$t = array();

		if (Zend_Registry::isRegistered('translate')) {
			$t = Zend_Registry::get('translate');
		}

		$langId = $_SESSION['lang_id'];
		$view->lang = self::lang();
		$view->lang_id = $langId;
		$view->isoLang = $_SESSION['lang'];

		$aFlagIcon = array(1 => "ico-cze",
						   2 => "ico-eng",
						   16 => "ico-svk",
						   17 => "ico-ger",
						   18 => "ico-rus");
		
		$view->flagIcon = $aFlagIcon[$langId];

		$userId = null;
		if (Zend_Registry::isRegistered('user_id')) {
			$userId = Zend_Registry::get('user_id');
			$user = array();
			if (!empty($userId)) {
				$user = It6_Models_User::getData($userId, $db);
				$view->user = $user;
				if (empty($user))
					throw new Exception('Unknown user or inconstisten user data: id=' . $userId);
			}
			else
				$userId = 0;
		}

		if (!empty($userId))
			$currencyId = $user['currencyId'];
		else if (!empty($langId))
			$currencyId = It6_Models_Language::get($langId, 'currencyId', $db);
		else
			$currencyId = It6_Models_Currency::getCurrencyIdByIso(DEFAULT_CURRENCY_ISO, $db);

		if (empty($currencyId))
			throw new Exception('Undetermined currency');
		$currency = It6_Models_Currency::getData($currencyId, $db);
		$view->currencyName = $currency['name'];
		$smallestUnitStake = $currency['smallestCash'];

		if (1 > $smallestUnitStake)
			$smallestUnitStake = 1;

		$view->currencyPrecisionStake = ceil(log10($smallestUnitStake));

		$smallestUnitWin = $currency['smallestUnit'];

		if (1 > $smallestUnitWin)
			$smallestUnitWin = 1;

		$view->currencyPrecisionWin = ceil(log10($smallestUnitWin));
		$view->title   = self::title();
		$view->description = self::description();
		$view->keywords = self::keywords();
		$view->menu    = self::menu();

		self::balance();
		$view->log = $GLOBALS['ses_status'];

		if (Zend_Registry::isRegistered('zustatek')) {
			//TODO doplnit zaokurouhleni na nejmensi platidlo, nyni natvrdo 2
			$view->balance = Zend_Registry::get('zustatek');
			$view->balanceFormated = $view->formatCurrency($view->balance) .' '. Zend_Registry::get('mena');
		} else {
			$view->balance = '';
		}

		if (Zend_Registry::isRegistered('points')) {
			$view->points = round(Zend_Registry::get('points'), 0);
			$view->pointsFormated = $view->formatFloat($view->points, 0) . ' B';
		} else {
			$view->points = '';
		}

		$view->ebVersion = false;
		if (isset($user['ebVersion'])) {
			if (1 == $user['ebVersion']) {
				if (
					isset($user['ebBalance'])
					&& isset($user['ebFrom'])
					&& !isset($user['ebApplied'])
				) {
					$view->ebVersion = 1;
					$view->ebBalance = $user['ebBalance'];
					$view->ebBase = $user['ebBase'];
					$view->ebAmount = $user['ebAmount'];
				}
			}
		}
	}

	private static function getControllerConvert() {
		$cid = Zend_Registry::get('c_id');
		$lid = $_SESSION['lang_id'];
		return It6_Models_ControllerConvert::getByIdAndLang($cid, $lid);
	}

	/**
	* Balance uzivatele
	* @return bool
	*/
	public static function balance() {

		if ($GLOBALS['ses_status'] == 2 || $GLOBALS['ses_status'] == 3) {
			try{
				$user = Zend_Registry::get('ws')->User->getById(Zend_Registry::get('user_id'));
			}
			catch ( Exception $e ) {
				Models_Exception_Handler::handle($e);
			}

			if( !empty($user) ) {
				Zend_Registry::set('mena', $user->currencyName);
				Zend_Registry::set('zustatek', $user->balance);
				Zend_Registry::set('points',$user->points[0]->balance);
				Zend_Registry::set('jmeno', $user->firstName);
				Zend_Registry::set('prijmeni', $user->lastName);
				Zend_Registry::set('nick', $user->firstName.' '.$user->lastName); // *
				// * bylo $user->username, to bylo pro login nahrazeno emailem a nick se nepoužívá
				// * to-do v grafice: pokud bude celé jméno dlouhé, nějak to zkrátit
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	* Menu aktualni id
	* @return voir
	*/
	public static function menu() {
		$cc = self::getControllerConvert();
		return (empty($cc) || empty($cc['controllerId']) ? 0 : $cc['controllerId']);
	}

	/**
	* title
	* @return string
	*/
	public static function title() {
		$cc = self::getControllerConvert();
		return (empty($cc) || empty($cc['title']) ? 'Sázejte u CompBet' : $cc['title'] . ' | CompBet');
	}

	/**
	* jazyky
	* @return voir
	*/
	public static function lang() {

		$result = array();
		$langs = It6_Models_ControllerConvert::getLangs();
		$langs = array_flip($langs);
		krsort($langs);
		$ccs = It6_Models_ControllerConvert::getById(Zend_Registry::get('c_id'), false);

		foreach ($langs as $lid => $iso) {
			if (empty($ccs[$lid]) || empty($ccs[$lid]['visible'])) {
				$show = false;
				$url = It6_Models_ControllerConvert::buildUrl($iso, 'index', 'index');
			} else {
				$show = ($_SESSION['lang'] == $iso ? 1 : 0);
				$url = It6_Models_ControllerConvert::buildUrl($iso, $ccs[$lid]['reqController'], $ccs[$lid]['reqAction']);
			}
			$result[$iso] = array(
				'show' => $show,
				'url' => $url,
				'name' => mb_strtoupper($iso),
			);
		}
		return $result;
	}

	/**
	* description
	* @return string
	*/
	public static function description() {
		$cc = self::getControllerConvert();
		return (empty($cc) || empty($cc['description']) ? '' : $cc['description']);
	}

	/**
	* Keywords
	* @return string
	*/
	public static function keywords() {
		$cc = self::getControllerConvert();
		return (empty($cc) || empty($cc['keywords']) ? '' : $cc['keywords']);
	}
}