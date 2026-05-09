<?php

class It6_Betradar_Import {

/**
 * Only texts will this language code will be used. See betradar documentation for code list.
 */
const DEFAULT_LANGUAGE = 'cs';

const BET_TYPE_MATCH = 1;
const BET_TYPE_OUTRIGHT = 2;

const CANCELED_IN_OFFER = 'offer';
const CANCELED_BY_RESULT = 'result';
const CANCELED_IN_OFFER_SAVED = 'offer-saved';
const CANCELED_BY_RESULT_SAVED = 'result-saved';



/**
 *  if cancelation type have its SAVED counterpart,
 *  it must be registered here
 */
private static $canceledTypeSavedMap = array(
	self::CANCELED_IN_OFFER => self::CANCELED_IN_OFFER_SAVED,
	self::CANCELED_BY_RESULT => self::CANCELED_BY_RESULT_SAVED,
);

private static $charDataElements = array(
	'VALUE', 'MATCHDATE', 'EVENTDATE', 'ODDS', 'OFF', 'TOURNAMENTID', 'CONFIRMEDMATCHSTART', 'SCORE'
);

private static $betColumnMap = array(
	'bewaId' => 'sazka_id',
	'brBetType' => 'betradar_bet_type',
	'brId' => 'betradar_match_id',
	'brOddsType' => 'betradar_odds_type',
	'brSpecialValue' => 'betradar_special_value',
	'brCompetitorId' => 'betradar_competitor_id',
	'brParams' => 'betradar_params',
	'typeId' => 'typ_id',
	'realTypeId' => 'real_typ_id',
	'subtypeId' => 'podtyp_id',
	'eventId' => 'udalost_id',
	'betName' => 'text',
	'validFrom' => 'platna_od',
	'validTo' => 'platna_do',
	'brUpdate' => 'betradar_autoupdate',
	'parentId' => 'parent_id',
);

private static $brBetTypeDbMap = array(
	self::BET_TYPE_MATCH => 'match',
	self::BET_TYPE_OUTRIGHT => 'outright',
);

/**
* BR score (eg. FT) -> DB column in bet results table (eg. 'ft')
* @var array
*/
private static $brScoreToDbColumnMap = array(
	It6_Betradar_Match::SCORETYPE_FT => 'ft',
	It6_Betradar_Match::SCORETYPE_HT => 'ht',
	It6_Betradar_Match::SCORETYPE_OT => 'ot',
	It6_Betradar_Match::SCORETYPE_AP => 'ap',
	It6_Betradar_Match::SCORETYPE_WO => 'wo',
	It6_Betradar_Match::SCORETYPE_C => 'c',
	It6_Betradar_Match::SCORETYPE_1P => '1t',
	It6_Betradar_Match::SCORETYPE_2P => '2t',
	It6_Betradar_Match::SCORETYPE_3P => '3t',
	It6_Betradar_Match::SCORETYPE_1Q => '1q',
	It6_Betradar_Match::SCORETYPE_2Q => '2q',
	It6_Betradar_Match::SCORETYPE_3Q => '3q',
	It6_Betradar_Match::SCORETYPE_4Q => '4q',
	It6_Betradar_Match::SCORETYPE_SET1 => 'set1',
	It6_Betradar_Match::SCORETYPE_SET2 => 'set2',
	It6_Betradar_Match::SCORETYPE_SET3 => 'set3',
	It6_Betradar_Match::SCORETYPE_SET4 => 'set4',
	It6_Betradar_Match::SCORETYPE_SET5 => 'set5',
);

/**
 * Database adapter for main database
 * @var Zend_Db_Adapter
 */
private $dbMain = null;

/**
 * Database adapter for admin database
 * @var Zend_Db_Adapter
 */
private $dbAdmin = null;

/**
 * UNIX timestamp of start of this import
 * @var integer
 */
private $startedAt = null;

/**
* UNIX timestamp this import was finished
* @var integer
*/
private $finishedAt = null;


/**
 * Betradar language code that should be used for texts
 * @var string
 */
private $language = self::DEFAULT_LANGUAGE;

/**
 * Admin (bookmaker) ID for operations in BBAS
 * @var integer
 */
private $bookmakerId = It6_Models_Admin::ID_INTERNET;

/**
 * If betradar update is enabled for newly created bets
 * @var boolean
 */
private $newBetsHaveBrUpdateOn = true;

/**
 * @var boolean
 */
private $canImportMatches = true;

/**
 * @var boolean
 */
private $canImportOutrights = false;

/**
 * Comma separated list of email recipients 
 * @var string
 */
private $bookmakerEmails = '';

/**
 * Send report on errors
 * @var boolean
 */
private $sendReportOnError = true;

/**
 * Send report whatever did or did not happen (who designed these overlaying options? ... $sendReportOnError)
 * @var boolean
 */
private $sendReportAlways = true;

/**
 * Stack pointer (offset of top element)
 * @var integer
 */
private $parseStackPtr = -1;

/**
 * Stack with node wrappers for current parsed path in XML document
 * @var array
 */
private $parseStack = array();

/**
 * Some shorthand flags for indicating some states during parsing. NAME => VALUE map.
 * @var array
 */
private $parseFlags = array();

/**
 * We will process one sport at the time, it will be our root for data structure.
 * @var It6_Betradar_Sport
 */
private $sport = null;

/**
 * Preloaded data handler
 * @var It6_Betradar_PreloadedData
 */
private $preloadedData  = null;

/**
 * List of error messages to be reported
 * @var array
 */
private $reportedErrors = array();

/**
 * List of betradar ID of teams that we created automatically
 * @var array
 */
private $createdTeams = array();

/**
 * Array (subtypId => name) of new added subtypes
 * @var array
 */
private $newSubtypes = array();

/**
 * Array (brId => name) of unknown sports (could not be imported)
 * @var array
 */
private $unknownSports = array();

/**
 * Array (brId => name) of unknown tournaments (could not be imported)
 * @var array
 */
private $unknownTournaments = array();

/**
 * Array (brId => name) of unknown teams (bets with this teams could not be imported)
 * @var array
 */
private $unknownTeams = array();

/**
 * List of odds types
 * @var array
 */
private $unknownOddsTypes = array();

/**
 * List of encounetered BBAS bet type IDs that are not supported
 * @var array
 */
private $unknownResultBetTypes = array();


/**
 * Lists of BBAS bet IDs that were canceled in BR's offer.
 * Each list is stored under key CANCELED_* class constant.
 * @var array
 */
private $betsCanceledAtBr = array();

public function __construct($dbMain = null, $dbAdmin = null) {
	$this->dbMain = (isset($dbMain) ? $dbMain : Zend_Registry::get('db'));
	$this->dbAdmin = (isset($dbAdmin) ? $dbAdmin : Zend_Registry::get('admindb'));

	// query which language should be used for texts
	$paramNames = array(
		It6_Models_Parameter::NAME_BETRADAR_QUERIED_LANGUAGE,
		It6_Models_Parameter::NAME_BETRADAR_BET_BOOKMAKER_ID,
		It6_Models_Parameter::NAME_BETRADAR_AUTOUPDATE_DEFAULT,
		It6_Models_Parameter::NAME_BETRADAR_EMAIL_RECIPIENTS,
		It6_Models_Parameter::NAME_BETRADAR_SEND_EMAIL_ON_ERROR,
		It6_Models_Parameter::NAME_BETRADAR_SEND_EMAIL_ALWAYS,
	);
	$params = It6_Models_Parameter::getDataByName($paramNames, $this->dbAdmin);
	if (!empty($params[It6_Models_Parameter::NAME_BETRADAR_QUERIED_LANGUAGE]['value']))
		$this->language = $params[It6_Models_Parameter::NAME_BETRADAR_QUERIED_LANGUAGE]['value'];
	
	if (!empty($params[It6_Models_Parameter::NAME_BETRADAR_BET_BOOKMAKER_ID]['value']))
		$this->bookmakerId = $params[It6_Models_Parameter::NAME_BETRADAR_BET_BOOKMAKER_ID]['value'];

	if (isset($params[It6_Models_Parameter::NAME_BETRADAR_AUTOUPDATE_DEFAULT]['value']))
		$this->newBetsHaveBrUpdateOn = $params[It6_Models_Parameter::NAME_BETRADAR_AUTOUPDATE_DEFAULT]['value'];

	if (!empty($params[It6_Models_Parameter::NAME_BETRADAR_EMAIL_RECIPIENTS]['value']))
		$this->bookmakerEmails = $params[It6_Models_Parameter::NAME_BETRADAR_EMAIL_RECIPIENTS]['value'];

	if (isset($params[It6_Models_Parameter::NAME_BETRADAR_SEND_EMAIL_ON_ERROR]['value']))
		$this->sendReportOnError = $params[It6_Models_Parameter::NAME_BETRADAR_SEND_EMAIL_ON_ERROR]['value'];
	
	if (isset($params[It6_Models_Parameter::NAME_BETRADAR_SEND_EMAIL_ALWAYS]['value']))
		$this->sendReportAlways = $params[It6_Models_Parameter::NAME_BETRADAR_SEND_EMAIL_ALWAYS]['value'];

}

public function getDbMain() {
	return $this->dbMain;
}

public function getDbAdmin() {
	return $this->dbAdmin;
}

/**
 * @param boolean $asTimestamp TRUE for UNIX timestamp (default), FALSE for datetime in DB format
 * @return integer|string
 */
public function getStartedAt($asTimestamp = true) {
	static $timestamp = null;
	static $formated = null;
	if ($asTimestamp)
		return $this->startedAt;
	else {
		if ($timestamp != $this->startedAt) {
			$timestamp = $this->startedAt;
			$formated = It6_Date::timestampToDb($timestamp);
		}
		return $formated;
	}
}

public function haveNewBetBrUpdateOn() {
	return $this->newBetsHaveBrUpdateOn;
}

public function canImportMatches($can = null) {
	if (isset($can))
		$this->canImportMatches = $can;
	return $this->canImportMatches;
}

public function canImportOutrights($can = null) {
	if (isset($can))
		$this->canImportOutrights = $can;
	return $this->canImportOutrights;
}

/**
 * Process whole XML file pushed from Betradar.
 * @param string $file File (path) to be read
 * @return boolean FALSE on error
 */
public function importFile($file) {
	if (!($f = fopen($file, 'r')))
		return false;

	$this->startedAt = time();
	It6_Log::info("Start of file import: $file", It6_Log::TAG_BETRADAR_OPERATION);
	It6_Models_BetradarImportLog::init(basename($file));

	$parser = xml_parser_create('UTF-8');
	xml_set_element_handler($parser, array($this, 'parseElementStart'), array($this, 'parseElementEnd'));
	xml_set_character_data_handler($parser, array($this, 'parseCharacterData'));

	$result = true;
	$start = true;
	while ($data = fread($f, 4096)) {
		if ($start) { // XML files from betradar are in fact saved raw POST data, so can start with something like 'null=<?xml ...'
			$pos = strpos($data, '<');
			if ($pos)
				$data = substr($data, $pos);
			$start = false;
		}
		if (!xml_parse($parser, $data, feof($f))) {
			$errMsg = sprintf("XML error: %s at line %d in file '%s'",
				xml_error_string(xml_get_error_code($parser)),
				xml_get_current_line_number($parser),
				$file
			);
			It6_Log::err($errMsg, It6_Log::TAG_BETRADAR_OPERATION);
			$this->reportedErrors[] = $errMsg;
			$result = false;
			break;
		}
	}

	xml_parser_free($parser);
	fclose($f);
	It6_Models_BetradarImportLog::save(false, $this->dbAdmin);

	$this->finishedAt = time();
	It6_Log::info("File import finished: $file", It6_Log::TAG_BETRADAR_OPERATION);
	
	$this->report($file);

	return $result;
}

/**
 * Traverses parser stack from top to bottom, finds first instance of given class
 * and calls his method passing given parameters.
 * @param string $parentClass Class name of parent to be matched
 * @param string $method Name of the method to be called
 * @param array $parameters Parameters to be passed to the method
 */
private function callMethodOfParentAtStack($parentClass, $method, array $parameters = array()) {
	for ($sp = $this->parseStackPtr; 0 <= $sp; --$sp) {
		if (empty($this->parseStack[$sp]['object']))
			continue;
		$parent = &$this->parseStack[$sp]['object'];
		if ($parentClass == get_class($parent))
			return call_user_func_array(array(&$parent, $method), $parameters);
	}
	return false;
}

/**
 * Parses string as floating point number
 * @param string $str
 * @return float
 */
public static function parseFloat($str) {
	return floatval(strtr($str, ',', '.'));
}

/**
 * Parses datetime in format YYYY-M-D*HH:MM:SS in local time
 * and returns timestamp or datetime data.
 * @param string $str
 * @param boolean $asTimestamp If TRUE then UNIX timestamp is returned, associative array with datetime data otherwise
 * @return integer|array UNIX timestamp or array with at least keys: hour, minute, second, month, day, year
 */
private static function parseDatetime($str, $asTimestamp = true) {
	$tm = date_parse_from_format('Y-n-j?G:i:s', $str);
	return (
		$asTimestamp
		? mktime($tm['hour'], $tm['minute'], $tm['second'], $tm['month'], $tm['day'], $tm['year'])
		: $tm
	);
}

/**
 * Callback for XML parser for element start.
 * @param resource $parser
 * @param string $name
 * @param array $attrs
 */
public function parseElementStart($parser, $name, $attrs) {
	$element = array(
		'element' => $name,
		'attrs' => $attrs,
		'charData' => in_array($name, static::$charDataElements),
	);
	++$this->parseStackPtr;
	$object = null;
	switch ($name) {
	case 'SPORT':
		$object = new It6_Betradar_Sport($attrs['BETRADARSPORTID']);
		$this->sport = &$object;
		break;
	case 'CATEGORY':
		$object = new It6_Betradar_Category($attrs['BETRADARCATEGORYID']);
		$this->callMethodOfParentAtStack('It6_Betradar_Sport', 'addCategory', array(&$object));
		break;
	case 'OUTRIGHT':
		$object = new It6_Betradar_Match($attrs['BETRADAROUTRIGHTID'], true);
		$this->callMethodOfParentAtStack('It6_Betradar_Category', 'addOutright', array(&$object));
		break;
	case 'TOURNAMENT':
		$object = new It6_Betradar_Tournament($attrs['BETRADARTOURNAMENTID']);
		$this->callMethodOfParentAtStack('It6_Betradar_Category', 'addTournament', array(&$object));
		break;
	case 'MATCH':
		$object = new It6_Betradar_Match($attrs['BETRADARMATCHID'], false);
		$this->callMethodOfParentAtStack('It6_Betradar_Tournament', 'addMatch', array(&$object));
		break;
	case 'BET':
		$parent = $this->parseStack[$this->parseStackPtr - 1];
		if ('MATCHODDS' == $parent['element'])
			$this->parseFlags['ODDSTYPE'] = $attrs['ODDSTYPE'];
		break;
	case 'OUTRIGHTODDS':
		$this->parseFlags['ODDSTYPE'] = $attrs['ODDSTYPE'];
		break;
	case 'RESULT':
		if ('OUTRIGHTRESULTS' == $this->parseStack[$this->parseStackPtr - 1]['element'])
			$this->parseFlags['OUTRIGHTRESULTS'] = $attrs['ID'];
		break;
	case 'W':
		$parent = $this->parseStack[$this->parseStackPtr - 1]['element'];
		if ('BETRESULT' == $parent) {
			$oddsType = $attrs['ODDSTYPE'];
			$specVal = (isset($attrs['SPECIALBETVALUE']) ? $attrs['SPECIALBETVALUE'] : null);
			$outcome = $attrs['OUTCOME'];
			$this->callMethodOfParentAtStack('It6_Betradar_Match', 'addBetResult', array($oddsType, $specVal, $outcome));
		}
		break;
	case 'VALUE':
		$useCharData = true;
		$parent = $this->parseStack[$this->parseStackPtr - 1];
		if ('TEXT' == $parent['element']) {
			if (!isset($parent['attrs']['LANGUAGE']) || $this->language != $parent['attrs']['LANGUAGE']) {
				$useCharData = false;
			}
		}
		if ($useCharData) {
			if (false === $element['charData'])
				$element['charData'] = true;
		}
		else
			$element['charData'] = false;
		break;
	default:
	}
	$element['object'] = &$object;
	$this->parseStack[] = &$element;
}

/**
 * Callback for XML parser for character data.
 * @param resource $parser
 * @param string $data
 */
public function parseCharacterData($parser, $data) {
	$elem = &$this->parseStack[$this->parseStackPtr];
	if ( in_array($elem['element'], static::$charDataElements) ) {
		if (false !== $elem['charData']) {
			if (true === $elem['charData'])
				$elem['charData'] = $data;
			else
				$elem['charData'] .= $data;
		}
	}
}

/**
 * Callback for XML parser for element end.
 * @param resource $parser
 * @param string $name
 */
public function parseElementEnd($parser, $name) {
	$elem = array_pop($this->parseStack);
	--$this->parseStackPtr;
	switch ($elem['element']) {
	case 'SPORT':
		$this->freeSport();
		break;
	case 'CATEGORY':
		$this->freeCategory(true);
		break;
	case 'OUTRIGHT':
		$this->importCategory(true);
		break;
	case 'TOURNAMENT':
		$this->importCategory(true);
		break;
	case 'MATCH':
		break;
	case 'MATCHDATE':
	case 'EVENTDATE':
		$timestamp = $this->parseDatetime($elem['charData']);
		$this->callMethodOfParentAtStack('It6_Betradar_Match', 'setStart', array($timestamp));
		break;
	case 'CONFIRMEDMATCHSTART':
		$timestamp = $this->parseDatetime($elem['charData']);
		$this->callMethodOfParentAtStack('It6_Betradar_Match', 'setConfirmedStart', array($timestamp));
		break;
	case 'OFF':
		$parent = $this->parseStack[$this->parseStackPtr]['element'];
		if ('STATUSINFO' ==  $parent || 'EVENTINFO' == $parent)
			$this->callMethodOfParentAtStack('It6_Betradar_Match', 'setCalledOff', array($elem['charData'] ? true : false));
		break;
	case 'TOURNAMENTID':
		if ('EVENTINFO' == $this->parseStack[$this->parseStackPtr]['element'])
			$this->callMethodOfParentAtStack('It6_Betradar_Match', 'setTournamentId', array($elem['charData']));
		break;
	case 'BET':
		if ('MATCHODDS' == $this->parseStack[$this->parseStackPtr]['element'])
			unset($this->parsetFlag['ODDSTYPE']);
		break;
	case 'OUTRIGHTODDS':
		unset($this->parsetFlag['ODDSTYPE']);
		break;
	case 'ODDS':
		if (isset($this->parseFlags['ODDSTYPE'])) {
			$parent = $this->parseStack[$this->parseStackPtr]['element'];
			$outcome = $elem['attrs'][ 'OUTRIGHTODDS' == $parent ? 'ID' : 'OUTCOME' ]; // another attribute for outright and match
			$rate = $this->parseFloat($elem['charData']);
			$specialValue = (isset($elem['attrs']['SPECIALBETVALUE']) ? $elem['attrs']['SPECIALBETVALUE'] : null);
			$this->callMethodOfParentAtStack('It6_Betradar_Match', 'addOdds', array(
				$this->parseFlags['ODDSTYPE'], $outcome, $rate, $specialValue
			));
		}
		break;
	case 'SCORE':
		if ('SCOREINFO' == $this->parseStack[$this->parseStackPtr]['element']) {
			$this->callMethodOfParentAtStack('It6_Betradar_Match', 'addScore', array(
				$elem['attrs']['TYPE'], $elem['charData']
			));
		}
		break;
	case 'RESULT':
		if ('OUTRIGHTRESULTS' == $this->parseStack[$this->parseStackPtr]['element']) {
			if (!isset($this->parseFlags['OUTRIGHTRESULTADDED'])) {
				// for special results without VALUE child element
				$this->callMethodOfParentAtStack('It6_Betradar_Match', 'addScore', array(
					$this->parseFlags['OUTRIGHTRESULTS'], ''
				));
			}
			else
				unset($this->parseFlags['OUTRIGHTRESULTADDED']);
			unset($this->parseFlags['OUTRIGHTRESULTS']);
		}
		break;
	case 'VALUE':
		
		if (false !== $elem['charData']) {
			$parent = $this->parseStack[$this->parseStackPtr]['element'];
			if ('TEXT' == $parent) {
				$elem['textAttrs'] = array();
				for ($sp = $this->parseStackPtr; 0 <= $sp; --$sp) {
					if ('TEXT' == $this->parseStack[$sp]['element'])
						// collect all TEXT element attributes going down the stack
						$elem['textAttrs'] = array_merge($this->parseStack[$sp]['attrs'], $elem['textAttrs']);
					else if ('COMPETITORS' == $this->parseStack[$sp]['element'])
						// also note if text is part of competitors data
						$elem['competitors'] = true;
					else if ('EVENTNAME' == $this->parseStack[$sp]['element'])
						// also note if text is part of competitors data
						$elem['eventName'] = true;
					$object = $this->parseStack[$sp]['object'];
					if ($object instanceof It6_Betradar_Node) {
						// first object takes text
						$object->addText($elem);
						break;
					}
				}
			}
			else if ('NEUTRALGROUND' == $parent) {
				$this->callMethodOfParentAtStack('It6_Betradar_Match', 'setNeutralGround', array($elem['charData'] ? true : false));
			}
			else if ('RESULT' == $parent) {
				if (isset($this->parseFlags['OUTRIGHTRESULTS'])) {
					$this->callMethodOfParentAtStack('It6_Betradar_Match', 'addScore', array(
						$this->parseFlags['OUTRIGHTRESULTS'], $elem['charData']
					));
					$this->parseFlags['OUTRIGHTRESULTADDED'] = true;
				}
			}
		}
		break;
	default:
		break;
	}

	//echo str_repeat(' ', $this->parseStackPtr) . "</$name> ... {$elem['element']}\n";
}

/**
 * Deletes current sport node wrapper.
 */
private function freeSport() {
	$this->sport = null;
}

/**
 * Deletes all categories' data in current sport node wrapper.
 */
private function freeCategory() {
	$this->sport->removeCategory();
}

private function preloadData() {
	if (!$this->preloadedData || $this->preloadedData->sportId != $this->sport->brId)
		$this->preloadedData = new It6_Betradar_PreloadedData($this->sport->brId);
	$this->sport->getDataToPreload($this->preloadedData);
	if (!$this->preloadedData->load($this))
		return false;
	return true;
}

public function getPreloadedData() {
	return $this->preloadedData;
}

/**
 * @param It6_Betradar_Sport $sport
 */
public function addUnknownSport($sport) {
	//$this->unknownSports[$sport->brId] = $sport->text;
}

/**
 * @param It6_Betradar_Tournament $tournament
 * @param string $region Region name
 * @param string $sport Sport name
 */
public function addUnknownTournament($tournament, $region = null, $sport = null) {
	$texts = array();
	if (!empty($sport)) {
		$texts[] = $sport;
	}
	if (!empty($region)) {
		$texts[] = $region;
	}
	$texts[] = $tournament->text;
	//$this->unknownTournaments[$tournament->brId] = implode(' / ', $texts);
}

public function addUnknownResultBetType($typeId) {
	//if (!in_array($typeId, $this->unknownResultBetTypes)) $this->unknownResultBetTypes[] = $typeId;
}

/**
 * @param integer $brId
 * @param string $name
 */
public function addUnknownTeam($brId, $name) {
	//$this->unknownTeams[$brId] = $name;
}

/**
 * @param string $oddsType
 */
public function addUnknownOddsType($oddsType) {
	//if (!in_array($oddsType, $this->unknownOddsTypes)) $this->unknownOddsTypes[] = $oddsType;
}

/**
 * Register cancelation of bet in BR's offer
 * @param integer|array $betId One or list of BBAS bet IDs, that were canceled in BR's offer
 * @param string $cancelationType Use CANCELED_* class constants
 */
public function addBetCanceledAtBetradar($betId, $cancelationType) {
	if (is_array($betId)) {
		if (!isset($this->betsCanceledAtBr[$cancelationType])) {
			$this->betsCanceledAtBr[$cancelationType] = array();
		}
		$this->betsCanceledAtBr[$cancelationType] = array_merge(
			$this->betsCanceledAtBr[$cancelationType], $betId
		);
	}
	else {
		$this->betsCanceledAtBr[$cancelationType][] = $betId;
	}
}

/**
 * Import currently parsed category tournaments and outrights.
 * @param boolean $freeData TRUE if data of imported bets should be freed
 */
private function importCategory($freeData) {
	$preloaded = false;
	foreach ($this->sport->categories as $category) {
		if ($category->hasAnythingToImport($this)) {
			if (!$preloaded)
				$this->preloadData();
				
			$category->import($this, $this->sport);
		}
		if ($freeData)
			$category->freeData();
	}
	if ($freeData) {
		if (isset($this->preloadedData))
			$this->preloadedData->freeCategoryData();
	}
}

public static function createSqlTupples($tupples, $dbAdapter = null, $level = 1) {
	if (1 < $level) {
		$_tupples = array();
		foreach ($tupples as $tupple)
			$_tupples[] = static::createSqlTupples($tupple, $dbAdapter, $level - 1);
		$tupples = $_tupples;
		unset($_tupples);
	}
	if (1 == $level && isset($dbAdapter))
		array_walk($tupples, function(&$x) use (&$dbAdapter) { $x = $dbAdapter->quote($x); });
	return '(' . implode(',', $tupples) . ')';
}
/**
 * Creates SQL WHERE constraint for tupples of columns using expression
 * ((c1=v1_1 AND c2=v2_1 AND c3=v3_1 AND ...) OR (c1=v1_2 AND c2=v2_2 AND c3=v3_2 AND ...) OR ...)
 * @param array $columns Mixed keys, numeric key = don't escape and value is column name,
 *                       non-numeric key = key is column name and boolean value
 *                       controls escaping (TRUE=escape)
 * @param array $tupples List of tupples (tupple must match order of column)
 * @param Zend_Db_Adapter $dbAdapter If not set, no escaping is performed at all
 * @return string SQL for tupples
 */
public static function createSqlTupplesLong($columns, $tupples, $dbAdapter = null) {
	$names = array();
	$escaping = array();
	foreach ($columns as $key => $value) {
		if (is_numeric($key)) {
			$escape = false;
			$name = $value;
		}
		else {
			$escape = $value;
			$name = $key;
		}
		$names[] = (isset($dbAdapter) ? $dbAdapter->quoteIdentifier($name) : $name);
		$escaping[] = $escape;
	}
	$ors = array();
	foreach ($tupples as $tupple) {
		$ands = array();
		foreach ($names as $i => $name) {
			$value = (isset($dbAdapter) && $escaping[$i] ? $dbAdapter->quote($tupple[$i]) : $tupple[$i]);
			$ands[] = "($name=$value)"; 
		}
		$ors[] = '(' . implode(' AND ', $ands) . ')';
	}
	return '(' . implode(' OR ', $ors) . ')';
}

private static function brBetTypeToDb($brBetType) {
	return static::$brBetTypeDbMap[$brBetType];
}

private static function brBetTypeFromDb($dbBrBetType) {
	return array_search($dbBrBetType, static::$brBetTypeDbMap);
}

/**
 * Get BBAS sport data (cached in array)
 * @param array $sports array of BR sport IDs
 * @return array|boolean FALSE on error or if some sport not found (it is logged), otherwise array (with BR IDs as keys) of structures with sport data (fields: bewaId, name, brId, betAliasFrom, betAliasTo)
 */
public function getSports($sports) {
	static $cache = array();
	$result = array();
	$missing = array();
	foreach ($sports as $sport) {
		$brId = $sport; 
		if (isset($cache[$brId]))
			$result[$brId] = $cache[$brId];
		else
			$missing[$brId] = $brId;
	}
	if (!empty($missing)) {
		try {
			$rows = $this->dbMain->select()
				->from('sport', array(
					'bewaId' => 'sport_id',
					'name' => 'nazev',
					'brId' => 'betradar_sport_id',
					'betAliasFrom' => 'bet_alias_from',
					'betAliasTo' => 'bet_alias_To',
				))
				->where('betradar_sport_id IN (?)', $missing)
				->query()
				->fetchAll();
		}
		catch (Exception $e) {
			It6_Log::err($e->getMessage(), It6_Log::TAG_BETRADAR_TEAM_OPERATION, null, $e);
			$result = $rows = false;
		}
		if (false !== $rows) {
			foreach ($rows as $row) {
				$brId = $row['brId'];
				$cache[$brId] = $row;
				$result[$brId] = $row;
				unset($missing[$brId]);
			}
			if (!empty($missing)) {
				$ids = implode(',', $missing);
				It6_Log::err("Unknown sport(s). betradarSportIds=[$ids]", It6_Log::TAG_BETRADAR_OPERATION);
				return false;
			}
		}
	}
	return $result;
}

/**
 * Get BBAS event (in BR called tournament) data (cached in array)
 * //TODO: create new events if not found?
 * @param array $tournaments array of BR tournament IDs
 * @return array|boolean FALSE on error or if some tournament not found (it is logged), otherwise array (with BR IDs as keys) of structures with tournament data (fields: bewaId, name, brId, bewaSportId)
 */
public function getTournaments($tournaments) {
	static $cache = array();
	$result = array();
	$missing = array();
	foreach ($tournaments as $tournament) {
		$brId = $tournament;
		if (empty($brId))
			continue;
		if (isset($cache[$brId]))
			$result[$brId] = $cache[$brId];
		else
			$missing[$brId] = $brId;
	}
	if (!empty($missing)) {
		try {
			$rows = $this->dbMain->select()
				->from('udalost_betradar', array('eventId' => 'udalost_id', 'brId' => 'betradar_udalost_id'))
				->where('betradar_udalost_id IN (?)', $missing)
				->query()
				->fetchAll();
			$events = array();
			foreach ($rows as $row)
				$events[$row['brId']] = $row['eventId'];
			$select = $this->dbMain->select()
				->from('udalost', array(
					'bewaId' => 'udalost_id',
					'name' => 'nazev',
					'brId' => 'betradar_udalost_id',
					'bewaSportId' => 'sport_id',
					'brTimeOffset' => 'betradar_time_offset',
				))
				->where('betradar_udalost_id IN (?)', $missing);
			if (!empty($events))
				$select->orWhere('udalost_id IN (?)', $events);
			$rows = $select->query()->fetchAll();
		}
		catch (Exception $e) {
			It6_Log::err($e->getMessage(), It6_Log::TAG_BETRADAR_TEAM_OPERATION, null, $e);
			$result = $rows = false;
		}
		if (false !== $rows) {
			foreach ($rows as $row) {
				$brId = $row['brId'];
				if (isset($missing[$brId])) {
					$cache[$brId] = $row;
					$result[$brId] = $row;
					unset($missing[$brId]);
				}
				$brIds = array_keys($events, $row['bewaId']);
				foreach ($brIds as $_brId) {
					if ($_brId != $brId) {
						$row['brId'] = $_brId;
						$cache[$_brId] = $row;
						$result[$_brId] = $row;
						unset($missing[$_brId]);
					}
				}
			}
			if (!empty($missing)) {
				$ids = implode(',', $missing);
				It6_Log::warn("Unknown event(s). betradarTournamentIds=[$ids]", It6_Log::TAG_BETRADAR_OPERATION);
				return false;
			}
		}
	}
	return $result;
}

/**
 * Get BBAS team data (cached in array), new team DB record is created if needed.
 * @param array $teams array of competitors structures (see It6_Betradar_Match::$competitors)
 * @param integer $sportId
 * @return struct (brId => array teamData), teamData keys: id, brId, sportId, name, shortName
 */
public function getTeams(array $teams, $sportId) {
	static $cache = array(); // brId => data
	$result = array();
	$missing = array();
	foreach ($teams as $team) {
		$brId = $team['brTeamId']; 
		if (isset($cache[$brId]))
			$result[$brId] = $cache[$brId];
		else
			$missing[$brId] = $brId;
	}
	if (!empty($missing)) {
		try {
			$rows = $this->dbMain->select()
				->from('team', array(
					'bewaId' => 'id',
					'brId' => 'betradar_id',
					'sportId' => 'sport_id',
					'name' => '(TRIM(name))',
					'shortName' => '(TRIM(short_name))',
				))
				->where('betradar_id IN (?)', $missing)
				->query()
				->fetchAll();
		}
		catch (Exception $e) {
			It6_Log::err($e->getMessage(), It6_Log::TAG_BETRADAR_TEAM_OPERATION, null, $e);
			$result = $rows = false;
		}
		if (false !== $rows) {
			foreach ($rows as $row) {
				$brId = $row['brId'];
				$cache[$brId] = $row;
				$result[$brId] = $row;
				unset($missing[$brId]);
			}
			foreach ($missing as $brId) {
				if ($team = $this->createTeam($teams[$brId], $sportId)) {
					$cache[$brId] = $team;
					$result[$brId] = $team;
				}
				else
					It6_Log::err("Team could not be created. brId=$brId", It6_Log::TAG_BETRADAR_TEAM_OPERATION, array('teamBetradarId' => $brId));
			}
		}
	}
	return $result;
}

/**
 * Create new betradar team record, use old table for short name value.
 * @param struct $team competitor structure (see It6_Betradar_Match::$competitors)
 * @param integer $sportId
 * @return struct|boolean FALSE on error, otherwise structure with DB data of newly created team
 */
private function createTeam($team, $sportId, &$dbData = null) {
	try {
		$teamName = trim($team['text']);
		$rows = $this->dbMain->select()
			->from('team_old', array('name', 'shortName' => 'short_name', 'sportId' => 'sport_id'))
			->where('name LIKE ?', $teamName)
			->query()
			->fetchAll();
		$oldDbData = null;
		foreach ($rows as $row) {
			if ($row['sportId'] == $sportId) {
				$oldDbData = $row;
				break;
			}
			if (!isset($oldDbData))
				$oldDbData = $row;
		}
		$dbData = array(
			'betradar_id' => $team['brTeamId'],
			'sport_id' => $sportId,
		);
		if (!empty($oldDbData)) {
			$dbData['name'] = ($oldDbData['sportId'] == $sportId ? $oldDbData['name'] : $teamName);
			$dbData['short_name'] = $oldDbData['shortName'];
		}
		else
			$dbData['name'] = $dbData['short_name'] = $teamName;
		$this->dbMain->insert('team', $dbData);
		$this->createdTeams[$team['brTeamId']] = $teamName;
		return array(
			'id' => $this->dbMain->lastInsertId(),
			'brId' => $dbData['betradar_id'],
			'sportId' => $dbData['sport_id'],
			'name' => $dbData['name'],
			'shortName' => $dbData['short_name'],
		);
	}
	catch (Exception $e) {
		It6_Log::err(
			"Cannot insert team. brTeamId={$team['brTeamId']} bewaSportId=$sportId",
			It6_Log::TAG_BETRADAR_TEAM_OPERATION,
			$team,
			$e
		);
		return false;
	}
}

/**
 * Get all subtypes with their column sets for given bet types. (cached in array)
 * @param array $betTypes array of bet type IDs
 * @return array|boolean FALSE if something went wrong or something not found, otherwise array( typeId => subtypeId => columnId => ('bewaId' => columnId, 'name' => columnName) )
 */
public function getColumnSetsForBetTypes($betTypes) {
	static $cache = array();
	$result = array();
	$missing = array();
	foreach ($betTypes as $typeId) {
		if (isset($cache[$typeId]))
			$result[$typeId] = $cache[$typeId];
		else
			$missing[$typeId] = $typeId;
	}
	if (!empty($missing)) {
		try {
			$rows = $this->dbMain->select()
				->from(array('t' => 'typ_podtyp'), array('typeId' => 'typ_id', 'subtypeId' => 'podtyp_id'))
				->join(array('c' => 'podtyp_sloupce'), 't.podtyp_id=c.podtyp_id', array(
					'columnId' => 'sloupec_id',
					'name' => 'nazev',
					'order' => 'poradi',
				))
				->where('t.typ_id IN (?)', $missing)
				->query()
				->fetchAll();
		}
		catch (Exception $e) {
			It6_Log::err($e->getMessage(), It6_Log::TAG_BETRADAR_TEAM_OPERATION, null, $e);
			$result = $rows = false;
		}
		if (false !== $rows) {
			foreach ($rows as $row) {
				$columnId = $row['columnId'];
				$cache[ $row['typeId'] ][ $row['subtypeId'] ][$columnId] = array(
					'bewaId' => $columnId,
					'order' => $row['order'],
					'name' => $row['name'],
				);
			}
			$notFound = array();
			foreach ($missing as $typeId) {
				if (isset($cache[$typeId]))
					$result[$typeId] = $cache[$typeId];
				else
					$notFound[$typeId] = $typeId;
			}
			if (!empty($notFound)) {
				$ids = implode(',', $notFound);
				It6_Log::err("Subtypes columns not found for bet type(s). betTypeIds=[$ids]", It6_Log::TAG_BETRADAR_TEAM_OPERATION);
				$result = false;
			}
		}
	}
	return $result;
}

public function getDefaultMinWinRatio() {
	return 1.11;
}

public function getDefaultRiskLimit() {
	return 200000;
}

/**
 * Get bet type data
 * @param integer|array $betType One or more BBAS bet type IDs
 * @return boolean|array|struct FALSE on error or when not found, otherwise one or more bet type structs,
 *    type struct has fields: typeId, name, brTimeOffset, groupTypeIds
 */
public function getBetType($betType) {
	static $cache = array(); // (typeId => struct)
	$more = is_array($betType);
	if (!$more)
		$betType = array($betType);
	$result = array();
	$missing = array();
	foreach ($betType as $typeId) {
		if (isset($cache[$typeId]))
			$result[$typeId] = $cache[$typeId];
		else
			$missing[$typeId] = $typeId;
	}
	if (!empty($missing)) {
		try {
			$rows = $this->dbMain->select()
				->from(array('t' => 'typ'), array(
					'typeId' => 'typ_id',
					'name' => 'nazev',
					'brTimeOffset' => 'betradar_time_offset',
				))
				->joinLeft(array('gt' => 'typ'), 't.typ_alias_group=gt.typ_alias_group', array('groupTypeId' => 'typ_id'))
				->where('t.typ_id IN (?)', $betType)
				->query()
				->fetchAll();
		}
		catch (Exception $e) {
				It6_Log::err($e->getMessage(), It6_Log::TAG_BETRADAR_OPERATION, null, $e);
				return false;
		}
		$betTypes = array();
		foreach ($rows as $row) {
			$typeId = $row['typeId'];
			if (!isset($betTypes[$typeId])) {
				$betTypes[$typeId] = array(
					'typeId' => $typeId,
					'name' => $row['name'],
					'brTimeOffset' => $row['brTimeOffset'],
					'groupTypeIds' => (empty($row['groupTypeId']) ? false : array($row['groupTypeId'])),
				);
			}
			else {
				$betTypes[$typeId]['groupTypeIds'][] = $row['groupTypeId'];
			}
		}
		foreach ($betTypes as $typeId => $type) {
			$cache[$typeId] = $type;
			$result[$typeId] = $type;
			unset($missing[$typeId]);
		}
		if (!empty($missing)) {
			$ids = implode(',', $missing);
			It6_Log::err("Bet type(s) not found. betTypeIds=[$ids]", It6_Log::TAG_BETRADAR_OPERATION);
			return false;
		}
	}
	return ($more ? $result : $result[$betType[0]]);
}

/**
 * @param integer|array $betType One or more BBAS bet type IDs
 * @param integer $sportId BBAS sport ID
 * @return string|array Text note or array of text notes ($typeId => textNote)
 */
public function getBetTypeTextNote($betType, $sportId) {
	static $cache = array(); // (sportId => betType => textNote)
	$more = is_array($betType);
	if (!$more)
		$betType = array($betType);
	$result = array();
	$missing = array();
	foreach ($betType as $typeId) {
		if (isset($cache[$sportId][$typeId]))
			$result[$typeId] = $cache[$sportId][$typeId];
		else
			$missing[$typeId] = $typeId;
	}
	if (!empty($missing)) {
		try {
			$rows = $this->dbMain->select()
				->from('typ_sport_note', array(
					'typeId' => 'typ_id',
					'textNote' => 'text_note',
				))
				->where('sport_id=?', $sportId)
				->where('typ_id IN (?)', $missing)
				->query()
				->fetchAll();
		}
		catch (Exception $e) {
				It6_Log::err($e->getMessage(), It6_Log::TAG_BETRADAR_BET_OPERATION, null, $e);
				return false;
		}
		foreach ($rows as $row) {
			$typeId = $row['typeId'];
			$textNote = $row['textNote'];
			$cache[$sportId][$typeId] = $textNote;
			$result[$typeId] = $textNote;
			unset($missing[$typeId]);
		}
		foreach ($missing as $typeId) {
			$cache[$sportId][$typeId] = '';
			$result[$typeId] = '';
		}
	}
	return ($more ? $result : $result[$betType[0]]);;
}

/**
 * Get bet type settings
 * @param array $betTypes (tournamentId => typeId => TRUE|array(subtypeIds)) If TRUE is specified instead of list of subtype IDs, all subtypes are fetched.
 * @param array $tournamentMap (tournamentId => bewaEventId) If NULL then it is fetched by getTournaments() call
 * @return array|struct array( tournamentId => typeId => subtypeId => array(keys: minWinRate, riskLimit) ) 
 */
public function getBetTypeSettings($betTypes, $tournamentMap = null) {
	static $cacheSingle = array();
	static $cacheComplete = array();
	if (!isset($tournamentMap))
		$tournamentMap = $this->getTournaments(array_keys($betTypes));
	$invTournamentMap = array();
	$dbData = array();
	$single = array(); // single data
	$complete = array(); // all subtypes for type 
	foreach ($betTypes as $tournamentId => $typeIds) {
		if (empty($tournamentMap[$tournamentId]['bewaId']))
			continue;
		$eventId = $tournamentMap[$tournamentId]['bewaId'];
		$invTournamentMap[$eventId] = $tournamentId;
		foreach ($typeIds as $typeId => $subtypeIds) {
			if (true === $subtypeIds) {
				if (isset($cacheComplete[$tournamentId][$typeId]))
					$dbData[$tournamentId][$typeId] = $cacheSingle[$tournamentId][$typeId];
				else
					$complete[] = array($eventId,$typeId);
			}
			else {
				foreach ($subtypeIds as $subtypeId) {
					if (isset($cacheSingle[$tournamentId][$typeId][$subtypeId]))
						$dbData[$tournamentId][$typeId][$subtypeId] = $cacheSingle[$tournamentId][$typeId][$subtypeId];
					else
						$single[] = array($eventId,$typeId,$subtypeId);
				}
			}
		}
	}
	$fnTuple = function($items) { return '(' . implode(',', $items) . ')'; };
	$where = '';
	if (!empty($single))
		$where = '((udalost_id,typ_id,podtyp_id) IN (' . implode(',', array_map($fnTuple, $single)) . '))';
	if (!empty($complete)) {
		if (!empty($where))
			$where .= ' OR ';
		$where .= '((udalost_id,typ_id) IN (' . implode(',', array_map($fnTuple, $complete)) . '))';
	}
	if (!empty($where)) {
		try {
			$rows = $this->dbMain->select()
				->from('bet_settings', array(
					'eventId' => 'udalost_id',
					'typeId' => 'typ_id',
					'subtypeId' => 'podtyp_id',
					'minWinRatio' => 'vyhernost_min',
					'riskLimit' => 'risk_limit',
				))
				->where($where)
				->query()
				->fetchAll();
			foreach ($rows as $row) {
				$eventId = $row['eventId'];
				$tournamentId = $invTournamentMap[$eventId];
				$typeId = $row['typeId'];
				$subtypeId = $row['subtypeId'];
				$_dbData = array(
					'minWinRatio' => $row['minWinRatio'],
					'riskLimit' => $row['riskLimit'],
				);
				$dbData[$tournamentId][$typeId][$subtypeId] = $_dbData;
				$cacheSingle[$tournamentId][$typeId][$subtypeId] = $_dbData;
				foreach ($complete as $_complete) {
					if ($_complete[0] == $eventId && $_complete[1] == $typeId) {
						$cacheComplete[$tournamentId][$typeId] = true;
						break;
					}
				}
			}
		}
		catch (Exception $e) {
			It6_Log::err($e->getMessage(), It6_Log::TAG_BETRADAR_OPERATION, null, $e);
			return false;
		}
	}
	return $dbData;
}

/**
 * Retrieve BBAS bet subtype data.
 * @param integer|array $betSubtypeId One or more BBAS subtype IDs
 * @param NULL|array If one item was requested NULL or one structure is returned, otherwise array of structures is returned (can be empty)
 *                   ( 'subtypeId' => integer, 'columns' => array(columnId => array('name' => ..., 'order' => ...)) )
 */
public function getBetSubtype($betSubtypeId) {
	static $cache = array();
	$more = is_array($betSubtypeId);
	if (!$more) {
		if (isset($cache[$betSubtypeId]))
			return $cache[$betSubtypeId];
		$betSubtypeId = array($betSubtypeId);
	}
	$result = array();
	$unknown = array();
	foreach ($betSubtypeId as $id) {
		if (isset($cache[$id]))
			$result[$id] = $cache[$id];
		else
			$unknown[$id] = true;
	}
	if (!empty($unknown)) {
		$rows = $this->dbMain->select()
			->from(array('pt' => 'podtyp'), array('id' => 'podtyp_id'))
			->join(array('s' => 'podtyp_sloupce'), 'pt.podtyp_id=s.podtyp_id', array(
				'columnId' => 'sloupec_id',
				'columnName' => 'nazev',
				'columnOrder' => 'poradi'
			))
			->where('pt.podtyp_id IN (?)', array_keys($unknown))
			->query()
			->fetchAll();
		foreach ($rows as $row) {
			$id = $row['id'];
			$columnId = $row['columnId'];
			$columnData = array('name' => $row['columnName'], 'order' => $row['columnOrder']);
			if (empty($result[$id])) {
				$data = array(
					'subtypeId' => $row['id'],
					'columns' => array($columnId => $columnData),
				);
				$result[$id] = $data;
				$cache[$id] = $data;
				unset($unknown[$id]);
			}
			else {
				$result[$id]['columns'][$columnId] = $columnData;
				$cache[$id]['columns'][$columnId] = $columnData;
			}
		}
		if (!empty($unknown))
			It6_Log::warn('Unknown bet subtype(s) requested: [' . implode(',', $unknown) . ']');
	}
	if ($more)
		return $result;
	else {
		$betSubtypeId = $betSubtypeId[0];
		return (isset($result[$betSubtypeId]) ? $result[$betSubtypeId] : null);
	}
}

/**
 * @param array $brBets (brBetType => matchId => { (oddsType => effSpecialValueList) | true })
 * @return array (brBetType => matchId => oddsType => effSpecialValue => bewaBetId => bewaData)<br/>
 *               bewaData is struct with fields:<br/>
 *                  bewaId => BBAS bet ID <br/>
 *                  brBetType => BR bet type</br>
 *                  brId => BR match ID<br/>
 *                  brOddsType => BR odds type<br/>
 *                  brSpecialValue => BR special value<br/>
 *                  brCompetitorId => BR competitor ID<br/>
 *                  brParams => BR extra parameters<br/>
 *                  typeId => BBAS bet type ID<br/>
 *                  realTypeId => BBAS bet real type ID<br/>
 *                  subtypeId => BBAS bet subtype ID<br/>
 *                  eventId => BBAS event ID<br/>
 *                  betName => BBAS bet text<br/>
 *                  validFrom => timestamp for bet start<br/>
 *                  validTo => timestamp for bet end<br/>
 *                  brUpdate => BR autoupdate flag<br/>
 *                  parentId => BBAS bet ID of parent bet<br/>
 */
public function getBetradarBets($brBets) {
	$result = array();
	$matches = array();
	$tupples = array();
	foreach ($brBets as $brBetType => $matchIds) {
		$dbBrBetType = static::brBetTypeToDb($brBetType);
		foreach ($matchIds as $matchId => $oddsTypes) {
			if (true === $oddsTypes)
				$matches[] = array($dbBrBetType, $matchId);
			else {
				foreach ($oddsTypes as $oddsType => $effSpecialValues) {
					foreach ($effSpecialValues as $effSpecialValue) {
						$tupples[] = array($dbBrBetType, $matchId, $oddsType, $effSpecialValue);
					}
				}
			}
		}
	}
	if (empty($tupples) && empty($matches))
		return array();
	$select = $this->dbMain->select()
		->from(array('b' => 'sazky'), static::$betColumnMap)
		->joinLeft(array('k' => 'sazka_kurz_aktualni'), 'b.sazka_id=k.sazka_id', array(
			'columnId' => 'sloupec_id',
			'rate' => 'kurz',
		))
		->where('1=0');
	if (!empty($matches)) {
		$sqlTupples = static::createSqlTupples($matches, $this->dbMain, 2);
		$select->orWhere("(betradar_bet_type,betradar_match_id) IN $sqlTupples");
	}
	if (!empty($tupples)) {
		$sqlTupples = static::createSqlTupples($tupples, $this->dbMain, 2);
		$select->orWhere("(betradar_bet_type,betradar_match_id,betradar_odds_type,betradar_special_value) IN $sqlTupples");
	}
	$stmt = $select->query();
	//$betIds = array();
	while ($r = $stmt->fetch()) {
		//$betIds[] = $r['bewaId'];
		$rate = $r['rate'];
		$columnId = $r['columnId'];
		$brBetType = static::brBetTypeFromDb($r['brBetType']);
		unset($r['rate'], $r['columnId']);
		if (empty($result[$brBetType][$r['brId']][$r['brOddsType']][$r['brSpecialValue']][$r['bewaId']])) {
			//if (is_null($r['brSpecialValue']))
			//	$r['brSpecialValue'] = It6_Betradar_Match::DEFAULT_SPECIAL_VALUE;
			$r['brBetType'] = $brBetType;
			$r['rates'] = array($columnId => $rate);
			$r['validFrom'] = It6_Date::dbDatetimeToTimestamp($r['validFrom']);
			$r['validTo'] = It6_Date::dbDatetimeToTimestamp($r['validTo']);
			$result[$brBetType][$r['brId']][$r['brOddsType']][$r['brSpecialValue']][$r['bewaId']] = $r;
		}
		else
			$result[$brBetType][$r['brId']][$r['brOddsType']][$r['brSpecialValue']][$r['bewaId']]['rates'][$columnId] = $rate;
	}
	
	return $result;
}

/**
 * Fetches bets by given parent bet ID(s)
 * @param integer|array $parentId One or list of BBAS bet IDs, if none is given empty array is returned.
 * @param boolean $nonBrBets TRUE if only bets without BR match ID should be retrieved, FALSE otherwise
 * @param boolean $completeStructs TRUE if all fields should retrieved, FALSE if fields typeId,parentId only should be retrieved
 * @return array|boolean Map (bewaBetId => ('typeId' => bewaBetTypeId, 'parentId' => bewaBetParentId) | struct completeBet) or FALSE on error.
 */
public function getBetsByParentId($parentId, $nonBrBets, $completeStructs) {
	if (empty($parentId))
		return array();
	try {
		$select = $this->dbMain->select()
			->from(
				'sazky',
				$completeStructs ? static::$betColumnMap : array('bewaId' => 'sazka_id', 'typeId' => 'typ_id', 'parentId' => 'parent_id')
			)
			->where('parent_id IN (?)', $parentId);
		if ($nonBrBets)
			$select->where('betradar_match_id IS NULL');
		$rows = $select->query()->fetchAll();
		$bets = array();
		foreach ($rows as $row) {
			if ($completeStructs) {
				$row['brBetType'] = static::brBetTypeFromDb($row['brBetType']);
				$row['validFrom'] = It6_Date::dbDatetimeToTimestamp($row['validFrom']);
				$row['validTo'] = It6_Date::dbDatetimeToTimestamp($row['validTo']);
				$bets[$row['bewaId']] = $row;
			}
			else
				$bets[$row['bewaId']] = array('typeId' => $row['typeId'], 'parentId' => $row['parentId']);
		}
		return $bets;
	}
	catch (Exception $e) {
		It6_Log::warn(
			'Cannot retrieve bets by parent.',
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('parentId' => $parentId, 'nonBrBets' => $nonBrBets),
			$e
		);
		return false;
	}
}

/**
 * Determine which real bet type IDs are already used. (not cached)
 * @param integer $brMatchId
 * @param integer $betTypeId
 * @param integer|NULL $betParentId
 * @return array|boolean FALSE on error, list of BBAS bet type IDs otherwise (can be empty)
 */
public function getUsedRealBetTypeIds($brBetType, $brMatchId, $betTypeId, $betParentId) {
	try {
		$brMatchId = intval($brMatchId);
		$betTypeId = intval($betTypeId);
		$betParentId = intval($betParentId);
		$betParentSql = (empty($betParentId) ? '' : " OR (s.parent_id=$betParentId)");
		$dbBrBetType = static::brBetTypeToDb($brBetType);
		$rows = $this->dbMain->select()
			->from(array('t' => 'typ'), array('typeId' => 'typ_id'))
			->join(array('gt' => 'typ'), 't.typ_alias_group=gt.typ_alias_group AND t.typ_id=' . $betTypeId, array('groupTypeId' => 'typ_id'))
			->join(
				array('s' => 'sazky'),
				"s.real_typ_id=gt.typ_id AND ((s.betradar_match_id=$brMatchId AND s.betradar_bet_type='$dbBrBetType')$betParentSql)",
				array('betId' => 'sazka_id')
			)
			->query()
			->fetchAll();
		$result = array();
		foreach ($rows as $row) {
			$id = $row['groupTypeId'];
			$result[$id] = $id;
		}
		return array_keys($result);
	}
	catch (Exception $e) {
		It6_Log::warn($e->getMessage(), It6_Log::TAG_BETRADAR_BET_OPERATION, null, $e);
		return false;
	}
}

/**
 * Inserts new bet into database, also adds this bet to preloaded data.
 * Passed structure's fields 'bewaId' and 'rates' will be updated.
 * @param struct $bet Struct with following fields:<br/>
 *    brBetType</br>
 *    brId<br/>
 *    brOddsType<br/>
 *    brSpecialValue<br/>
 *    brParams<br/>
 *    typeId<br/>
 *    realTypeId<br/>
 *    subtypeId<br/>
 *    betName<br/>
 *    validFrom<br/>
 *    validTo<br/>
 *    parentId<br/>
 *    textNote<br/>
 *    riskLimit<br/>
 *    brUpdate<br/>
 *    parentId<br/>
 * @param array $columnRates (bewaColumndId => rate)
 * @param struct $extraData Structure with additional data for bet creation, structure has fields:
 *               <dl>
 *                <dt>integer timeOffsetTournament</dt>
 *                <dd>time offset in minutes for parts of matches in tournament (eg. 2nd half in soccer)</dd>
 *                <dt>float timeOffsetBetType</dt>
 *                <dd>time offset multiplier for bet type (multiplies tournament time offset)</dd>
 *               </dl>
 * @return boolean
 */
public function createBet(&$bet, $columnRates, $extraData) {
	$betTime = It6_Date::timestampToDb($bet['validFrom']);
	$validToDb = It6_Date::timestampToDb(self::offsetBetValidTo(
		$bet['validTo'], $extraData['timeOffsetTournament'], $extraData['timeOffsetBetType']
	));
	$data = array(
		'betradar_bet_type' => static::brBetTypeToDb($bet['brBetType']),
		'betradar_match_id' => $bet['brId'],
		'betradar_odds_type' => $bet['brOddsType'],
		'betradar_special_value' => $bet['brSpecialValue'],
		'betradar_params' => $bet['brParams'],
		'typ_id' => $bet['typeId'],
		'real_typ_id' => $bet['realTypeId'],
		'podtyp_id' => $bet['subtypeId'],
		'udalost_id' => $bet['eventId'],
		'text' => $bet['betName'],
		'platna_od' => $betTime,
		'platna_do' => $validToDb,
		'parent_id' => $bet['parentId'],
		'status' => It6_Models_Bet::STATUS_SUSPENDED,
		'status_ext' => It6_Models_Bet::STATUSEXT_BY_BETRADAR,
		'text_note' => $bet['textNote'],
		'risk_limit' => $bet['riskLimit'],
		'betradar_autoupdate' => $bet['brUpdate'],
		'bookmaker_id' => $this->bookmakerId,
		'parent_id' => $bet['parentId'],
	);
	It6_DbTransaction::begin($this->dbMain);
	try {
		// insert bet data
		$errMsg = 'Cannot insert bet.';
		$this->dbMain->insert('sazky', $data);
		$bet['bewaId'] = $betId = $this->dbMain->lastInsertId();
		// insert bet odds
		$errMsg = 'Cannot insert bet odds.';
		$rates = array();
		foreach ($columnRates as $columnId => $rate) {
			$this->dbMain->insert('sazka_kurz', array(
				'sazka_id' => $betId,
				'sloupec_id' => $columnId,
				'poradi' => 1,
				'kurz' => $rate,
				'platny_od' => $betTime,
				'kurz_zmena' => 0,
			));
			$rates[$columnId] = $rate;
			$this->dbMain->insert(
				'bet_column',
				array(
					'sazka_id' => $betId,
					'sloupec_id' => $columnId,
					'risk_limit_balance' => 0,
				)
			);
		}
		$change = It6_Models_BetChangelog::getBetChange(null, array(
			'betId' => $betId,
			'status' => It6_Models_Bet::STATUS_NEW,
			'confirmed' => 0,
			'paidOut' => 0,
			'validFrom' => $bet['validFrom'],
			'validTo' => $validToDb,
			'text' => $bet['betName'],
			'ticketText' => '',
			'textNote' => $bet['textNote'],
			'simple' => 0,
			'ako' => 0,
			'rates' => $rates,
			'correlated' => array(),
		));
		if (!empty($change)) {
			It6_Models_BetChangelog::saveBetChange($change, $this->dbMain);
		}
		It6_DbTransaction::commit($this->dbMain);
		$bet['rates'] = $rates;
		$this->preloadedData->addDbBet($bet);
		It6_Log::info("New bet created. betId=$betId", It6_Log::TAG_BETRADAR_BET_OPERATION, array('betId' => $betId, 'betData' => $bet));
		
		if(BR_CREATE_BETS_SUSPENDED == 0) {
			$this->activateBet($betId);
		} 
		
		return true;
	}
	catch (Exception $e) {
		It6_DbTransaction::rollback($this->dbMain);
		It6_Log::err($errMsg, It6_Log::TAG_BETRADAR_BET_OPERATION, array('betData' => $bet), $e);
		return false;
	}


}



/**
 * Activates a newly created bet.
 * @param int $betId The betId of the bet to be activated
 * @return void
 */
private function activateBet($betId) {
	$ws = Zend_Registry::get('ws');
	
	try {
		$ws->Bet->generateAlias($betId);
		It6_Log::info(
			"Bet '%bet%' got alias '%alias%'.",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('bet' => $betId, 'alias' => $alias)
		);
	}
	catch (Exception $e) {
		It6_Log::err(
			"Cannot generate alias for bet #'%bet%'",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('bet'=> $betId),
			$e
		);
	}

	try {
		$newParentId = $ws->Bet->updateBetPackHierarchy($betId);
		It6_Log::info(
			"New parent (#'%parent%') for bet  #'%bet%' has been resolved'.",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('bet'=> $betId, 'parent' => $newParentId)
		);
	}
	catch (Exception $e) {
		It6_Log::err(
			"Cannot update bet pack hierarchy for bet #'%bet%'",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('bet'=> $betId),
			$e
		);
	}

	try {
		$ws->Bet->activateBet($betId);
		It6_Log::info(
			"Bet #'%bet%' was activated.",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('bet'=> $betId)
		);
	}
	catch (Exception $e) {
		It6_Log::err(
			"Error activating bet '%bet%'.",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('bet' => $betId),
			$e
		);
	}

	It6_GlobalCache_Invalidator::invalidateSportsbookByBet($betId);
}



/**
 * Create association between bet and team
 * @param integer $bbasBetId BBAS bet ID
 * @param integer|array $brTeamId One or list of betradar team IDs
 * @return boolean TRUE on success, FALSE on error
 */
public function associateBetAndTeams($bbasBetId, $brTeamId) {
	$err = false;
	$associatedTeamIds = array();
	try {
		$newTeamIds = (is_array($brTeamId) ? array_flip($brTeamId) : array($brTeamId => 0));
		$stmt = $this->dbMain->select()
			->from('team_sazky', array('brTeamId' => 'betradar_team_id', 'betId' => 'sazka_id'))
			->where('betradar_team_id IN (?)', array_keys($newTeamIds))
			->where('sazka_id=?', $bbasBetId)
			->query();
		while ($row = $stmt->fetch())
			unset($newTeamIds[ $row['brTeamId'] ]);
		foreach (array_keys($newTeamIds) as $teamId) {
			try {
				$this->dbMain->insert(
					'team_sazky',
					array(
						'betradar_team_id' => $teamId,
						'sazka_id' => $bbasBetId,
					) 
				);
				$associatedTeamIds[] = $teamId;
			}
			catch (Exception $e) {
				$err = $e;
			}
		}
	}
	catch (Exception $e) {
		$err = $e;
	}
	$logData = array(
		'betId' => $bbasBetId,
		'brTeamIds' => $brTeamId,
		'associatedBrTeamIds' => $associatedTeamIds,
	);
	if ($err) {
		It6_Log::err(
			'Cannot associate bet and teams.',
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			$logData,
			is_object($err) ? $err : null
		);
		return false;
	}
	else {
		It6_Log::info(
			'Bet and teams were associated.',
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			$logData
		);
		return true;
	}
}

/**
 * [A,B..Z], [AA,BB..ZZ], [AAA,BBB..ZZZ] and so on
 * @param string $current Current value
 * @return string Next value in sequence
 */
public static function getSameLettersSequenceNext($current) {
	$n = strlen($current);
	$l = $current[0];
	if ('Z' == $l) {
		++$n;
		$l = 'A';
	}
	else
		$l = chr(ord($l) + 1);
	return str_repeat($l, $n);
}

/**
 * Adds logged changes to bet changelog data structure if needed.
 * It is assumed that only fields that was really changed are passed to this function.
 * This function reflect, that there are only specific fields, that are updated by BR.
 * @param string $field Name of field from bet structure
 * @param mixed $value New value of field
 * @param array $changelog Bet changelog structure to be updated 
 */
private function betChangeToChangelog($field, $value, array &$changelog) {
	$fnAddType = function ($type) use (&$changelog) {
		if (!in_array($type, $changelog['changeType'])) {
			$changelog['changeType'][] = $type;
		}
	};
	switch ($field) {
	case 'validFrom':
		$changelog['validFrom'] = $value;
		$fnAddType(It6_Models_BetChangelog::TYPE_VALIDITY);
		break;
	case 'validTo':
		$changelog['validTo'] = $value;
		$fnAddType(It6_Models_BetChangelog::TYPE_VALIDITY);
		break;
	case 'betName':
		$changelog['text'] = $value;
		$fnAddType(It6_Models_BetChangelog::TYPE_TEXT);
		break;
	case 'rates':
		$changelog['rates'] = $value;
		$fnAddType(It6_Models_BetChangelog::TYPE_RATE);
		break;
	default:
		break;
	}
}

/**
 * Computes new "valid to" timestamp with added offset from tournament and bet type
 * @param integer $validTo UNIX timestamp
 * @param integer $tournamentTimeOffset Offset unit size in minutes (parameter of tournament)
 * @param float $betTypeTimeOffset Count of units in offset (parameter of bet type)
 * @return integer UNIX timestamp
 */
public static function offsetBetValidTo($validTo, $tournamentTimeOffset, $betTypeTimeOffset) {
	$offset = intval($tournamentTimeOffset) * 60;
	$offset = round($offset * floatVal($betTypeTimeOffset));
	return ($validTo + $offset);
}

/**
 * Creates new subtype with appropriate columns (to be used with score type)
 * @param integer $betTypeId
 * @param array $outcomes Betradar outcomes (keys are column names)
 * @return struct|boolean Struct ('subtypeId' => subtypeId, 'columns' => (columnId => ('bewaId' => columnId, 'name' => columnName)))
 *                        or FALSE on error  
 */
public function createBetSubtype($betTypeId, $outcomes, $isRowColumn = true) {
	$prefix = 'Varianta ';
	$subtypeId = false;
	$exception = null;
	It6_DbTransaction::begin($this->dbMain);
	try {
		// determine subtype name
		$subtypeName = '';
		$stmt = $this->dbMain->select()
			->from('podtyp', array('name' => 'interni_nazev'))
			->where('interni_nazev LIKE ?', "$prefix%")
			->query();
		while ($row = $stmt->fetch()) {
			$name = trim( substr($row['name'], strlen($prefix)) );
			if (0 < strcasecmp($name, $subtypeName))
				$subtypeName = $name;
		}
		if (empty($subtypeName))
			$subtypeName = 'A';
		else
			$subtypeName = static::getSameLettersSequenceNext($subtypeName);
		$subtypeName = $prefix . $subtypeName;

		// insert new subtype
		$res = $this->dbMain->insert(
			'podtyp',
			array(
				'radek_sloupec' => ($isRowColumn ? 1 : 0),
				'sloupec_pocet_max' => count($outcomes),
				'text' => '',
				'interni_nazev' => $subtypeName,
			)
		);
		if ($res) { // insert all column for newly created subtype
			$subtypeId = $this->dbMain->lastInsertId();
			$order = 1;
			$columns = array();
			foreach (array_keys($outcomes) as $name) {
				$this->dbMain->insert(
					'podtyp_sloupce',
					array(
						'podtyp_id' => $subtypeId,
						'nazev' => $name,
						'poradi' => $order++, 
					)
				);
				$columnId = $this->dbMain->lastInsertId();
				$columns[$columnId] = array('bewaId' => $columnId, 'name' => $name);
			}
		}
		It6_DbTransaction::commit($this->dbMain);
	}
	catch (Exception $e) {
		$subtypeId = false;
		$exception = $e;
		It6_DbTransaction::rollback($this->dbMain);
	}
	if (!$subtypeId) {
		It6_Log::err(
			'New bet subtype was not created',
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('betTypeId' => $betTypeId, 'outcomes' => $outcomes),
			$exception
		);
		return false;
	} else {
		$this->newSubtypes[$subtypeId] = $subtypeName;
		It6_Log::info(
			'New bet subtype was created',
			 It6_Log::TAG_BETRADAR_BET_OPERATION,
			 array('betTypeId' => $betTypeId, 'subtypeId' => $subtypeId, 'subtypeName' => $subtypeName, 'outcomes' => $outcomes)
		);
		return array('subtypeId' => $subtypeId, 'columns' => $columns);
	}
}

/**
 * Updates parent bet association for bet group from betradar match
 * NOTE: It could be useful to to update somehow bets associated by old parent_id and bot by BR match.
 * @param integer $brBetType
 * @param integer $brMatchId BR match ID that identifies bets from match
 * @param integer $oldParentId BBAS ID of previous parent bet
 * @param integer $newParentId BBAS ID of new parent bet, parent ID will be set NULL for this bet
 * @param array $nonBrBetIds List of BBAS IDs of non-BR bet that should have been parent updated too
 * @return integer|boolean Number of updated rows on success, FALSE otherwise
 */
public function updateBetsParentId($brBetType, $brMatchId, $oldParentId, $newParentId, $nonBrBetIds) {
	It6_DbTransaction::begin($this->dbMain);
	try {
		$dbBrBetType = static::brBetTypeToDb($brBetType);
		$parentIds = $nonBrBetIds;
		if (!empty($oldParentId) && !in_array($oldParentId, $parentIds))
			$parentIds[] = $oldParentId;
		$where = "betradar_bet_type='$dbBrBetType' AND betradar_match_id=" . intval($brMatchId);
		if (!empty($parentIds))
			$where = "(($where) OR parent_id IN (" . $this->dbMain->quote($parentIds) . '))';
		$where .= ' AND sazka_id<>' . intval($newParentId); // AND betradar_autoupdate=1
		$n1 = $this->dbMain->update(
			'sazky',
			array('parent_id' => $newParentId),
			$where
		);
		$n2 = $this->dbMain->update(
			'sazky',
			array('parent_id' => null),
			array('sazka_id=?' => $newParentId)
		);
		It6_DbTransaction::commit($this->dbMain);
		It6_Log::info(
			'Bet group parent updated.', It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('brMatchId' => $brMatchId, 'oldParentId' => $oldParentId, 'newParentId' => $newParentId)
		);
		$this->preloadedData->updateDbBetParentId($brBetType, $brMatchId, $newParentId);
		return ($n1 + $n2);
	}
	catch (Exception $e) {
		It6_DbTransaction::rollback($this->dbMain);
		It6_Log::err(
			'Bet group parent not updated', It6_Log::TAG_BETRADAR_BET_OPERATION,
			 array('brMatchId' => $brMatchId, 'oldParentId' => $oldParentId, 'newParentId' => $newParentId)
		);
		return false;
	} 
}

/**
 * Updates correlated bets, so passed array of bets will be correlated between each other.
 * (Present correlation will be preserved.) 
 * @param array $dbBet Array of BBAS bet structures
 */
public function updateBetCorrelations($dbBets) {
	if (empty($dbBets))
		return;
	$fnMakeFstLower = function(&$fst, &$snd) {
		if ($fst > $snd) {
			$_ = $fst;
			$fst = $snd;
			$snd = $_;
		}
	};
	$betIds = array();
	$betTypeIds = array();
	$parentIds = array();
	$brMatches = array();
	$eventTypes = array();
	foreach ($dbBets as $bet) {
		$betId = $bet['bewaId'];
		$eventId = $bet['eventId'];
		$typeId = $bet['typeId'];
		$betIds[$betId] = $betId;
		$betTypeIds[$betId] = $typeId;
		if (!empty($bet['parentId'])) {
			$parentId = $bet['parentId'];
			$parentIds[$parentId] = $parentId;
		}
		if (!empty($bet['brId'])) {
			$brBetType = $this->brBetTypeToDb($bet['brBetType']);
			$brMatchId = $bet['brId'];
			$brMatches[$brBetType][$brMatchId] = true;
		}
		$eventTypes[$eventId][$typeId] = $typeId;
	}
	// fetch correlated bets by parent bet or by same BR match
	if (!empty($parentIds) || !empty($brMatches)) {
		$select = $this->dbMain->select()
			->from('sazky', array('betId' => 'sazka_id', 'typeId' => 'typ_id'));
		$fnWhere = 'where';
		if (!empty($parentIds)) {
			$select->$fnWhere('parent_id IN (?)', $parentIds);
			$fnWhere = 'orWhere';
		}
		if (!empty($brMatches)) {
			$pairs = array();
			foreach ($brMatches as $brBetType => $matchIds) {
				foreach ($matchIds as $matchId => $dummy)
					$pairs[] = array($brBetType, $matchId);
			}
			$pairsSql = $this->createSqlTupples($pairs, $this->dbMain, 2);
			$select->$fnWhere("(betradar_bet_type,betradar_match_id) IN $pairsSql");
			$fnWhere = 'orWhere';
		}
		$stmt = $select->query();
		while ($row = $stmt->fetch()) {
			$betId = $row['betId'];
			$betIds[$betId] = $betId;
			$betTypeIds[$betId] = $row['typeId'];
		}
	}
	// fetch combinations in DB already
	$stmt = $this->dbMain->select()
		->from('sazka_kombinace', array('id1' => 'sazka1_id', 'id2' => 'sazka2_id'))
		->where('sazka1_id IN (?)', $betIds)
		->orWhere('sazka2_id IN (?)', $betIds)
		->query();
	$existing = array();
	while ($row = $stmt->fetch()) {
		$betId1 = $row['id1'];
		$betId2 = $row['id2'];
		if (isset($betIds[$betId1]) && isset($betIds[$betId2])) {
			$fnMakeFstLower($betId1, $betId2);
			$existing[$betId1][$betId2] = $betId2;
		}
	}
	$betIds = array_keys($betIds);
	// determine what new combination are needed 
	$n = count($betIds);
	$new = array();
	for ($i = 0; $i < $n - 1; ++$i) {
		for ($j = $i + 1; $j < $n; ++$j) {
			$betId1 = $betIds[$i];
			$betId2 = $betIds[$j];
			$fnMakeFstLower($betId1, $betId2);
			if (!isset($existing[$betId1][$betId2]))
				$new[$betId1][$betId2] = null; // this is value for visibility, left NULL for DB default value to be used
		}
	}
	// determine new combinations from event/betType rules
	$pairs = array();
	foreach ($eventTypes as $eventId => $types) {
		foreach ($types as $typeId) {
			$pairs[] = array($eventId, $typeId);
		}
	}
	$pairsSql = $this->createSqlTupples($pairs, null, 2);
	$stmt = $this->dbMain->select()
		->from(
			'kombinace_druh',
			array(
				'eventId1' => 'udalost_id_1',
				'typeId1' => 'typ_id_1',
				'eventId2' => 'udalost_id_2',
				'typeId2' => 'typ_id_2',
				'visible' => 'viditelnost',
			)
		)
		->where("(udalost_id_1,typ_id_1) IN $pairsSql")
		->orWhere("(udalost_id_2,typ_id_2) IN $pairsSql")
		->query();
	$eventTypeCombs = array();
	while ($row = $stmt->fetch()) {
		$eventId1 = $row['eventId1'];
		$typeId1 = $row['typeId1'];
		$eventId2 = $row['eventId2'];
		$typeId2 = $row['typeId2'];
		$visible = $row['visible'];
		if (isset($eventTypes[$eventId1][$typeId1]))
			$eventTypeCombs[$eventId1][$typeId1][$eventId2][$typeId2] = $visible;
		if (isset($eventTypes[$eventId2][$typeId2]))
			$eventTypeCombs[$eventId2][$typeId2][$eventId1][$typeId1] = $visible;
	}
	$pairs = array();
	foreach ($eventTypeCombs as $eventId1 => $types1) {
		foreach ($types1 as $typeId1 => $events2) {
			foreach ($events2 as $eventId2 => $types2) {
				foreach ($types2 as $typeId2 => $visible) {
					$pairs[] = array($eventId2, $typeId2);
				}
			}
		}
	}
	if (!empty($pairs)) {
		$pairsSql = $this->createSqlTupplesLong(array('udalost_id', 'typ_id'), $pairs, null);
		$stmt = $this->dbMain->select()
			->from('sazky', array(
				'betId' => 'sazka_id',
				'eventId' => 'udalost_id',
				'typeId' => 'typ_id',
			))
			->where("$pairsSql")
			->query();
		$eventTypeBets = array();
		while ($row = $stmt->fetch()) { // fetch all bets for requested event/type pairs
			$betId = $row['betId'];
			$eventId = $row['eventId'];
			$typeId = $row['typeId'];
			$eventTypeBets[$eventId][$typeId2][$betId] = $betId;
		}
		if (!empty($eventTypeBets)) {
			foreach ($dbBets as $bet) { // now for each bet associate all bets according to event/type rule
				$betId1 = $row['bewaId'];
				$eventId1 = $row['eventId'];
				$typeId1 = $row['typeId'];
				if (isset($eventTypeCombs[$eventId1][$typeId1])) {
					foreach ($eventTypeCombs[$eventId1][$typeId1] as $eventId2 => $types2) {
						foreach ($types2 as $typeId2 => $visible) {
							if (!empty($eventTypeBets[$eventId2][$typeId2])) {
								foreach ($eventTypeBets[$eventId2][$typeId2] as $betId2) {
									$_betId1 = $betId1;
									$_betId2 = $betId2;
									$fnMakeFstLower($_betId1, $_betId2);
									if (!isset($existing[$_betId1][$_betId2]))
										$new[$_betId1][$_betId2] = $visible;
								}
							}
						}
					}
				}
			}
		}
	}
	// determine outright combinations event
	//TODO:
	// add new combinations
	$err = 0;
	$added = array();
	foreach ($new as $betId1 => $ids) {
		foreach ($ids as $betId2 => $visible) {
			try {
				$typeId1 = (isset($betTypeIds[$betId1]) ? $betTypeIds[$betId1] : 0);
				$typeId2 = (isset($betTypeIds[$betId2]) ? $betTypeIds[$betId2] : 0);
				if ($typeId1 && $typeId2 && It6_Models_BetType::shouldNotBeCorrelated($typeId1, $typeId2))
					continue;
				$data = array(
					'sazka1_id' => $betId1,
					'sazka2_id' => $betId2,
				);
				if (isset($visible))
					$data['kombinace_show'] = $visible;
				$this->dbMain->insert('sazka_kombinace', $data);
				$added[] = array($betId1, $betId2);
			}
			catch (Exception $e) {
				++$err;
			}
		}
	}
	It6_Log::info(
		'Bet combinations updated.',
		It6_Log::TAG_BETRADAR_BET_OPERATION,
		array('errorCount' => $err, 'newCombinations' => $added, 'betIds' => $betIds)
	);
	// bet changelog records (in fact we were only adding new correlations)
	$changelogBets = array();
	foreach ($added as $pair) {
		list($betId1, $betId2) = $pair;
		$changelogBets[$betId1][] = $betId2;
		$changelogBets[$betId2][] = $betId1;
	}
	foreach ($changelogBets as $betId => $_added) {
		It6_Models_BetChangelog::saveBetChangeOfCorrelated($betId, $_added, array(), $this->dbMain);
	}
}

/**
 * Updates BBAS bet in DB
 * @param integer $betId
 * @param array $changes Sparse set of BBAS structure fields with new values
 * @param struct $extraData Structure with additional data for update, structure has fields:
 *               <dl>
 *                <dt>integer timeOffsetTournament</dt>
 *                <dd>time offset in minutes for parts of matches in tournament (eg. 2nd half in soccer)</dd>
 *                <dt>float timeOffsetBetType</dt>
 *                <dd>time offset multiplier for bet type (multiplies tournament time offset)</dd>
 *               </dl>
 * @param interger|NULL $validTo UNIX timestamp, if not empty then update child bets' valid-to in DB using this value
 * @return boolean FALSE on error
 */
public function updateBet($betId, $changes, $extraData, $childValidTo = null) {
	$result = true;
	if (empty($betId) || empty($changes))
		return false;
	$changelog = array(
		'changeType' => array(),
	);
	// preprocess changes
	if (empty($changes['rates']))
		$rates = null;
	else {
		$rates = $changes['rates'];
		unset($changes['rates']);
	}

	// update bet data
	$err = false;
	try {
		$dbChanges = array();
		foreach ($changes as $field => $value) {
			if ('validTo' == $field) {
				$value = It6_Date::timestampToDb(self::offsetBetValidTo(
					$value, $extraData['timeOffsetTournament'], $extraData['timeOffsetBetType']
				));
			}
			$dbChanges[ isset(static::$betColumnMap[$field]) ? static::$betColumnMap[$field] : $field ] = $value;
			$this->betChangeToChangelog($field, $value, $changelog);
		}
		if (!empty($dbChanges)) {
			if (!$this->dbMain->update('sazky', $dbChanges, array('sazka_id=?' => $betId)))
				$err = true;
		}
	}
	catch (Exception $e) {
		$err = $e;
	}
	if ($err) {
		if (!is_object($err))
			$err = null;
		It6_Log::warn('Bet was not updated', It6_Log::TAG_BETRADAR_BET_OPERATION, array('betId' => $betId, 'changes' => $changes), $err);
		$result = false;
	}
	else
		It6_Log::info('Bet was updated', It6_Log::TAG_BETRADAR_BET_OPERATION, array('betId' => $betId, 'changes' => $changes));

	if (!empty($rates)) {
		// update rates
		It6_DbTransaction::begin($this->dbMain);
		try {
			$rows = $this->dbMain->select()
				->from('sazka_kurz', array(
					'betId' => 'sazka_id',
					//'columnId' => 'sloupec_id',
					'lastOrder' => new Zend_Db_Expr('MAX(poradi)'),
					//'rate' => new Zend_Db_Expr('MAX(kurz)'),
				))
				->where('sazka_id=?', $betId)
				//->where('sloupec_id=?', $columnId)
				//->group(array('sazka_id', 'sloupec_id'))
				->group('sazka_id')
				->query()
				->fetchAll();
			if (empty($rows))
				$order = 1;
			else
				$order = intval($rows[0]['lastOrder']) + 1;
			$now = It6_Date::dbNow();
			foreach ($rates as $columnId => $rate) {
				$this->dbMain->insert(
					'sazka_kurz',
					array(
						'sazka_id' => $betId,
						'sloupec_id' => $columnId,
						'poradi' => $order,
						'kurz' => $rate,
						'platny_od' => $now,
						'kurz_zmena' => 0,
					)
				);
			}
			It6_DbTransaction::commit($this->dbMain);
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($this->dbMain);
			$err = $e;
		}
		if ($err) {
			if (!is_object($err))
				$err = null;
			It6_Log::warn('Bet rates were not updated', It6_Log::TAG_BETRADAR_BET_OPERATION, array('betId' => $betId, 'rates' => $rates), $err);
			$result = false;
		}
		else {
			It6_Log::info('Bet rates were updated', It6_Log::TAG_BETRADAR_BET_OPERATION, array('betId' => $betId, 'rates' => $rates));
			$changelog['rates'] = It6_Models_BetChangelog::encodeRates($rates);
			if (!in_array(It6_Models_BetChangelog::TYPE_RATE, $changelog['changeType'])) {
				$changelog['changeType'][] = It6_Models_BetChangelog::TYPE_RATE;
			}
		}
	}

	if (!empty($changelog['changeType'])) {
		$changelog['betId'] = $betId;
		$changelog['changeTime'] = It6_Date::dbNow();
		It6_Models_BetChangelog::saveBetChange($changelog, $this->dbMain);
	}

	if (!empty($childValidTo)) {
		// update child bets' valid-to
		$toNotUpdate = It6_Models_BetType::getAllToNotUpdateValidToTimeByParent();
		if (false !== $toNotUpdate) {
			$select = $this->dbMain->select()
				->from(array('s' => 'sazky'), array('betId' => 'sazka_id'))
				->join(
					array('u' => 'udalost'),
					's.udalost_id=u.udalost_id',
					array('timeOffset' => 'betradar_time_offset')
				)
				->join(
					array('t' => 'typ'),
					's.typ_id=t.typ_id',
					array('timeOffsetCoef' => 'betradar_time_offset')
				)
				->where('s.parent_id=?', $betId)
				->where('s.proplacena=0');
			if (!empty($toNotUpdate)) {
				$select->where('s.typ_id NOT IN (?)', $toNotUpdate);
			}
			$updated = 0;
			$toUpdate = array();
			try {
				$rows = $select->query()->fetchAll();
				if (!empty($rows)) {
					foreach ($rows as $row) {
						$_validTo = self::offsetBetValidTo(
							$childValidTo, $row['timeOffset'], $row['timeOffsetCoef']
						);
						$toUpdate[$_validTo][] = $row['betId'];
					}
					unset($rows);
					foreach ($toUpdate as $_validTo => $updateBetIds) {
						$dbValidTo = It6_Date::timestampToDb($_validTo);
						$_updated = $this->dbMain->update(
							'sazky',
							array('platna_do' => $dbValidTo),
							array(
								'sazka_id IN (?)' => $updateBetIds,
								'platna_do<>?' => $dbValidTo,
							)
						);
						if ($_updated) {
							foreach ($updateBetIds as $_betId) {
								It6_Models_BetChangelog::saveBetChangeOfValidity(
									$_betId, null, $dbValidTo, $this->dbMain
								);
							}
							$updated += $_updated;
						}
					}
				}
			}
			catch (Exception $e) {
				It6_Log::warn(
					"Derived bets validTo update error: parentBet #'%bet%'.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array('betId' => $betId),
					$e
				);
				$updated = $result = false;
			}
			if (false !== $updated) {
				It6_Log::info(
					"Derived bets validTo updated.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array('bet' => $betId, 'count' => $updated, 'betIds' => $toUpdate)
				);
			}
		}
	}
	return $result;
}

/**
 * Updates result data of bet
 * @param integer $betId BBAS bet ID
 * @param string $score Scores for bet (type => score)
 * @param array|NULL $columns List of BBAS column IDs that win, ignored if not evaluating
 * @param array|NULL $allScores If set, BR scores will be saved into DB
 * @param boolean $cancel TRUE to set status to CANCELED instead of EVALUATED, default is FALSE
 * @param boolean $scoreOnly FALSE to set status to EVALUATED/CANCELED etc., TRUE to preserve bet status etc., default is FALSE.
 *                           If canceling then parameter is ignored (equal to FALSE).
 * @return boolean
 */
public function updateBetResult($betId, $score, $columns, $allScores, $cancel = false, $scoreOnly = false) {
	It6_DbTransaction::begin($this->dbMain);
	try {
		$data =	array(
			'score' => $score,
		);
		$status = It6_Models_Bet::STATUS_EVALUATED;
		if ($cancel) {
			$this->addBetCanceledAtBetradar($betId, ($scoreOnly ? self::CANCELED_BY_RESULT : self::CANCELED_BY_RESULT_SAVED));
			$status = It6_Models_Bet::STATUS_CANCELED;
			$data['message'] = new Zend_Db_Expr("TRIM(CONCAT(message, ' Canceled by Betradar.'))");
		}
		if (!$scoreOnly) {
			$data['status'] = $status;
			$data['vysledek'] = (empty($columns) ? '' : implode(';', $columns)) . ';';
		}
		$count = $this->dbMain->update(
			'sazky',
			$data,
			array(
				'sazka_id=?' => $betId,
				'status NOT IN (3,1)',
				'overena=0'
			)
		);
		if (isset($allScores)) {
			$dbScores = array(
				'sazka_id' => $betId,
				'all_scores' => Zend_Json::encode($allScores),
			);
			foreach ($allScores as $type => $_score) {
				$type = strtoupper($type);
				foreach (self::$brScoreToDbColumnMap as $mapType => $dbColumn) {
					if (strtoupper($mapType) == $type) {
						$dbScores[$dbColumn] = $_score;
						break;
					}
				}
			}
			$this->dbMain->delete('sazky_result', array('sazka_id=?' => $betId));
			$this->dbMain->insert('sazky_result', $dbScores);
		}
		if ($count) {
			It6_Models_BetChangelog::saveBetChangeOfStatus($betId, $status, 0, 0, $this->dbMain);
		}
		It6_DbTransaction::commit($this->dbMain);
	}
	catch (Exception $e) {
		It6_DbTransaction::rollback($this->dbMain);
		It6_Log::warn(
			'Bet result was not set',
			It6_Log::TAG_BETRADAR_RESULT_OPERATION,
			array('betId' => $betId, 'score' => $score, 'columns' => $columns, 'allScores' => $allScores),
			$e
		);
		return false;
	}
	$msg = ($count > 0 ? 'Bet result was set.' : 'Bet result was already set.');
	if ($cancel)
		$msg .= ' (Bet was canceled.)';
	if ($scoreOnly)
		$msg .= ' (Only scores, status preserved.)';
	It6_Log::info(
		$msg,
		It6_Log::TAG_BETRADAR_RESULT_OPERATION,
		array('betId' => $betId, 'score' => $score, 'columns' => $columns, 'allScores' => $allScores)
	);
	return true;
}

/**
 * FIXME: In logs can be Array to String conversion when bewaBetId is Array
 * Change bet status in DB to be suspended
 * @param integer|array $bbasBetId One or list of BBAS bet IDs of bets being suspended
 * @param string $cancelationType Use CANCELED_* class constants not the "_SAVED" version,
 *               if $registerOnly is FALSE, it is converted to "_SAVED" version automatically
 * @param boolean $registerOnly TRUE if status is not changed in DB, TRUE is default.
 * @return boolean TRUE on success, FALSE on error
 */
public function suspendBet($bbasBetId, $cancelationType, $registerOnly = true) {
	if ($registerOnly) {
		$this->addBetCanceledAtBetradar($bbasBetId, $cancelationType);
		It6_Log::info(
			'Bet was suspended. (notification only)',
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('betId' => $bbasBetId)
		);
	}
	else {
		$count = 0;
		try {
			$count = $this->dbMain->update(
				'sazky',
				array('status' => It6_Models_Bet::STATUS_SUSPENDED),
				array(
					'status=?' => It6_Models_Bet::STATUS_NEW,
					'sazka_id IN (?)' => $bbasBetId,
				)
			);
		}
		catch (Exception $e) {
			It6_Log::err(
				'Bet could not be suspended.',
				It6_Log::TAG_BETRADAR_BET_OPERATION,
				array('betId' => $bbasBetId),
				$e
			);
			return false;
		}
		It6_Log::info(
			'Bet was suspended.',
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('betId' => $bbasBetId)
		);
		if ($count) {
			if (isset(self::$canceledTypeSavedMap[$cancelationType])) {
				$this->addBetCanceledAtBetradar($bbasBetId, self::$canceledTypeSavedMap[$cancelationType]);
			}
			It6_Models_BetChangelog::saveBetChangeOfStatus(
				$bbasBetId, It6_Models_Bet::STATUS_SUSPENDED, 0, 0, $this->dbMain
			);
		}
		return true;
	}
}

/**
 * Cancels bet if it is still active.
 * @param integer|array $bbasBetId One or list of BBAS bet IDs of bets to cancel
 * @param string $cancelationType Use CANCELED_* class constants
 * @return integer|boolean Actual number of bets canceled or FALSE on error
 */
public function cancelBet($bbasBetId, $cancelationType) {
	try {
		$n = $this->dbMain->update(
			'sazky',
			array(
				'status' => It6_Models_Bet::STATUS_CANCELED,
				'status_ext' => It6_Models_Bet::STATUSEXT_BY_BETRADAR,
			),
			array(
				'sazka_id IN (?)' => $bbasBetId,
				'status=?' => It6_Models_Bet::STATUS_NEW,
			)
		);
		$betCount = (is_array($bbasBetId) ? count($bbasBetId) : 1);
		It6_Log::info(
			"$n of $betCount was canceled.",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('betId' => $bbasBetId)
		);
		if ($n) {
			It6_Models_BetChangelog::saveBetChangeOfStatus(
				$bbasBetId, It6_Models_Bet::STATUS_CANCELED, 0, 0, $this->dbMain
			);
		}
		$this->addBetCanceledAtBetradar($bbasBetId, $cancelationType);
		return $n;
	}
	catch (Exception $e) {
		It6_Log::err(
			'Bet could not be canceled.',
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('betId' => $bbasBetId),
			$e
		);
		return false;
	}
}

/**
 * Sends report about problems during import,
 * then sends report about bets canceled in BR's offer (if any).
 * @param string $file
 * @return boolean
 */
private function report($file) {

	$fnMakeBlock = function($header, $list, $emptyMsg, $associative) {
		if (empty($list) && !$emptyMsg)
			return '';
		$buff = "\n$header\n\n";
		if (empty($list))
			$buff .= "$emptyMsg\n";
		else if ($associative) {
			foreach ($list as $key => $text)
				$buff .= "$key - $text\n";
		}
		else
			$buff .= implode("\n", $list) . "\n";
		return $buff;
	};

	$recipients = explode(',', $this->bookmakerEmails);

	if (empty($recipients)) {
		It6_Log::warn('No email recipients for import report.', It6_Log::TAG_BETRADAR_OPERATION);
	} else {
		// error report
		$errorCount = count($this->reportedErrors)
			+ count($this->unknownSports)
			+ count($this->unknownTournaments)
			+ count($this->unknownTeams)
			+ count($this->unknownOddsTypes)
			+ count($this->unknownResultBetTypes);

		$newCreated = count($this->createdTeams)
			+ count($this->newSubtypes);

		$sendEmail = ($this->sendReportAlways || ($this->sendReportOnError && (0 < $errorCount || 0 < $newCreated)));

		if ($sendEmail) {
			$body = "File: $file\n\n";
			$body .= $fnMakeBlock('*** General errors:', $this->reportedErrors, false, false);
			$body .= $fnMakeBlock('*** New teams created (BRID - name):', $this->createdTeams, false, true);
			$body .= $fnMakeBlock('*** New bet subtype created (BBAS subtype ID - name):', $this->newSubtypes, false, true);
			$body .= $fnMakeBlock('*** Unknown sports (BRID - name):', $this->unknownSports, false, true);
			$body .= $fnMakeBlock('*** Unknown events (BRID - name):', $this->unknownTournaments, false, true);
			$body .= $fnMakeBlock('*** Unknown teams (BR team ID - name):', $this->unknownTeams, false, true);
			$body .= $fnMakeBlock('*** Unsupported odds types  (BR odds type):', $this->unknownOddsTypes, false, false);
			$body .= $fnMakeBlock('*** Unsupported bet types for results (BBAS bet type ID):', $this->unknownResultBetTypes, false, false);

			$body .= "\nImport started at: " . It6_Date::timestampToDateTime($this->startedAt) . "\n";
			$body .= "\nImport finished at: " . It6_Date::timestampToDateTime($this->finishedAt) . "\n";

			$mail = new Zend_Mail('utf-8');
			$mail->setBodyText($body);
			$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
			foreach ($recipients as $r) {
				$mail->addTo($r, $r);
			}
			$mail->setSubject(MAIL_SUBJECT_PREFIX . ' ' . SUBJECT_PREFIX . 'Betradar import report');
			try {
				if ($mail->send()) It6_Log::info('Email with betradar import errors was sent.');
			}
			catch (Exception $e) {
				It6_Log::err('Email with import report could not be sent.', It6_Log::TAG_BETRADAR_OPERATION, null, $e);
			}
		}

		// bet cancelation report
		if (!empty($this->betsCanceledAtBr)) {
			$body = "File: $file\n\n*** BBAS bet IDs that are to be canceled or was canceled\n\n";
			$types = array(
				self::CANCELED_IN_OFFER => 'Bets removed from Betradar offer but NOT from BBAS offer',
				self::CANCELED_IN_OFFER_SAVED => 'Bets removed from Betradar offer AND from BBAS offer',
				self::CANCELED_BY_RESULT => 'Bets with unknown result NOT canceled',
				self::CANCELED_BY_RESULT_SAVED => 'Bets with unknown result AND canceled',
			);
			foreach ($types as $type => $heading) {
				if (!empty($this->betsCanceledAtBr[$type])) {
					$body .= "\n$heading:\n\n";
					$body .= implode("\n", array_unique($this->betsCanceledAtBr[$type])) . "\n";
				}
			}
			$mail = new Zend_Mail('utf-8');
			$mail->setBodyText($body);
			$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
			foreach ($recipients as $r) {
				$mail->addTo($r, $r);
			}
			$mail->setSubject(MAIL_SUBJECT_PREFIX . ' ' . SUBJECT_PREFIX . 'Betradar import report - canceled bets');
			try {
				$mail->send();
			}
			catch (Exception $e) {
				It6_Log::err('Email with import report for bets cancelations could not be sent.', It6_Log::TAG_BETRADAR_OPERATION, null, $e);
			}
		}
	}
}

} // class