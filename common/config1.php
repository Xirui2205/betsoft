<?php
##############################
#PHP 5.x + MySQL Database 5.0
#Cookie enable
#PEAR:DB
##############################

include_once(ROOT . 'common/version.php');

#IP ktere se nemaji pocitat do shody#
$ipd = array('"62.77.88.215"');

##!!!!!! TOHLE PAK DAT NA OSTREM PRYC##
$_SERVER["HTTPS"] = 'on';


if(!defined("ROOT")) define("ROOT", dirname( dirname(__FILE__) ) . '/');

define('TEMPLATES_DIRECTORY', ROOT."files/templates/");

include_once(ROOT . 'common/config_util.inc.php');

//TODO: fill in mail info

if(!defined('SMTP_ADDRESS')) define('SMTP_ADDRESS','tmp_mail.wtnet.cz');

if(!defined('SMTP_LOGIN_TYPE')) define('SMTP_LOGIN_TYPE','login');
if(!defined('SMTP_USERNAME'))
define('SMTP_USERNAME','info@compbet.com');
if(!defined('SMTP_PASSWORD')) define('SMTP_PASSWORD','M4ilT3st');
//if(!defined('SMTP_AUTH')) define('SMTP_AUTH','crammd5'); //options: plain, login, crammd5
if(!defined('SMTP_PORT')) define('SMTP_PORT', 25);
if(!defined('SMTP_SSL')) define('SMTP_SSL','tls'); //if it is not needed, set it to empty value


def('WEB_SERVICE_URL', array(
	'UNIT_TESTING' => 'https://compbet.com',
	'DEVEL_LOCAL'  => 'http://svc.testbook.cz',
	'DEVEL'        => 'https://svc.compbet.com',
	'BBAS_TESTING' => 'https://svc.test.compbet.cz',
	'BBAS_PRODUCTION' => 'https://svc.compbet.com',
	'BBAS_BETA' => 'https://svc-compbet.com',
));
include_once('ws-security.inc.php');

def('WEBHOST', array(
	'UNIT_TESTING' => 'www.testbook.cz',
	'DEVEL_LOCAL'  => 'www.slevauto.cz',
	'DEVEL'        => 'www.compbet.com',
	'BBAS_TESTING' => 'www.test.compbet.com', // www-compbet.com
	'BBAS_PRODUCTION' => 'www.compbet.com',
	'BBAS_BETA' => 'www-test2.compbet.com',
));

def('WEBHOSTL3', array(
	'UNIT_TESTING' => 'www.testbook.cz',
	'DEVEL_LOCAL'  => 'slevauto.cz',
	'DEVEL'        => 'www.compbet.com',
	'BBAS_TESTING' => 'www-test2-compbet.com',
	'BBAS_PRODUCTION' => 'www-compbet.com',
	'BBAS_BETA' => 'www-test2-compbet.com',
));

def('BASEDOMAIN', array(
	'UNIT_TESTING' => 'www.testbook.cz',
	'DEVEL_LOCAL'  => 'bewa.compbet.com',
	'DEVEL'        => 'www.compbet.com',
	'BBAS_TESTING' => 'www.test.compbet.com', // www-test2.compbet.com
	'BBAS_PRODUCTION' => 'www.compbet.com',
	'BBAS_BETA' => 'www-test2.compbet.com',
));

def('ADMINHOST', array(
	'UNIT_TESTING' => 'admin.testbook.cz',
	'DEVEL_LOCAL'  => 'bewa.compbet.com',
	'DEVEL'        => 'admin.compbet.com',
	'BBAS_TESTING' => 'admin.test.compbet.com',
	'BBAS_PRODUCTION' => 'admin.compbet.com',
	'BBAS_BETA' => 'admin-test2.compbet.com',
));

def('MAILHOST', array(
	'UNIT_TESTING' => 'testbook.cz',
	'DEVEL_LOCAL'  => 'slevauto.cz',
	'DEVEL'        => 'compbet.com',
	'BBAS_TESTING' => 'admin.test.compbet.com',
	'BBAS_PRODUCTION' => 'compbet.com',
	'BBAS_BETA' => 'compbet.com',
));

