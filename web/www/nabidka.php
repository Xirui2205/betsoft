<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);

define('RUNNING_FROM_CLI', true);
define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'common/library/' . PATH_SEPARATOR .
		ROOT . 'betting-service/application/' . PATH_SEPARATOR .
		ROOT . 'betting-service/library/' . PATH_SEPARATOR .
		ROOT . 'admin/library/' . PATH_SEPARATOR
);

include(ROOT . 'common/config_util.inc.php');

$appEnv = getAppEnv();
if (empty($appEnv)) {
    echo "APPLICATION_ENVIRONMENT environment variable not set!\n";
    exit(1);
}

include(ROOT . 'common/config.php');
include(ROOT . 'common/includes.inc.php');

require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');
$autoloader->registerNamespace('WarpTurn_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN);
$dbAdmin = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);

$now = It6_Date::dbNow();
$langId = 1; //$_SESSION['lang_id'];

$langColSql = "utf8_czech_ci";
//if (isset($_SESSION['lang_collation'])) {
//	$langColSql = trim($_SESSION['lang_collation']);
//}



/*
$select = $db->select()
	->from(array('u'=>'udalost'))
	->join(
			array('s'=>'sport'),
			's.sport_id=u.sport_id AND s.zobrazeno = 1',
			array('sport_nazev'=>"s.nazev"))
	->join(
			array('o'=>'oblast'),
			'o.oblast_id=u.oblast_id',
			array())
	->joinLeft(
			array('tro' => 'preklady'),
			'o.nazev=tro.index_pole AND tro.lang_id=' . intval($langId),
			array())
	->join(
			array('sz'=>'sazky'),
			'u.udalost_id=sz.udalost_id AND sz.live=0 AND sz.status=0 AND sz.risk_limit > sz.risk_limit_balance',
			array())
	->join(
			array('t'=>'typ'),
			'sz.typ_id=t.typ_id AND t.zobrazeno = 1',
			array()
		)
	->join(
			array('tu'=>'typ_udalost'),
			'tu.typ_id=t.typ_id AND tu.udalost_id=u.udalost_id',
			array()
		)
	->where('sz.live=0')
	->where('sz.status=0')
	->where('sz.platna_od<=?',$now )
	->where('sz.platna_do >= ?',$now)
	->where('sz.risk_limit > sz.risk_limit_balance')
	->where('u.platne_od<=?',$now)
	->where('u.platne_do>=?',$now )
	->where('u.zobrazeno = 1')
	->group('u.udalost_id')
	->order(array('s.pozice','o.pozice','o.nazev','u.pozice'))
	->columns(array('u.udalost_id','onazev'=>"(TRIM(COALESCE(tro.text,o.nazev)))"));
	//->query()->fetchAll();
*/


$select = $db->select()
	->from(
		array('sz'=>'sazky'),
		array(
			's.sport_id',
			'u.oblast_id',
			'snazev'=>'s.nazev',
			'unazev'=>'u.nazev',
			'onazev'=>"(TRIM(COALESCE(tro.text,o.nazev)))",
			'u.udalost_id',
			'u.betradar_udalost_id',
			'sz.typ_id',
			'sz.real_typ_id',
			'sz.sazka_id',
			'sz.jednoducha',
			'sz.ako',
			//'sz.betradar_sazka_id',
			'sz.betradar_match_id',
			'mtext'=>"IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text)",
			'sz.podtyp_id',
			'tnazev'=>'t.nazev',
			'textNote'=>'tsn.text_note',
			'betTextNote' => 'sz.text_note',
			'pnazev'=>'ps.nazev',
			'ps.sloupec_id',
			'sk.kurz',
			'platna_do'=>'sz.platna_do',
			'ptext'=>'p.text',
			'sz.info',
			'szAlias' => 'alias',
			'szAliasNew' => 'alias_new',
			'parent_id' => 'sz.parent_id',
			'realTypeId' => 't2.typ_id',
			't2.typ_alias_id',
			'typeOrder' => 'tot.name',
			'p.radek_sloupec',
			'p.sloupec_pocet_max',
			'liveBetId' => 'ml.id'
	))
	->join(
		array('u'=>'udalost'),
		'sz.udalost_id=u.udalost_id AND u.zobrazeno = 1',null
	)
	->join(
		array('s'=>'sport'),
		's.sport_id=u.sport_id AND s.zobrazeno = 1', null
	)
	->join(
		array('o'=>'oblast'),
		'o.oblast_id=u.oblast_id', null
	)
	->joinLeft(
		array('tro' => 'preklady'),
		'o.nazev=tro.index_pole AND tro.lang_id=' . intval($langId),
		array()
	)
	->join(
		array('t'=>'typ'),
		'sz.typ_id=t.typ_id AND t.zobrazeno', null
	)
	->join(
		array('t2'=>'typ'),
		'COALESCE(sz.real_typ_id, sz.typ_id)=t2.typ_id', null
	)
	->joinLeft	(
		array('tot'=>'typ_order_type'),
		'tot.id=t2.order_type_id', null
	)
	->join(
		array('p'=>'podtyp'),
		'sz.podtyp_id=p.podtyp_id', null
	)
	->joinLeft(
		array('tu'=>'typ_udalost'),
		'tu.typ_id=t.typ_id AND tu.udalost_id=u.udalost_id', null
	)
	->joinLeft(
		array('tsn'=>'typ_sport_note'),
		'tsn.typ_id=t.typ_id AND tsn.sport_id=s.sport_id', null
	)
	->join(
		array('sk'=>'sazka_kurz_aktualni'),
		'sk.sazka_id = sz.sazka_id', null
	)
	->join(
		array('ps'=>'podtyp_sloupce'),
		'ps.sloupec_id = sk.sloupec_id', null
	)
	->joinLeft(
		array('ml' => 'match_live'),
		'sz.sazka_id = ml.special_id', null
	)
	->where('sz.live=0')
	->where('sz.status=0')
	->where('sz.platna_od<=?',$now )
	->where('sz.platna_do >= ?',$now)
	//->where('sz.risk_limit > sz.risk_limit_balance')
	->where('u.platne_od<=?',$now)
	->where('u.platne_do>=?',$now)
	//->where('u.udalost_id IN (?)',$events)
	->order('sz.udalost_id')
	->order('s.pozice')
	->order('o.pozice')
	->order('onazev')
	->order('u.pozice')
	->order('tu.order')
	//->order('sz.platna_do '.$now)
	//->order("IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text)")
	//->order('sz.alias')
	->order('sz.alias_new')
	->order('sz.sazka_id')
	->order('ps.poradi');
