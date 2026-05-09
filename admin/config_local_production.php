<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname(__FILE__) ) . '/');

set_include_path (
	'.' . PATH_SEPARATOR .
	get_include_path() . PATH_SEPARATOR .
	ROOT . 'admin/include/class/'. PATH_SEPARATOR .
	ROOT . 'admin/application/'. PATH_SEPARATOR .
	ROOT . 'admin/library/' . PATH_SEPARATOR .
//	ROOT . 'common/library/'. PATH_SEPARATOR .
	ROOT . 'betting-service/application/'
);

include(ROOT . 'common/config_util.inc.php');

//def('WS_WRAPPER', 'XmlRpc');
def('WS_WRAPPER', 'Direct');

if ( WS_WRAPPER == 'Direct' ) {
	set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'betting-service/library'
	);
}

if(!defined('DOC_ROOT')) define('DOC_ROOT', ROOT.'tmp/');
if(!defined('TEMP_DIRECTORY')) define('TEMP_DIRECTORY', ROOT.'tmp/');
if(!defined('UPLOAD_ROOT')) define('UPLOAD_ROOT',"tmp/"); //adresar pro nahravani souboru
#cesta ke galerii v /web/ pro nahledy
if(!defined('GALLERY_PATH')) define('GALLERY_PATH', ROOT . 'admin/www/images/gallery/');
if(!defined('GALLERY_URL')) define('GALLERY_URL', '/images/gallery/');
if(!defined('GALLERY_THUMB_PATH')) define('GALLERY_THUMB_PATH', ROOT . 'admin/www/images/gallery/small/');
if(!defined('GALLERY_THUMB_URL')) define('GALLERY_THUMB_URL', '/images/gallery/small/');
if(!defined('SITE_WEB_ROOT')) define('SITE_WEB_ROOT',ROOT.'web/www/');

#Dalsi nastaveni#
if(!defined('WEB_ROOT')) define('WEB_ROOT', ROOT.'admin/www/');

def('HOST', 'https://admin.compbet.com');
def('JSHOST', 'admin.compbet.com');
def('BASEDOMAIN', 'admin.compbet.com'); //host, ktery nebude logovan jako referer
if(!defined('PROTOCOL')) define('PROTOCOL', 'https://'); //protocol stranek
def('WEBHOST', 'www.compbet.com/'); //host uzivatelskych stranek

if(!defined('SESMAX')) define('SESMAX',1800); //maxmimalni zivotnost session
if(!defined('ONLINEGAME')) define('ONLINEGAME',600); //maxmimalni doba po kterou se pocitaji hraci her za online hrace
if(!defined('PAGE')) define('PAGE',20); //Pocet polozek na stranku
if(!defined('GAMELIFE')) define('GAMELIFE',45); //Maximalni zivotnost hry bez provedene akce uvedeno v minutach
if(!defined('ADMINERRORMAIL')) define('ADMINERRORMAIL','info@compbet.com'); //mail na ktery se zasilaji chybove zpravy, ktere nastanou ve scriptech
if(!defined('BOOKMAIL')) define('BOOKMAIL','info@compbet.com'); //mail na bookmakery
if(!defined('EN_LANG_ID')) define('EN_LANG_ID',2); // ID anglickeho jazyka
if(!defined('CZ_LANG_ID')) define('CZ_LANG_ID',1); // ID ceskeho jazyka
if(!defined('ADMIN_VERSION')) define('ADMIN_VERSION','0.4/rev2415/16.1.2011');
if(!defined('ALIAS_DELIMITER')) define('ALIAS_DELIMITER', '/');

//Ciselniky
// team
if(!defined('CATALOG_TEAM_SECTION')) define('CATALOG_TEAM_SECTION', '/?section=266');
if(!defined('CATALOG_TEAM_WIDTH')) define('CATALOG_TEAM_WIDTH', '800');
if(!defined('CATALOG_TEAM_HEIGHT')) define('CATALOG_TEAM_HEIGHT', '600');
// combinations
if(!defined('CATALOG_COMB_SECTION')) define('CATALOG_COMB_SECTION', '/?section=269');
if(!defined('CATALOG_COMB_WIDTH')) define('CATALOG_COMB_WIDTH', '800');
if(!defined('CATALOG_COMB_HEIGHT')) define('CATALOG_COMB_HEIGHT', '600');

#Bet Radar config#
if(!defined('EXPORT_URL')) define('EXPORT_URL',"https://www.betradar.com/betradar/getXmlFeed.php?bookmakerName=compbet&key=do34lp56FT2&xmlFeedName=SourceJoin&deleteAfterTransfer=yes"); // url na betradaru pro zjisteni ids statistik a sparovani id sazek

#Max zivotnost sazek v sec. pak prevedeni do skladu#
if(!defined('BETLIFETIME')) define('BETLIFETIME',15552000); //pul roku
#if(!defined('BETLIFETIME')) define('BETLIFETIME',2592000); //mesic