def('BETADAR_DEBUG', array(
	'UNIT_TESTING' => true,
	'DEVEL_LOCAL'  => true,
	'DEVEL'        => false,
	'BBAS_TESTING' => false,
	'BBAS_PRODUCTION' => false,
	'BBAS_BETA' => false,
));

def('LIVEZILLA_URI', array(
	'UNIT_TESTING' => 'https://www-compbet.com/livezilla/chat.php',
	'DEVEL_LOCAL'  => 'https://www.slevaauto.cz/livezilla/chat.php',
	'DEVEL'        => 'https://www-compbet.com/livezilla/chat.php',
	'BBAS_TESTING' => 'https://www-test2-compbet.com/livezilla/chat.php',
	'BBAS_PRODUCTION' => 'https://www-compbet.com/livezilla/chat.php',
	'BBAS_BETA' => 'https://www-test2.compbet.com/livezilla/chat.php',
));

def('LIVEBETTING_URI', array(
	'UNIT_TESTING' => 'https://livetest.compbet.com',
	'DEVEL_LOCAL'  => 'https://livetest.compbet.com',
	'DEVEL'        => 'https://livetest.compbet.com',
	'BBAS_TESTING' => 'https://livetest.compbet.com',
	'BBAS_PRODUCTION' => 'https://live.compbet.com',
	'BBAS_BETA' => 'https://livetest.compbet.com',
));
def('LIVEBETTING_CID', 587469874);