$sazky = $select->query()->fetchAll();

$tisk = array();
foreach ($sazky as $sazka) {
	//$tisk[$sazka["sport_id"]]["sport_id"] = $sazka["sport_id"];
	$tisk[$sazka["sport_id"]]["snazev"] = $sazka["snazev"];

	//$tisk[$sazka["sport_id"]][$sazka["oblast_id"]]["oblast_id"] = $sazka["oblast_id"];
	$tisk[$sazka["sport_id"]][$sazka["oblast_id"]]["onazev"] = $sazka["onazev"];

	//$tisk[$sazka["sport_id"]][$sazka["oblast_id"]][$sazka["udalost_id"]]["udalost_id"] = $sazka["udalost_id"];
	$tisk[$sazka["sport_id"]][$sazka["oblast_id"]][$sazka["udalost_id"]]["szAlias"] = $sazka["szAlias"];
	$tisk[$sazka["sport_id"]][$sazka["oblast_id"]][$sazka["udalost_id"]]["date"] = date("d.m", strtotime($sazka["platna_do"]));
	$tisk[$sazka["sport_id"]][$sazka["oblast_id"]][$sazka["udalost_id"]]["time"] = date("H:i", strtotime($sazka["platna_do"]));
	$tisk[$sazka["sport_id"]][$sazka["oblast_id"]][$sazka["udalost_id"]]["mtext"] = $sazka["mtext"];

	//$tisk[$sazka["sport_id"]][$sazka["oblast_id"]][$sazka["sazka_id"]][$sazka["sloupec_id"]] = $sazka;
}

echo "<pre>";
print_r($tisk);					

/*
(
    [sport_id] => 1001
    [oblast_id] => 247
    [snazev] => Fotbal
    [unazev] => f-cze-1liga
    [onazev] => ÄŚesko
    [udalost_id] => 1
    [betradar_udalost_id] => 49
    [typ_id] => 16
    [real_typ_id] => 
    [sazka_id] => 1189877
    [jednoducha] => 0
    [ako] => 0
    [betradar_match_id] => 5685844
    [mtext] => ÄŚ. BudÄ›jovice - Teplice
    [podtyp_id] => 218
    [tnazev] => 1.polocas
    [textNote] => 
    [betTextNote] => 
    [pnazev] => 1
    [sloupec_id] => 1857
    [kurz] => 3.64
    [platna_do] => 2017-08-08 00:00:00
    [ptext] => 
    [info] => 
    [szAlias] => 4218
    [szAliasNew] => 17464
    [parent_id] => 1189866
    [realTypeId] => 16
    [typ_alias_id] => 44
    [typeOrder] => 
    [radek_sloupec] => 0
    [sloupec_pocet_max] => 0
    [liveBetId] => 
)
*/