#Kurzy a vyhernosti#
if(!defined('MIN_KURZ')) define('MIN_KURZ',1.00); //Minimalni kurz
if(!defined('MAX_KURZ')) define('MAX_KURZ',6000); //Maximalni kurz
if(!defined('MIN_WON')) define('MIN_WON',1.11); //Minimalni vyhernost
if(!defined('MAX_WON')) define('MAX_WON',1.11); //Maximalni vyhernost
if(!defined('RISK_LIMIT')) define('RISK_LIMIT',200000); //Risk limit

#XML RPC#
#adresar kam se ulozi obrazky#
define("XML_RPC_PATH","/xml_rpc_server_image_news.php");
#hostname serveru#
def('XML_RPC_HOST', 'www.compbet.com'); //host uzivatelskych stranek

#adresar kde jsou obrazky#
define("XML_RPC_IMAGEPATH","/images/gallery/");
#hostname serveru#
define("XML_RPC_PORT","443");
#prihlasovaci jmeno#
define("XML_RPC_NAME","vicimageuser");
#prihlasovaci heslo#
define("XML_RPC_PASS","asdas54wewe844ewdwee846qw6qwqww8ww");
#prihlasovaci jmeno zalozeni tiketu#
define("XML_RPC_NAME_TICKET_CREATE","ticketCreator");
#prihlasovaci heslo zalozeni tiketu#
define("XML_RPC_PASS_TICKET_CREATE","thisisthespecialpasswordforsomesuperman");
#adresar kde se vola sluzba#
define("XML_RPC_PATH_TICKET_CREATE","/webservice/");

#Templaty#
if(!defined('TPLHEAD')) define("TPLHEAD",WEB_ROOT."_tpl/head.tpl"); //template hlavicka
if(!defined('TPLLOG'))  define("TPLLOG",WEB_ROOT."_tpl/login.tpl"); //template login form
if(!defined('TPLFOOT')) define("TPLFOOT",WEB_ROOT."_tpl/foot.tpl"); //template paticka
if(!defined('TPLBODY')) define("TPLBODY",WEB_ROOT."_tpl/page.tpl"); //template telo
if(!defined('HTMLHEAD')) define("HTMLHEAD",'
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta name="keywords" content="on-line casino, games, offshore, secure, legal, chance, licensed, regulated, on-line," />
 <meta name="description" content="Play on-line games for real money or for free. " />
 <meta name="abstract" content="" />
 <meta name="robots" content=\'index,follow\' />
 <meta name="googlebot" content=\'index,follow,snippet,archive\' />
 <meta name="Author" content="" />
 </head>
 <body style="font-family: Arial, sans-serif;font-size:12px;color: #333333; margin: 0px;padding: 0px;_height:100%;background: White;">'); //hlavicka dokumentu

if(!defined('HTMLFOOT')) define("HTMLFOOT","</body></html>"); //paticka dokumentu
if(!defined('TPLBODY')) define("TPLBODY",WEB_ROOT."_tpl/.tpl"); //template telo
if(!defined('TPLLIVE')) define("TPLLIVE",WEB_ROOT."_tpl/live.tpl"); //template live

$GLOBALS['STRICT_ERRORS_FILTER'] = array(
	'filterName' => 'RegExp',
	'filterNamespace' => 'It6_Log_Filter',
	'filterParams' => array(
		'pattern' => array(
			'/\bDB::.*should not be called statically/',
			'/\bSesClass::.*should not be called statically/',
			'/\bmain::.*should not be called statically/',
			'/\bPEAR::.*should not be called statically/',
			//'/^Only variables should be assigned by reference$/',
			//'/^Undefined index: /',
			//'/^Undefined offset: /',
			//'/^Undefined variable: /',
			'/^Directive \'magic_quotes_gpc\' is deprecated/',
		),
		'priority' => 1
	)
);

def('PHP_OUTPUT_ERROR_LEVEL', 1);

$GLOBALS['LOG_CONFIG'] = array(
	//"phpOutput" => array(
	//	'filterParams' => array(
	//		'filters' => array(
	//			$GLOBALS['STRICT_ERRORS_FILTER']
	//		)
	//	)
	//),
	"mainlog" => array(
		'writerParams' => array(
			'stream'   => ROOT.'errorlog/admin-%F.log'
		),
		'filterParams' => array(
			'filters' => array(
				$GLOBALS['STRICT_ERRORS_FILTER'],
			)
		)
	)
);

def('ACL_FACTORY_CLASS', 'It6_Acl_Factory_Admin');
def('LAYOUT_PATH', ROOT.'admin/application/views/layouts/');

if(!defined('DEFAULT_LANG')) define('DEFAULT_LANG', 1);

// minimal time interval between BR IDs synchronizations (in seconds, undefined/empty if no interval required)
// synchronization is initiated by pulling our XML export by Betradar
def('EXPORT_BR_ID_SYNC_INTERVAL', 600);