if(!defined('MAIL_FROM_ADDRESS')) define('MAIL_FROM_ADDRESS','sender@'.MAILHOST);
if(!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME','CompBet');
if(!defined('MAIL_SUBJECT_PREFIX')) define('MAIL_SUBJECT_PREFIX','[CompBet]');
if(!defined('REPLY_TO_ADDRESS')) define('REPLY_TO_ADDRESS','replyTo@'.MAILHOST);
if(!defined('REPLY_TO_NAME')) define('REPLY_TO_NAME','fillme');

if(!defined('NOREPLY')) define('NOREPLY','VIC <no-reply.vic@compbet.com>');
if(!defined('INFOMAIL')) define('INFOMAIL','info@compbet.com');
if(!defined('ADMINMAIL')) define('ADMINMAIL','admin@compbet.com');
if(!defined('BOOKMAIL')) define('BOOKMAIL','bookmakers@compbet.com');

def('SUBJECT_PREFIX', array(
	'DEVEL_LOCAL' => 'devel-local: ',
	'DEVEL' => 'devel: ',
	'BBAS_TESTING' => 'testing: ',
	'BBAS_PRODUCTION' => 'production: ',
	'BBAS_BETA' => 'beta: ')
);

if(!defined('INC_DIR')) define('INC_DIR','../include/');
if(!defined('FMW_DIR')) define('FMW_DIR','');
if(!defined('FRAMEWORK')) define('FRAMEWORK','../../include/common.php');
if(!defined('FILES_DIR')) define('FILES_DIR', ROOT . 'files/');

if(!defined('GALERY_PATH')) define('GALERY_PATH', '/images/gallery/');  //obrazky z galerie

if(!defined('RC4CRYPTKEY'))     define('RC4CRYPTKEY',"J25etb89lc89omPl32dr39mf322rf33un2c8asi922no03and32b239t");

// adresar pro ukladani logu
if(!defined('LOG')) define('LOG',ROOT.'tmp/log/');

if(!defined('MENU_CACHE_LIFETIME'))    define('MENU_CACHE_LIFETIME',0);  //Menu cache life time
if(!defined('PROMO_CACHE_LIFETIME'))    define('PROMO_CACHE_LIFETIME',30);  //Promo cache life time
if(!defined('WEEKWINNER_CACHE_LIFETIME'))    define('WEEKWINNER_CACHE_LIFETIME',6200);  //Week winner cache life time

if(!defined('MARKETS_CACHE_LIFETIME')) define('MARKETS_CACHE_LIFETIME',120);  //Markets cache life time

if(!defined('PROTOCOL')) define('PROTOCOL',"http://"); //Protokol pro prihlaseni (vcetne "://")
//if (empty($_SERVER['HTTPS']) && !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' != $_SERVER['HTTP_X_FORWARDED_PROTO']) {
//	if(!defined('PROTOCOL')) define('PROTOCOL',"http://");
//} else {
//	if(!defined('PROTOCOL')) define('PROTOCOL',"https://");
//}

$GLOBALS['EXCLUDEUSER'] =  array(0); //pole user id ktere se nemaji pocitat

if(!defined('LIVE_LACK')) define('LIVE_LACK',20); //Prodleva kontroly aktualizace live sazek mezi zadanim zmeny adminem a viditelnosti zmen pro uzivatele

if(!defined('CACHE_DIRECTORY')) define('CACHE_DIRECTORY',ROOT.'tmp/');
def('ZEND_CACHE_FILE_HASHDIR_UMASK', 0770);
def('ZEND_CACHE_FILE_UMASK', 0660);
if (!isset($GLOBALS['ZEND_CACHE_BACKEND_OPTIONS'])) {
	$GLOBALS['ZEND_CACHE_BACKEND_OPTIONS'] = array(
		'cache_dir' => CACHE_DIRECTORY,
		'hashed_directory_umask' => ZEND_CACHE_FILE_HASHDIR_UMASK,
		'cache_file_umask' => ZEND_CACHE_FILE_UMASK,
	);
}

//if(PROTOCOL == "http://") $_SERVER["HTTPS"] = 'on'; // TODO na ostrem dat pryc

// moznost predefinovat konstanty pro vyvoj
if(file_exists('./config_local.php')) require_once 'config_local.php';
//if(file_exists(INC_DIR.'config_local.php')) require_once INC_DIR.'config_local.php';

// oddelena definice konstant
require_once 'constants.php';

if(!defined('PROVE_SEC'))    define('PROVE_SEC',90);  //Maximalni doba v sec pro blokovani tiketu pri zakladani

if(!defined('RC4CRYPTKEY'))     define('RC4CRYPTKEY',"J25etb89lc89omPl32dr39mf322rf33un2c8asi922no03and32b239t");

if(!defined("ROOTCAPTCHA")) define("ROOTCAPTCHA",ROOT."web/www/captcha/");

// zakladni adresa stranek - pozor, musi byt https !!!

if(!defined("DEFAULT_HOST")) {
  if(isset($_SERVER["HTTP_X_FORWARDED_HOST"])) define("DEFAULT_HOST", PROTOCOL . $_SERVER["HTTP_X_FORWARDED_HOST"]);
  else if(isset($_SERVER["HTTP_HOST"])) define("DEFAULT_HOST", PROTOCOL . $_SERVER["HTTP_HOST"]);
  else define("DEFAULT_HOST", PROTOCOL. 'localhost');
}

// soubor pro logovani tridou Logger
if(!defined('LOGFILE')) define('LOGFILE','php://output');


#Udaji pro databazi
if(!defined('GDATABASE'))   define('GDATABASE','mysqli');    //Database type
if(!defined('GMY_DB'))      define('GMY_DB','vic_main');   //DATABASE
if(!defined('GMY_PORT'))    define('GMY_PORT','3306');          //PORT

def('GMY_HOST', array(
	'UNIT_TESTING' => 'localhost',
	'DEVEL_LOCAL'  => 'localhost',
	'DEVEL'        => 'localhost',
	'BBAS_TESTING' => 'localhost',
	'BBAS_PRODUCTION' => 'localhost',
	'BBAS_BETA' => 'localhost',
)); //HOST

def('GMY_USER', array(
	'UNIT_TESTING' => 'compbet',
	'DEVEL_LOCAL'  => 'root',
	'DEVEL'        => 'kuchar_adm',
	'BBAS_TESTING' => 'kuchar_adm',
	'BBAS_PRODUCTION' => 'kuchar_adm',
	'BBAS_BETA' => 'CompBet',
));      //USER

def('GMY_PASS', array(
	'UNIT_TESTING' => 'vicvic88',
	'DEVEL_LOCAL'  => 'tomasKucharaaa',
	'DEVEL'        => 'ToibeufeeB0n',
	'BBAS_TESTING' => 'ToibeufeeB0n',
	'BBAS_PRODUCTION' => 'ToibeufeeB0n',
	'BBAS_BETA' => 'sX0IzIJf6N2',
));          //PASSWORD

#udaje pro spojeni s db s procedurama (Bendy: Morisovi testy ticketu)
/*if(!defined('GAPDATABASE'))   define('GAPDATABASE','mysqli');    //Database type
if(!defined('GAPMY_DB'))      define('GAPMY_DB',"vic_procedure");   //DATABASE
if(!defined('GAPMY_HOST'))    define('GAPMY_HOST','127.0.0.1'); //HOST
if(!defined('GAPMY_PORT'))    define('GAPMY_PORT','');          //PORT
if(!defined('GAPMY_USER'))    define('GAPMY_USER',"root");      //USER
if(!defined('GAPMY_PASS'))    define('GAPMY_PASS',"sumperk");          //PASSWORD
*/
#Udaji pro databazi session
if(!defined('SESDATABASE'))   define('SESDATABASE','mysqli');     //Database type
if(!defined('SESMY_DB'))      define('SESMY_DB','vic_session');   //DATABASE
if(!defined('SESMY_PORT'))    define('SESMY_PORT','3306');        //PORT

def('SESMY_HOST', array(
	'UNIT_TESTING' => 'localhost',
	'DEVEL_LOCAL'  => 'localhost',
	'DEVEL'        => 'localhost',
	'BBAS_TESTING' => 'localhost',
	'BBAS_PRODUCTION' => 'localhost',
	'BBAS_BETA' => 'localhost',
)); //HOST

def('SESMY_USER', array(
	'UNIT_TESTING' => 'compbet',
	'DEVEL_LOCAL'  => 'root',
	'DEVEL'        => 'kuchar_adm',
	'BBAS_TESTING' => 'kuchar_adm',
	'BBAS_PRODUCTION' => 'compbet',
	'BBAS_BETA' => 'CompBet',
));      //USER

def('SESMY_PASS', array(
	'UNIT_TESTING' => 'vicvic88',
	'DEVEL_LOCAL'  => 'tomasKucharaaa',
	'DEVEL'        => 'ToibeufeeB0n',
	'BBAS_TESTING' => 'ToibeufeeB0n', // sX0IzIJf6bN2
	'BBAS_PRODUCTION' => 'ToibeufeeB0n',
	'BBAS_BETA' => 'sX0IzIJf6bN2',
));          //PASSWORD

#Udaji pro databazi admin
if(!defined('DATABASE'))   define('DATABASE','mysqli');       //Database type
if(!defined('MY_DB'))      define('MY_DB','vic_admin');       //DATABASE
if(!defined('MY_PORT'))    define('MY_PORT','');              //PORT

def('MY_HOST', array(
	'UNIT_TESTING' => 'localhost',
	'DEVEL_LOCAL'  => 'localhost',
	'DEVEL'        => 'localhost',
	'BBAS_TESTING' => 'localhost',
	'BBAS_PRODUCTION' => '193.85.234.246',
	'BBAS_BETA' => 'localhost',
)); //HOST

def('MY_USER', array(
	'UNIT_TESTING' => 'compbet',
	'DEVEL_LOCAL'  => 'root',
	'DEVEL'        => 'kuchar_adm',
	'BBAS_TESTING' => 'kuchar_adm',
	'BBAS_PRODUCTION' => 'kuchar_adm',
	'BBAS_BETA' => 'CompBe',
));      //USER

def('MY_PASS', array(
	'UNIT_TESTING' => 'vicvic88',
	'DEVEL_LOCAL'  => 'tomasKucharaaa',
	'DEVEL'        => 'ToibeufeeB0n',
	'BBAS_TESTING' => 'ToibeufeeB0n', // sX0IzIJf6bN2
	'BBAS_PRODUCTION' => 'ToibeufeeB0n',
	'BBAS_BETA' => 'sX0IzIJf6bN2',
));          //PASSWORD

#Udaji pro databazi betwarehouse
if(!defined('WDATABASE'))   define('WDATABASE','mysqli');     //Database type
if(!defined('WMY_DB'))      define('WMY_DB','vic_warehouse');  //DATABASE
if(!defined('WMY_PORT'))    define('WMY_PORT','');            //PORT

def('WMY_HOST', array(
	'UNIT_TESTING' => 'localhost',
	'DEVEL_LOCAL'  => 'localhost',
	'DEVEL'        => 'localhost',
	'BBAS_TESTING' => 'localhost',
	'BBAS_PRODUCTION' => '193.85.234.246',
	'BBAS_BETA' => 'localhost',
)); //HOST

def('WMY_USER', array(
	'UNIT_TESTING' => 'compbet',
	'DEVEL_LOCAL'  => 'root',
	'DEVEL'        => 'kuchar_adm',
	'BBAS_TESTING' => 'kuchar_adm',
	'BBAS_PRODUCTION' => 'kuchar_adm',
	'BBAS_BETA' => 'CompBet',
));      //USER

def('WMY_PASS', array(
	'UNIT_TESTING' => 'vicvic88',
	'DEVEL_LOCAL'  => 'root',
	'DEVEL'        => 'ToibeufeeB0n',
	'BBAS_TESTING' => 'ToibeufeeB0n', // sX0IzIJf6bN2
	'BBAS_PRODUCTION' => 'ToibeufeeB0n',
	'BBAS_BETA' => 'ToibeufeeB0n',
));          //PASSWORD

if(!defined('MONGODB_LOG_DB'))      define('MONGODB_LOG_DB','vic_log');  //DATABASE
if(!defined('MONGODB_LOG_PORT'))    define('MONGODB_LOG_PORT','27017');            //PORT

def('MONGODB_LOG_HOST', array(
	'UNIT_TESTING' => 'localhost',
	'DEVEL_LOCAL'  => 'localhost',
	'DEVEL'        => 'localhost',
	'BBAS_TESTING' => 'localhost',
	'BBAS_PRODUCTION' => 'localhost',
));



# IP ADRESY #
if(!defined('IP_SHOP')) define('IP_SHOP','62.77.88.213');// TEST



#Debug zapnout vypnout#
if(!defined('DEBUG'))  define('DEBUG',1);

// ??? co je toto ???
   $systemAr = array();
   $systemAr[2] = Array(); $systemAr[2][1] = 2; $systemAr[2][2] = 1;
   $systemAr[3] = Array(); $systemAr[3][1] = 3; $systemAr[3][2] = 3;$systemAr[3][3] = 1;
   $systemAr[4] = Array(); $systemAr[4][1] = 4;$systemAr[4][2] = 6;$systemAr[4][3] = 4;$systemAr[4][4] = 1;
   $systemAr[5] = Array(); $systemAr[5][1] = 5;$systemAr[5][2] = 10;$systemAr[5][3] = 10;$systemAr[5][4] = 5;$systemAr[5][5] = 1;
   $systemAr[6] = Array(); $systemAr[6][1] = 6;$systemAr[6][2] = 15;$systemAr[6][3] = 20;$systemAr[6][4] = 15;$systemAr[6][5] = 6;$systemAr[6][6] = 1;
   $systemAr[7] = Array(); $systemAr[7][1] = 7;$systemAr[7][2] = 21;$systemAr[7][3] = 35;$systemAr[7][4] = 35;$systemAr[7][5] = 21;$systemAr[7][6] = 7;$systemAr[7][7] = 1;
   $systemAr[8] = Array(); $systemAr[8][1] = 8;$systemAr[8][2] = 28;$systemAr[8][3] = 56;$systemAr[8][4] = 70;$systemAr[8][5] = 56;$systemAr[8][6] = 28;$systemAr[8][7] = 8;$systemAr[8][8] = 1;
   $systemAr[9] = Array(); $systemAr[9][1] = 9;$systemAr[9][2] = 36;$systemAr[9][3] = 84;$systemAr[9][4] = 126;$systemAr[9][5] = 126;$systemAr[9][6] = 84;$systemAr[9][7] = 36;$systemAr[9][8] = 9;$systemAr[9][9] = 1;
   $systemAr[10] = Array(); $systemAr[10][1] = 10;$systemAr[10][2] = 45;$systemAr[10][3] = 120;$systemAr[10][4] = 210;$systemAr[10][5] = 252;$systemAr[10][6] = 210;$systemAr[10][7] = 120;$systemAr[10][8] = 45;$systemAr[10][9] = 10;$systemAr[10][10] = 1;


#Dalsi nastaveni#
if(!defined('WEB_ROOT'))   define('WEB_ROOT',ROOT.'www/');       //adresar

#include path pro xml import/export#
if(!defined('XML_IMPORT_PATH')) define('XML_IMPORT_PATH',ROOT."admin/include/:".ROOT.":/usr/local/lib/php/");

#XML RPC#
#server pro spracovani pozadavku#
define("XML_RPC_SERVER","/xml_rpc_server.php");
#hostname serveru#
define("XML_RPC_SERVER_HOST","admin.testbook.cz");

def('XML_RPC_SERVER_HOST', array(
	'DEVEL_LOCAL'  => 'admin.slevauto.cz',
	'DEVEL'        => 'admin.testbook.compbet.com',
	'BBAS_TESTING' => 'admin-test2.compbet.com',
	'BBAS_PRODUCTION' => 'admin.compbet.com',
	'BBAS_BETA' => 'admin-test2.compbet.com',
));

#Skryte heslo pro debug stranek#
if(!defined('SECREDPASS')) define("SECREDPASS","totojiznadaleneniskryteheslo");

#Live sazky ajax refresh
if(!defined('LIVEBET_MATCH_REFRESH_INFO')) define('LIVEBET_MATCH_REFRESH_INFO',12000);
if(!defined('LIVEBET_MATCH_REFRESH_ODDS')) define('LIVEBET_MATCH_REFRESH_ODDS',7000);
if(!defined('LIVEBET_LIST_REFRESH_ONLINE')) define('LIVEBET_LIST_REFRESH_ONLINE',30000);
if(!defined('LIVEBET_LIST_REFRESH_COMING')) define('LIVEBET_LIST_REFRESH_COMING',60000);
if(!defined('LIVECALENDAR_REFRESH_LIST')) define('LIVECALENDAR_REFRESH_LIST',60000);

#RSS
if(!defined('FEED_TITLE')) define('FEED_TITLE', 'CompBet RSS feed');
if(!defined('FEED_AUTHOR')) define('FEED_AUTHOR', 'CompBet');
if(!defined('FEED_CHARSET')) define('FEED_CHARSET', 'utf-8');

#logs
if(!defined('MAIN_LOG_ERROR_LEVEL'))
	define('MAIN_LOG_ERROR_LEVEL', 8); //Zend_Log::DEBUG = 8

if(!defined('PHP_OUTPUT_ERROR_LEVEL'))
	define('PHP_OUTPUT_ERROR_LEVEL', 4); //4 = Zend_Log::WARN

def('DISPLAY_ERRORS', array(
	'DEVEL_LOCAL'  => 1,
	'DEVEL'        => 1,
	'BBAS_TESTING' => 0,
	'BBAS_PRODUCTION' => 0,
	'BBAS_BETA' => 1,
)); // Should be errors visible to user? Surely NOT for production environment.

$GLOBALS['COMMON_LOG_CONFIG'] = array(
	"dbLog" => array(
		//'writerName'      => 'Db',
		'writerName'      => 'MongoDb',
		'writerNamespace' => 'It6_Log_Writer',
		'writerParams'    => array(
			//'db' => 'admindb|zdb_admin',
			'db' => 'logdb',
			'table' => 'log',
			'columnMap' => array(
				'time'      => 'timestamp',
				'ip'        => 'ip',
				'admin_id'  => 'admin',
				'host_id'   => 'host',
				'user_id'   => 'user',
				'bet_id'    => 'bet',
				'ticket_id' => 'ticket',
				'coupon_id' => 'coupon',
				'tag'       => 'tag',
				'priority'  => 'priority',
				'message'   => 'message',
				'args'      => 'args',
				'exception' => 'exception',
				'file'      => 'file',
				'line'      => 'line'))),
	"mainlog" => array(
		'writerName'   => 'Stream',
		'writerNamespace'   => 'It6_Log_Writer',
		'writerParams' => array(
			'stream'   => ROOT.'errorlog/%F.log',
			'format' => '%timestamp% %ip% %admin% %host% %user% %tag% %priorityName% (%priority%) %message% %args% %exception% %file% (%line%)' . PHP_EOL),
		'filterName'   => 'Container',
		'filterNamespace' => 'It6_Log_Filter',
		'filterParams' => array(
			'filters' => array(
				array(
					'filterName' => 'Priority',
					'filterParams' => array('priority' => MAIN_LOG_ERROR_LEVEL)
				),
			)
		)
	)
);

if (DISPLAY_ERRORS) {
	$GLOBALS['COMMON_LOG_CONFIG']['phpOutput'] = array(
		'writerName'   => 'Stream',
		'writerNamespace'   => 'It6_Log_Writer',
		'writerParams' => array(
			'stream'   => 'php://output',
			'format' => '%priorityName%: %message% %args% %exception% %file% (%line%)<br/>' . PHP_EOL),
		'filterName'   => 'Container',
		'filterNamespace' => 'It6_Log_Filter',
		'filterParams' => array(
			'filters' => array(
				array(
					'filterName' => 'Priority',
					'filterParams' => array('priority' => PHP_OUTPUT_ERROR_LEVEL)
				),
			)
		)
	);
}

if ( empty($GLOBALS['LOG_CONFIG']) )
	$GLOBALS['LOG_CONFIG'] = $GLOBALS['COMMON_LOG_CONFIG'];
else
	$GLOBALS['LOG_CONFIG'] = array_replace_recursive($GLOBALS['COMMON_LOG_CONFIG'], $GLOBALS['LOG_CONFIG']);

if (!DISPLAY_ERRORS) {
	unset($GLOBALS['LOG_CONFIG']['phpOutput']);
}

def('ROUND_STAKES_ALWAYS_AS_CASH', true);
//def('SS_USER_DEPOSIT_BANK', '10111213'); // Specific Symbol for transaction type user.deposit.bank (without leading zeros)
                                           // if undefined no check is performed (empty value is required if defined)

def('DIR_CACHE_HOST_MESSAGES', ROOT . 'betting-service/www/host-messages/'); // directory with cached host messages (must end with slash)

def('JQUERY_URI', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'); // Google hosted CDN

def('JQUERY_UI_CSS_URI', 'http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css'); // Google hosted CDN

def('JQUERY_UI_URI', 'http://code.jquery.com/ui/1.10.0/jquery-ui.js'); // Google hosted CDN

def('FTPSYNC_HOSTS', array( // semicolon separated list of hosts (IPs or hostnames), hosts can contain port number separated by colon (eg. "127.0.0.1:21")
	'DEVEL_LOCAL' => '127.0.0.1',
	'BBAS_TESTING' => '127.0.0.1',
	'BBAS_PRODUCTION' => '127.0.0.1;127.0.0.1',
));
def('FTPSYNC_USER', 'ftpsync');
def('FTPSYNC_PASSWD', array(
	'DEVEL_LOCAL' => '_uBDxanboGWHB',
	'BBAS_TESTING' => '_ftrfadm.603',
	'BBAS_PRODUCTION' => '_heslo',
));

def('DAILY_SEQUENCE_OFFSET_BANK_EXPORT', 1000);

include_once(ROOT . 'common/node.inc.php');

def('BR_CREATE_BETS_SUSPENDED', array(
	'DEVEL_LOCAL' => 1,
	'BBAS_TESTING' => 1,
	'BBAS_PRODUCTION' => 1,
));

if(!defined("ISP_SMS_GATE_CERT")) define("ISP_SMS_GATE_CERT", ROOT .
"common/ssl/sms_gate/compbet.pem");

$langBrowser = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) : 'en';
define('LANG_BROWSER', $langBrowser);

define('BRANCHES_ENABLED', false);