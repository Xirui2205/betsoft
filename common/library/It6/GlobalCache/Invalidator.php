<?php
class It6_GlobalCache_Invalidator {

/**
 * Creates standard list of values from given ControllerConvert data.
 * NOTE: for actionless controllers is action taken from extra parameters
 * @param string $langIso
 * @param array $data Controller convert data, required field: reqController, reqAction, actionless
 * @param array $params Additional URL path components as extra parameters
 * @return array Standardized list of values: array(langIso, reqController, reqAction, actionlessFlag, param1, param2, param3)
 */
private static function makeResource($langIso, $data, $params = array()) {
	if (!empty($data['actionless'])) {
		if (empty($params))
			$action = 'index'; // this is bust, we could miss page with only one parameter without value
		else
			$action = array_shift($params);
	}
	else
		$action = $data['reqAction'];
	$params = array_values($params);
	$param1 =  (empty($params[0]) ? '' : $params[0]);
	$param2 =  (empty($params[1]) ? '' : $params[1]);
	$param3 =  (empty($params[2]) ? '' : $params[2]);
	return array($langIso, $data['reqController'], $action, $data['actionless'], $param1, $param2, $param3);
}

/**
 * Takes controller ID and optionaly language ID and returns data for cache resource(s)
 * @param integer $controllerId ID of controller convert record
 * @param integer|NULL $langId If empty value is specified, all languages are fetched
 * @param array $params Additional URL path components as extra parameters
 * @return array|FALSE One language: One resource or FALSE; all languages: list of resources (could be empty);
 *                     @see makeResource() 
 */
private static function translateResource($controllerId, $langId = null, $params = null) {
	if (empty($langId)) {
		$data = It6_Models_ControllerConvert::getById($controllerId, true);
		if (empty($data)) {
			return array();
		}
		if (empty($params)) {
			$params = array();
		}
		$result = array();
		foreach ($data as $langIso => $ctl)
			$result[] = self::makeResource($langIso, $ctl, $params);
		return $result;
	}
	else {
		$ctl = It6_Models_ControllerConvert::getByIdAndLang($controllerId, $langId);
		if (empty($ctl))
			return array();
		else {
			$langIso = It6_Models_ControllerConvert::getLangIsoById($langId);
			return self::makeResource($langIso, $ctl, $params);
		}
	}
}

/**
 * Invalidates by controller and/or action and/or whatever specified resources,
 * the set of resources is determined by version variable to be invalidated and by session specification.
 * NOTE: For 'G' version just invalidates whole WC tree, no other parameters are taken into account.
 * @param array $resources List or resources as returned from translateResource()
 * @param string $version Version variable
 * @param string|boolean $session Session ID or TRUE for current session or FALSE for no session (doesn't matter for non-session versions)
 * @return boolean
 */
private static function invalidateResources($resources, $version = null, $session = false) {
	if ('G' == $version)
		return It6_GlobalCache::deleteKeys('WC', array('G' => 'WC')); // special case (whole WC)

	if (empty($resources))
		return false;

	if (empty($version))
		// default is controller->action or controller->action->session
		$version = (false === $session ? 'CA' : 'CAS');

	if (false === $session)
		$session = 'N';
	else if (true === $session)
		$session = It6_GlobalCache_Starter::getSessionPartition();
	else
		$session = It6_GlobalCache_Starter::getSessionPartition($session);

	$deleted = array();
	foreach ($resources as $resource) {
		$_version = $version;
		if (!empty($resource[3])) {	// actionless
			// strip out action version
			switch ($version) {
				case 'CA':
					$_version = 'C';
					break;
				case 'CAS':
					$_version = 'CS';
					break;
				case 'CAP1':
					$_version = 'CA';
					break;
				case 'CAP12':
					$_verion = 'CAP1';
					break;
				case 'CAP123':
					$_version = 'CAP12';
					break;
				default:
					break;
			}
		}

		$skip = false;
		switch ($_version) {
			case 'C':
				$partition = "{$resource[0]}:{$resource[1]}";
				break;
			case 'CA':
				$partition = "{$resource[0]}:{$resource[1]}:{$resource[2]}";
				break;
			case 'S':
				$partition = $session;
				break;
			case 'CS':
				$partition = "{$resource[0]}:$session";
				break;
			case 'CAS':
				$partition = "{$resource[0]}:{$resource[1]}:{$resource[2]}:$session";
				break;
			case 'CAP1':
				$partition = "{$resource[0]}:{$resource[1]}:{$resource[2]}:{$resource[4]}";
				break;
			case 'CAP12':
				$partition = "{$resource[0]}:{$resource[1]}:{$resource[2]}:{$resource[4]}:{$resource[5]}";
				break;
			case 'CAP123':
				$partition = "{$resource[0]}:{$resource[1]}:{$resource[2]}:{$resource[4]}:{$resource[5]}:{$resource[6]}";
				break;
			default:
				$skip = true;
		}
		if (!$skip && empty($deleted[$_version][$partition])) {
			It6_GlobalCache::deleteKeys('WC', array($_version => $partition));
			$deleted[$_version][$partition] = true;
		}
	}
}

private static function getUserSessionIds($userId) {
	if (empty($userId))
		return false;
	// add 'status' column constraint? probably not
	$stmt = Zend_Registry::get('dbSes')->query('SELECT * FROM session WHERE user_id=' . intval($userId));
	$ids = array();
	while ($row = $stmt->fetch())
		$ids[] = $row['ses_id'];
	return $ids;
}

/**
 * Maps invalidations of user resources to invalidations of session resources.
 * @param integer $userId Given user or if unknown user (empty value) then use current session
 * @param array $resources List or resources as returned from translateResource()
 * @param string $version Version variable (really should contain 'S', it will be mostly 'CAS')
 * @return boolean
 */
private static function invalidateResourcesByUserId($userId, $resources, $version = null) {
	if (empty($userId))
		return self::invalidateResources($resources, $version, true);
	else {
		$sessionIds = self::getUserSessionIds($userId);
		if (empty($sessionIds))
			return false;
		$result = true;
		foreach ($sessionIds as $id) {
			if (!self::invalidateResources($resources, $version, $id))
				$result = false;
		}
		return $result;
	}
}

private static function dbt($dbt) {
	$b = ''; $n = 0;
	foreach ($dbt as $r)
		$b .= ++$n . " {$r['file']} ({$r['line']})\n";
	return $b;
}

public static function invalidateAllWebContent() {
	self::invalidateResources(array(), 'G');
}

/**
 * Invalidates classic controller-action page cached content.
 * @param integer|array $controllerId One or list of IDs from controller convert table
 * @param integer|NULL $langId If empty value is specified, all languages are invalidated
 */
public static function invalidateWebPage($controllerId, $langId = null) {
	$res = array();
	if (!is_array($controllerId)) {
		$controllerId = array($controllerId);
	}
	foreach ($controllerId as $id) {
		$res = array_merge($res, self::translateResource($id, $langId));
	}
	if (!empty($res)) {
		self::invalidateResources($res, 'CA');
	}
}

public static function invalidateLogoutFrame() {
//file_put_contents('/tmp/cache-debug.log', self::dbt(debug_backtrace()), FILE_APPEND);
	$res = self::translateResource(66); // global-cache-frame/logout
	self::invalidateResources($res, 'CAS', true);
	
//	It6_GlobalCache::invalidate(
//		'/web/*/global-cache-frame/logout'
//		. It6_GlobalCache_Starter::getSessionPostfix());

	self::invalidateTicketFrame();
}

public static function invalidateTicketFrame() {
	$res = self::translateResource(67); // global-cache-frame/ticket
	self::invalidateResources($res, 'CAS', true);
//	It6_GlobalCache::invalidate(
//		'/web/*/global-cache-frame/ticket'
//		. It6_GlobalCache_Starter::getSessionPostfix());
}

/**
 * Invalidate all frames that are bound to logged-in user
 */
public static function invalidateUserFrames() {
	static::invalidateLogoutFrame();
	static::invalidateTicketFrame();
}

public static function invalidateSportMenuFrame() {
	$res = self::translateResource(86); // ajax-menu (actionless)
	self::invalidateResources($res, 'C', false);
//	It6_GlobalCache::invalidate(
//		'/web/*/global-cache-frame/sport-menu'
//		. It6_GlobalCache_Starter::NO_SESSION);
}

public static function invalidateSportMenuFavFrame() {
	$res = self::translateResource(99); // ajax-menu-fav (actionless)
	self::invalidateResources($res, 'C', false);
//	It6_GlobalCache::invalidate(
//		'/web/*/global-cache-frame/sport-menu'
//		. It6_GlobalCache_Starter::NO_SESSION);
}

//TODO: correct klingon madness, sport names are shit, IDs (of controller or of sport) or anything would be better
public static function invalidateSportsbook($sportId = null, $regionId = null, $eventId = null) {
	$res1 = self::translateResource(45); // vyhledavani
	$res2 = self::translateResource(4); // dnesni-nabidka
	$res3 = self::translateResource(5); // dnes-a-zitra
	$res4 = self::translateResource(9); // detail
	$res5 = array();
	if (null != $sportId) {
		// sport name => controller ID (what an idea!)
		$table = array(1001 => 6, 1003 => 7);
		if (array_key_exists($sportId, $table))
			$res5 = self::translateResource($table[$sportId]);
	}
	self::invalidateResources(array_merge($res1, $res2, $res3, $res4, $res5), 'C', false);

	//TODO: !!! translate sports, use params:
	// array(sport, region, event) ... CAP12
	// array(sport, region) ... CAP1
	// array(sport) ... CA
//	if (null != $sportId) {
//		$version = 'CAP12';
//		$params = array($sportName);
//		if (null != $regionName) {
//			$params[] = $regionName;
//			if (null != $eventName)
//				$params[] = $eventName;
//		}
//		else if (null != $eventName)
//			$params[] = $eventName;
//		$res1 = self::translateResource(2, null, $params); // actionless sazky + params
//	}
//	else {
//		$version = 'C';
//		$params = array();
//		$res1 = self::translateResource(2); // actionless sazky
//	}
//	self::invalidateResources($res1, $version, false);
	$level = 0;
	if (null != $sportId) {
		++$level;
		$db = null;
		//$langs = It6_Models_Language::getAllDisplayed($db);
		$sportNames = It6_Models_Sportsbook::getAllLangsUrls($sportId, It6_Models_Sportsbook::URL_TYPE_SPORT, $db);
		if (null != $regionId) {
			++$level;
			$regionNames = It6_Models_Sportsbook::getAllLangsUrls($regionId, It6_Models_Sportsbook::URL_TYPE_REGION, $db);
		}
		if (null != $eventId) {
			++$level;
			$eventNames = It6_Models_Sportsbook::getAllLangsUrls($eventId, It6_Models_Sportsbook::URL_TYPE_EVENT, $db);
		}

		$params = array();
		foreach ($sportNames as $lang => $name)
			$params[$lang] = array($name);
		if (!empty($regionNames)) {
			foreach ($sportNames as $lang => $name)
				$params[$lang][] = $regionNames[$lang];
		}
		if (!empty($eventNames)) {
			foreach ($sportNames as $lang => $name)
				$params[$lang][] = $eventNames[$lang];
		}
		$res1 = array(); // resources to be invalidated with level version
		$res2 = array(); // upper levels' resources to be invalidated with CAP12 version
		$ctls = array();
		$ctls[] =  array('upperLevels' => true, 'data' => It6_Models_ControllerConvert::getById(2, true)); // sazky (actionless)
		$ctls[] = array( 'upperLevels' => false, 'data' => It6_Models_ControllerConvert::getById(84, true)); // ajax-sazky (actionless)
		foreach ($ctls as $ctl) {
			foreach ($ctl['data'] as $_ctl) {
				$lang = $_ctl['langIso'];
				if (!empty($params[$lang])) {
					$res1[] = self::makeResource($lang, $_ctl, $params[$lang]);
					if (!empty($ctl['upperLevels'])) {
						for ($n = 0; $n < $level; ++$n) {
							$_params = array_slice($params[$lang], 0, $n);
							$res2[] = self::makeResource($lang, $_ctl, $_params);
						}
					}
				}
			}
		}
		if (!empty($res1)) {
			$levelVersion = array('C', 'CA', 'CAP1', 'CAP12');
			$version = $levelVersion[$level];
			self::invalidateResources($res1, $version, false);
		}
		if (!empty($res2))
			self::invalidateResources($res2, 'CAP12', false); //  ? use $version instead of 'CAP12' ? (but most useful it is for CAP12)
	}
	else {
		$res1 = self::translateResource(2); // sazky (actionless)
		$res2 = self::translateResource(84); // ajax-sazky (actionless)
		self::invalidateResources(array_merge($res1, $res2), 'C', false);
	}

	self::invalidateHomePage();
	
/* DEPRECATED:
	It6_GlobalCache::invalidate('/web/cs/vyhledavani' . It6_GlobalCache_Starter::NO_SESSION);
	It6_GlobalCache::invalidate('/web/cs/dnesni-nabidka' . It6_GlobalCache_Starter::NO_SESSION);
	It6_GlobalCache::invalidate('/web/cs/dnes-a-zitra' . It6_GlobalCache_Starter::NO_SESSION);
	It6_GlobalCache::invalidate('/web/cs/detail*' . It6_GlobalCache_Starter::NO_SESSION);

	//NOTE: I think, this is another klingon's madness, look down and see, it always invalidates
	//      all the "/web/cs/sazky" tree, doesn't matter if there is region and/or event
	$root = '/web/cs/sazky';
	$paths = array($root);
	$additionalPaths = array();
	if ( null != $sportName ) {
		$paths[] = $root . '/' . $sportName;
		$additionalPaths[] = "/web/cs/$sportName-dnes";
		if ( null != $regionName )
			$paths[] = $root . '/' . $sportName . '/' . $regionName;
		if ( null != $eventName ) {
			if ( null != $retionId )
				$paths[] = $root . '/' . $sportName . '/' . $regionName . '/' . $eventName;
			else
				$paths[] = $root . '/' . $sportName . '/' . $eventName;
		}
	}

	while ( $path = array_shift($paths) ) {
		It6_GlobalCache::invalidate($path . It6_GlobalCache_Starter::NO_SESSION);
		if ( empty($paths) )
			It6_GlobalCache::invalidate($path);
	}

	foreach ( $additionalPaths as $path )
		It6_GlobalCache::invalidate($path);
*/

}

public static function invalidateSportsbookByBet($betId, $db = null) {
	// get bet data: event, region, sport, time
	if (!isset($db))
		$db = Zend_Registry::get('db');
	$rows = $db->query(
			'SELECT e.sport_id AS sportId, e.oblast_id AS regionId, b.udalost_id AS eventId, b.platna_do AS validTo'
			. ' FROM sazky b JOIN udalost e ON b.udalost_id=e.udalost_id'
			. ' WHERE b.sazka_id=' . intval($betId)
		)->fetchAll();
	if (!is_array($rows) || empty($rows))
		return;
//	require_once(ROOT . 'web/application/Models/Navigation/MenuAbstract.php');
//	require_once(ROOT . 'web/application/Models/Navigation/SportMenu.php');

	$bet = $rows[0];
	self::invalidateSportsbook($bet['sportId'], $bet['regionId'], $bet['eventId']);

	// invalidate lastMinute bets?
	if (self::isBetIdCached(It6_GlobalCache::KEY_PREFIX_LAST_MINUTE_BETS, $betId))
		self::invalidateLastMinuteBets();
	// invalidate terno bets?
	if (self::isBetIdCached(It6_GlobalCache::KEY_PREFIX_TERNO_BETS, $betId))
		self::invalidateTernoBets();
	// invalidate supertip bets?
	if (self::isBetIdCached(It6_GlobalCache::KEY_PREFIX_SUPERTIP_BETS, $betId))
		self::invalidateSupertipBets();
		
}

private static function isBetIdCached($key, $betId) {
	$ids = It6_GlobalCache::getKey($key);
	if (empty($ids))
		return false;
	$ids = explode(';', $ids);
	foreach ($ids as $id) {
		$id = trim($id);
		if ($id == $betId)
			return true;
	}
	return false;
}

public static function invalidateLastMinuteBets() {
	$res = self::translateResource(81);
	self::invalidateResources($res, 'CA');
	It6_GlobalCache::deleteKey(It6_GlobalCache::KEY_PREFIX_LAST_MINUTE_BETS);
}

public static function invalidateTernoBets() {
	$res = self::translateResource(82);
	self::invalidateResources($res, 'CA');
	It6_GlobalCache::deleteKey(It6_GlobalCache::KEY_PREFIX_TERNO_BETS);
}

public static function invalidateSupertipBets() {
	$res = self::translateResource(110);
	self::invalidateResources($res, 'CA');
	It6_GlobalCache::deleteKey(It6_GlobalCache::KEY_PREFIX_SUPERTIP_BETS);
}

/**
 * @param integer $userId
 */
public static function Transaction_changeBalance($userId) {
	$res1 = self::translateResource(66); // global-cache-frame/logout
	$res2 = self::translateResource(67); // global-cache-frame/ticket
	self::invalidateResourcesByUserId($userId, array_merge($res1, $res2), 'CAS');

//	It6_GlobalCache::invalidate('/web/*/global-cache-frame/logout*/*/*userId~'.$userId.'*');
//	It6_GlobalCache::invalidate('/web/*/global-cache-frame/ticket*/*/*userId~'.$userId.'*');
}

public static function PointsTransaction_changeBalance($userId) {
	self::Transaction_changeBalance($userId);
	//self::invalidateResources(array_merge($res1, $res2), 'CAS');
}

public static function HappyHour_userHappyHour($type) {
	$res1 = self::translateResource(66); // global-cache-frame/logout
}

public static function Livebetting_setMatches() {
	It6_GlobalCache::useSession();
	$langs = It6_Models_ControllerConvert::getLangs();
	
	$res1 = self::translateResource(71); // global-cache-frame/live-calendar-small
	$res2 = self::translateResource(72); // global-cache-frame/live-calendar
    $res3 = self::translateResource(3); // live-sazky

	 self::invalidateResources(array_merge($res1, $res2, $res3), 'CA');
	    $adminDB = Zend_Registry::get("db");

	foreach ($langs as $iso => $id) {
		Zend_Registry::set("translate", new It6_Translate_Admin($id, $adminDB));
		$liveCalendarKey = It6_GlobalCache::createLocalizedKey(It6_GlobalCache::KEY_PREFIX_LIVE_CALENDAR, $iso);
		$liveCalendarSmallKey = It6_GlobalCache::createLocalizedKey(It6_GlobalCache::KEY_PREFIX_LIVE_CALENDAR_SMALL, $iso);
		$view = new Zend_View();
		$view->setScriptPath(ROOT .'web/application/views/scripts/');
		$view->addHelperPath('It6/View/Helper', 'It6_View_Helper_');
		$view->liveOnline = Webservice_MatchLive::getHpCalendarSmallOnLine();
		$view->liveComing = Webservice_MatchLive::getHpCalendarSmallComing();
		$html = $view->render('global-cache-frame/live-calendar-small-content.phtml');
		unset($view->liveOnline);
		unset($view->liveComing);
		It6_GlobalCache::setKey($liveCalendarSmallKey, $html);
		$view->liveCalendarMatches = Webservice_MatchLive::getHpCalendar();
		$this->view->liveCom = Models_LiveBetting_Calendar::getComming(false);
		$html = $view->render('global-cache-frame/live-calendar-content.phtml');
		unset($view->liveCalendarMatches);
		It6_GlobalCache::setKey($liveCalendarKey, $html);

		}
	}

public static function Banner_update() {
	It6_Memcached::flush();
}

public static function invalidateHomePage() {
	$res1 = self::translateResource(1); // index
	$res2 = self::translateResource(71); // global-cache-frame/live-calendar-small
	$res3 = self::translateResource(72); // global-cache-frame/live-calendar
	self::invalidateResources(array_merge($res1, $res2, $res3), 'CA');

	$langs = It6_Models_ControllerConvert::getLangs();
	foreach ($langs as $iso => $id) {
		It6_GlobalCache::deleteKey(It6_GlobalCache::createLocalizedKey(It6_GlobalCache::KEY_PREFIX_LIVE_CALENDAR, $iso));
		It6_GlobalCache::deleteKey(It6_GlobalCache::createLocalizedKey(It6_GlobalCache::KEY_PREFIX_LIVE_CALENDAR_SMALL, $iso));
	}
//	It6_GlobalCache::invalidate(
//		'/web/cs'
//		. It6_GlobalCache_Starter::SESSION);

//	It6_GlobalCache::deleteKeys(
//		'/^(' . It6_GlobalCache::KEY_LIVE_CALENDAR_PREFIX . '|' . It6_GlobalCache::KEY_LIVE_CALENDAR_SMALL_PREFIX . ')/'
//	);
//	It6_GlobalCache::invalidate('/web/*/global-cache-frame/live-calendar-small__*__');

//	It6_GlobalCache::invalidate('/web/*/global-cache-frame/live-calendar__*__');
}

public static function invalidateCalendars() {
	$res_small = self::translateResource(71); // global-cache-frame/live-calendar-small
	$res = self::translateResource(72); // global-cache-frame/live-calendar
	self::invalidateResources(array_merge($res_small, $res), 'CA');
}

/*
public static function StaticPage_update($pageName) {
	$contActions = Zend_Registry::get('db')->select()
		->from(
			array('cc' => 'controller_convert'),
			array('req_controller')
		)
		->join(
			array('j' => 'jazyky'),
			'j.lang_id = cc.lang_id',
			array('iso')
		)
		->where('cc.real_action = ?', $pageName)
		->query()->fetchAll();


	foreach($contActions as $action) {
		It6_GlobalCache::invalidate(
			'/web/'.$action['iso'].'/'.$action['req_controller']
			. It6_GlobalCache_Starter::NO_SESSION);
	}
}
*/

/**
 * @param string|array $page page_spec | array(page_spec, ...), where page_spec := page_name | "page_name:lang_id_or_iso_or_*"
 */
public static function StaticPage_update($page) {
	$db = Zend_Registry::get('db');
	$pages = (is_array($page) ? $page : array($page));
	$select = $db->select()->from(array('cc' => 'controller_convert'), array(
			'realAction' => 'real_action',
			'reqController' => 'req_controller',
			'reqAction' => 'req_action',
			'actionless'
		))
		->join(array('l' => 'jazyky'), 'l.lang_id=cc.lang_id', array('iso'));
	$allCcs = array(); // realAction -> iso -> data
	foreach ($pages as $_page) {
		$select->reset(Zend_Db_Select::WHERE);
		$_page = explode(':', $_page);
		if (empty($_page[1]) || '*' == $_page[1]) {
			$name = $_page[0];
			$langId = null;
		}
		else {
			$name = $_page[0];
			$langId = $_page[1];
		}
		$select->where('cc.real_controller=?', It6_Models_ControllerConvert::CONTROLLER_STATIC_PAGES)
			->where('cc.real_action=?', $name);
		if (!empty($langId)) {
			if (ctype_digit($langId))
				$select->where('cc.lang_id=?', $langId);
			else
				$select->where('l.iso=?', $langId);
		}
		$ccs = $select->query()->fetchAll();
		if (!empty($ccs)) {
			foreach ($ccs as $cc)
				$allCcs[$cc['realAction']][$cc['iso']] = $cc;
		}
	}
	$res = array();
	foreach ($allCcs as $realAction => $langs) {
		foreach ($langs as $iso => $cc) {
			$res[] = self::makeResource($iso, $cc);
		}
	}
	unset($allCcs);
	self::invalidateResources($res, 'CA');
}

/**
 * @param integer|NULL $branchId Current implementation does not use branch ID, use it for future compatibility where suitable
 */
public static function Branch_update($branchId = null) {
	$res = self::translateResource(23); // pobocky/index
	self::invalidateResources($res, 'CA');
}

/**
 * Invalidates AJAX box(es) for particular ticket game(s) or all ticket game boxes
 * @param integer|array|NULL $gameId One or more ticket game IDs, NULL for all game boxes
 */
public static function Campaign_ticketGameBox($gameId = null) {
	if (isset($gameId)) {
		if (!is_array($gameId)) {
			$gameId = array($gameId);
		}
		$res = array();
		foreach ($gameId as $id) {
			$res = array_merge(
				$res,
				self::translateResource(94, null, array('g' => $id)) // ajax/ticket-game/$gid
			);
		}
		It6_GlobalCache_Invalidator::invalidateResources($res, 'CAP1');
	}
	else {
		$res = self::translateResource(94, null); // ajax/ticket-game/g/*
		It6_GlobalCache_Invalidator::invalidateResources($res, 'CA');
	}
}

/**
 * Invalidates top ticket resources from particular ticket game(s)
 * @param integer|array|NULL $gameId One or more ticket game IDs, NULL for all games and all their tickets
 * @param integer|array|NULL $ticketId One or more ticket IDs, NULL for all ticket in given game(s).
 *                                     Is ignored if $gameId is NULL.
 */
public static function Campaign_ticketGameTopTicket($gameId = null, $ticketId = null) {
	$res = self::translateResource(95, null);
	if (!isset($gameId)) {
		// soutezni-tikety/g/*/t/*
		It6_GlobalCache_Invalidator::invalidateResources($res, 'CA');
	}
	else {
		if (!is_array($gameId)) {
			$gameId = array($gameId);
		}
		if (!isset($ticketId)) {
			$res = array();
			foreach ($gameId as $gid) {
				$res = array_merge(
					$res,
					// soutezni-tikety/g/$gid/t/*
					self::translateResource(95, null, array('g' => $gid))
				);
			}
			It6_GlobalCache_Invalidator::invalidateResources($res, 'CAP1');
		}
		else {
			if (!is_array($ticketId)) {
				$ticketId = array($ticketId);
			}
			foreach ($gameId as $gid) {
				foreach ($ticketId as $tid) { 
					$res = array_merge(
						$res,
						//soutezni-tikety/g/$gid/t/$tid
						self::translateResource(95, null, array('g' => $gid, 't' => $tid))
					);
				}
			}
			It6_GlobalCache_Invalidator::invalidateResources($res, 'CAP12');
		}
	}
}

/**
 * Invalidates resources of ticket game(s) according to changed tickets.
 * @param array $gameTickets Map (game ID => list of ticket IDs or NULL for all)
 * @param array $gameHandlers Prefetched map (game ID => handler class name), all game IDs
 *                            from $gameTickets must be prefetched.
 */
public static function Campaign_ticketGame($gameTickets, $gameHandlers = null) {
	if (empty($gameTickets)) {
		return; // nothing to do
	}
	if (!isset($gameHandlers)) {
		$gameHandlers = array();
		$db = Zend_Registry::get('db');
		$rows = $db->select()
			->from('ticket_game_campaign', array(
				'gameId' => 'id',
				'handlerClass' => 'handler_class',
			))
			->where('id IN (?)', array_keys($gameTickets))
			->query()
			->fetchAll();
		foreach ($rows as $row) {
			$gameHandlers[$row['gameId']] = $row['handlerClass'];
		}
	}
	foreach ($gameTickets as $gameId => $ticketIds) {
		//$gameHandlers[2] = 'It6_Campaign_TicketGame_IPadEuro2012';
		if (empty($gameHandlers[$gameId])) {
			It6_Log::warn(
				'Unknown ticket game handler class, skipping cache invalidation for game.',
				It6_Log::TAG_DEFAULT,
				array('gameId' => $gameId)
			);
			continue;
		}
		call_user_func(array($gameHandlers[$gameId], 'invalidateCache'), $gameId, $ticketIds);
	}
}

	public static function invalidateFooterFrame() {
		$res = self::translateResource(102); // ajax-menu (actionless)
		self::invalidateResources($res, 'C', false);
	//	It6_GlobalCache::invalidate(
	//		'/web/*/global-cache-frame/sport-menu'
	//		. It6_GlobalCache_Starter::NO_SESSION);
	}


} // It6_GlobalCache_Invalidator
