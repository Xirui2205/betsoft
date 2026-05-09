<?php

/**
 * This class uses local cache with whole controller convert table loaded.
 * TODO: consider moving language related methods to It6_Models_Language (would need refactorization to use It6_LocalCache)
 */

require_once ROOT . 'common/library/It6/Models/DbDependent.php';
require_once ROOT . 'common/library/It6/LocalCache.php';
require_once ROOT . 'common/library/Zend/Uri/Http.php';

class It6_Models_ControllerConvert extends It6_Models_DbDependent {

const CONTROLLER_STATIC_PAGES = 'static-page';

const CACHE_TTL = 3600; // 1 hour

protected static $_columns = array(
	'controllerId' => 'c_id',
	'langId' => 'lang_id',
	'parentId' => 'parent_id',
	'order' => 'poradi',
	'visible' => 'zobrazeno',
	'columns' => 'cols',
	'leftSide' => 'left_side',
	'rightSide' => 'right_side',
	'title' => 'title',
	'description' => 'description',
	'keywords' => 'keywords',
	'text' => 'text',
	'reqController' => 'req_controller',
	'reqAction' => 'req_action',
	'realController' => 'real_controller',
	'realAction' => 'real_action',
	'afterLogin' => 'after_login',
	'actionless' => 'actionless',
	'nonassocParams' => 'nonassoc_params',
);

protected static $_cleanUpLock = false; // flag to clean lock

public static function cleanUpLock() {
	if (self::$_cleanUpLock) {
		It6_LocalCache::delete('CC:I:LOCK');
		self::$_cleanUpLock = false;
	}
}

/**
 * Initializes data for these maps in local cache:
 *    CC:D: (controllerId, langId) -> data (per row)
 *    CC:R: (langIso, reqController, reqAction) -> (controllerId, langId) (per row)
 *    CC:A: (langIso, reqController) -> (controllerId, langId)] (per row)
 *    CC:L (langIso) -> langId (complete table)
 * Using local cache key 'CC:I:LOCK' for request synchronization.
 * @param boolean $reinit FALSE means that cache won't be initialized again if it was initialized before, FALSE is default.
 * @param Zend_Db_Adapter $db
 */
public static function initCache($reinit = false, &$db = null) {
	if (It6_LocalCache::get('CC:I') && !$reinit)
		return;
	$start = time();
	$first = true;
	while (true) {
		$lock = It6_LocalCache::add('CC:I:LOCK', 1, 60);
		if (60 < time() - $start) {
			break;
		}
		if ($lock) {
			break;
		}
		$first = false;
		usleep(150000);
	}
	if (!$lock) {
		throw new Exception('Cannot initialize "controller convert" cache, initialization locked by other thread');
	}
	else {
		self::$_cleanUpLock = true;
	}
	if (!$first && !$reinit) {
		self::cleanUpLock();
		return; // cache was initialized by other thread
	}

	register_shutdown_function(array('It6_Models_ControllerConvert', 'cleanUpLock'));
	if ($reinit)
		self::clearCache();
	if (!Zend_Registry::isRegistered('db')) {
		// probably called from cache starter or another script that can bypass MVC stack
		require_once(ROOT . 'common/library/Zend/Controller/Plugin/DbPLugin.php');
		$db = Zend_Controller_Plugin_DbPLugin::connectDbMain();
	}
	else {
		static::assureDbParam($db);
	}
	$rows = $db->select()
		->from(array('cc' => 'controller_convert'), static::$_columns)
		->join(array('l' => 'jazyky'), 'l.lang_id=cc.lang_id', array('langIso' => 'iso'))
		->query()
		->fetchAll();
	$langs = array();
	$actionless = array();
	foreach ($rows as $row) {
		$controllerId = $row['controllerId'];
		$langId = $row['langId'];
		$langIso = $row['langIso'];
		$langs[$langIso] = $langId;
		It6_LocalCache::set("CC:D:$controllerId:$langId", $row);
		It6_LocalCache::set("CC:R:$langIso:{$row['reqController']}:{$row['reqAction']}", array($controllerId, $langId));
		if (!empty($row['actionless'])) {
			$controller = $row['reqController'];
			$action = $row['reqAction'];
			if ('index' == $action || empty($actionless[$langIso][$controller][$action]))
				$actionless[$langIso][$controller][$action] = array($controllerId, $langId);
		}
	}
	foreach ($actionless as $langIso => $_cs) {
		foreach ($_cs as $controller => $_as) {
			foreach ($_as as $action => $ids)
				It6_LocalCache::set("CC:A:$langIso:$controller", $ids);
		}
	}
	It6_LocalCache::set('CC:L', $langs);
	It6_LocalCache::set('CC:I', true, self::CACHE_TTL);
	self::cleanUpLock();
}

public static function clearCache() {
	It6_LocalCache::deleteKeys('CC:');
}

public static function updateCache($data) {
	It6_LocalCache::set("CC:D:{$data['controllerId']}:{$data['langId']}", $data);
	$langIso = self::getLangIsoById($data['langId']);
	It6_LocalCache::set("CC:R:$langIso:{$row['reqController']}:{$row['reqAction']}", $controllerId);
}

/**
 * @param Zend_Db_adapter $db
 * @return array dictionary ISO => ID
 */
public static function getLangs(&$db = null) {
	static $cache = false;
	if (false === $cache) {
		self::initCache(false, $db);
		$cache = It6_LocalCache::get('CC:L');
	}
	return $cache;
}

public static function getLangIdByIso($langIso, &$db = null) {
	$langs = self::getLangs($db);
	if (empty($langs) || empty($langs[$langIso]))
		return false;
	else
		return $langs[$langIso];
}

public static function getLangIsoById($langId, &$db = null) {
	$langs = self::getLangs($db);
	if (empty($langs))
		return false;
	else
		return array_search($langId, $langs);
}

/**
 * Retrieves controller data for all languages
 * @param integer $controllerId
 * @param integer|string $lang Lang ID or ISO code
 * @param boolean $byIso TRUE if language keys should ISO, FALSE for using language IDs
 * @param Zend_Db_Adapter $db
 * @return boolean|array array(lang key => controller data)
 */
public static function getById($controllerId, $byIso = true, &$db = null) {
	$langs = self::getLangs($db);
	$result = array();
	foreach ($langs as $iso => $langId)
		$result[$byIso ? $iso : $langId] = It6_LocalCache::get("CC:D:$controllerId:$langId");
	return $result;
}

/**
 *
 * @param integer $controllerId
 * @param integer|string $lang Lang ID or ISO code (internal conversion ISO -> ID)
 * @param Zend_Db_Adapter $db
 * @return boolean|array
 */
public static function getByIdAndLang($controllerId, $lang, &$db = null) {
	self::initCache(false, $db);
	if (!ctype_digit($lang))
		$lang = self::getLangIdByIso($lang);
	return It6_LocalCache::get("CC:D:$controllerId:$lang");
}

/**
 * Expects normalized form with 'index' values for empty controller/action URL parts.
 * @param string $lang URL part for lang
 * @param string $controller URL part for controller
 * @param string $action URL part for action
 * @param Zend_Db_Adapter $db
 * @return boolean|array
 */
public static function getByRequest($lang, $controller, $action, &$db = null) {
	self::initCache(false, $db);
	$pk = It6_LocalCache::get("CC:R:$lang:$controller:$action");
	if (empty($pk))
		return false;
	else
		return It6_LocalCache::get("CC:D:{$pk[0]}:{$pk[1]}");
}

/**
 * Expects normalized form with 'index' value for empty controller URL part.
 * @param string $lang URL part for lang
 * @param string $controller URL part for controller
 * @param Zend_Db_Adapter $db
 * @return boolean|array
 */
public static function getByRequestActionless($lang, $controller, &$db = null) {
	self::initCache(false, $db);
	$pk = It6_LocalCache::get("CC:A:$lang:$controller");
	if (empty($pk))
		return false;
	else
		return It6_LocalCache::get("CC:D:{$pk[0]}:{$pk[1]}");
}

/**
 * Retrieves parts from given URI and tries to find particular controller convert data in DB.
 * @param string|array|Zend_Uri_Http $url Given URI to examine, in case of array there must be this elements (langIso, controller, action, array $reqParams)
 * @param string $lang Output variable (part of $url), ISO code
 * @param string $controller Output variable (part of $url), request controller ('index' minimized to '')
 * @param string $action Output variable (part of $url), request action ('index' minimized to '')
 * @param array $params Output variable (part of $url), additional parameters
 * @param Zend_Db_Adapter $db
 * @return NULL|array Data from controller convert
 */
public static function getDataFromUrl($url, &$lang = null, &$controller = null, &$action = null, &$params = null, &$db = null) {
	if (!is_array($url)) {
		if (!is_string($url)) {
			if (!($url instanceof Zend_Uri_Http))
				$url = Zend_Uri_Http::fromString($_SERVER['HTTP_REFERER']);
			$path = $url->getPath();
		}
		else {
			$path = $url;
		}
		$pathParts = array();
		if (!empty($path)) {
			$path = trim($path, '/');
			$pathParts = explode('/', $path);
		}
		$params = $pathParts;
	}
	else {
		$pathParts = $url;
		$params = array_slice($pathParts[3], 1);
	}
	$controller = null;
	$action = null;
	$lang = null;
	if (array_key_exists(0, $pathParts))
		$lang = $pathParts[0];
	if (array_key_exists(1, $pathParts))
		$controller = $pathParts[1];
	if (array_key_exists(2, $pathParts))
		$action = $pathParts[2];
	if (empty($lang))
		$lang = DEFAULT_LANG;
	if (empty($controller))
		$controller = 'index';
	if (empty($action))
		$action = 'index';

	$saved = self::getByRequest($lang, $controller, $action, $db);
	if (!empty($saved))
		$paramStart = 3;
	else {
		$saved = self::getByRequestActionless($lang, $controller, $db);
		if (empty($saved))
			return null;
		$controller = $saved['reqController'];
		$action = $saved['reqAction'];
		$paramStart = 2;
	}
	if ('index' == $action) {
		$action = '';
		if ('index' == $controller)
			$controller = '';
	}
	$params = array_slice($params, $paramStart);
	$n = count($params);
	if (empty($saved['nonassocParams'])) {
		$_params = array();
		for ($i = 0; $i < $n; $i += 2) {
			$_params[$params[$i]] = (array_key_exists($i + 1, $params) ? $params[$i + 1] : null);
		}
		$params = $_params;
	}
	return $saved;
}

/**
 * Creates URI from components, "index" component are skipped where possible.
 * @param string $lang
 * @param string $controller
 * @param string $action
 * @param boolean $pathOnly If build full URI (including protocol etc.) or only path part, FALSE is default
 * @param array $params An array of params to be added to the und of url. The params mustnt be urlencoded.
 * @return string
 */
public static function buildUrl($lang, $controller, $action, $pathOnly = false, $params=null) {
 	if (empty($controller))
		$controller = 'index';
	if (empty($action))
		$action = 'index';
	$url = ($pathOnly ? '' : PROTOCOL . WEBHOST) . '/' . $lang . '/';
	if ('index' != $action)
		$url .= $controller . '/' . $action . '/';
	else if ('index' != $controller)
		$url .= $controller . '/';
	
	if(!empty($params)) {
		$params = self::arrToGetString($params);
		$url .= '?' . $params;
	}
	
	return $url;
}

/**
 * Creates a get parameter string that can be attached to url.
 * @param array $params The arra that is to be converted to the get query string
 * @return string The resulting string 
 */
public static function arrToGetString(array $params) {
	$paramsArr = array();
	foreach($params as $pName => $pValue) {
		$paramsArr[] = urlencode($pName).'='.urlencode($pValue);
	}
	
	return implode('&', $paramsArr);
}


public static function getGoogleAdParams() {
	$gAdParams = array();
	if(isset($_GET['utm_source'])) {
		$gAdParams['utm_source'] = $_GET['utm_source'];
	}
	if(isset($_GET['utm_medium'])) {
		$gAdParams['utm_medium'] = $_GET['utm_medium'];
	}
	if(isset($_GET['utm_term'])) {
		$gAdParams['utm_term'] = $_GET['utm_term'];
	}
	if(isset($_GET['utm_content'])) {
		$gAdParams['utm_content'] = $_GET['utm_content'];
	}
	if(isset($_GET['utm_campaign'])) {
		$gAdParams['utm_campaign'] = $_GET['utm_campaign'];
	}
	
	return $gAdParams;
}
} // class It6_Models_ControllerConvert