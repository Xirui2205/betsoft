<?php
include_once(ROOT . 'common/config_util.inc.php');

set_include_path (
	'.' . PATH_SEPARATOR .
	get_include_path() . PATH_SEPARATOR .
	ROOT . 'web/application/'. PATH_SEPARATOR .
	ROOT . 'web/library/' . PATH_SEPARATOR .
	ROOT . 'betting-service/application/'
);

//require_once('/It6/WS.php');
//def('WS_TYPE', It6_WS::DIRECT);

def('WS_TYPE', 'Direct');
def('DISPLAY_ERRORS', 0);

if (!defined('SESMAX')) define('SESMAX', 3600); //maximalni zivotnost session

//detaily uctu pro vklady
if (!defined('VIC_BANK_ACCOUNT')) define('VIC_BANK_ACCOUNT', '43-9315490287');
if (!defined('VIC_BANK_NAME')) define('VIC_BANK_NAME', 'Komerční banka, a.s. (pobočka Roudnice nad Labem)');
if (!defined('VIC_BANK_CODE')) define('VIC_BANK_CODE', '0100');
if (!defined('VIC_BANK_ADDRESS')) define('VIC_BANK_ADDRESS', 'Dr. Slavíka 1062, 413 01  Roudnice nad Labem');

require_once(ROOT.'common/library/It6/Log.php');
$GLOBALS['LOG_CONFIG'] = array(
	'mainlog' => array(
		'writerName' => 'Stream',
		'writerNamespace' => 'It6_Log_Writer',
		'writerParams' => array(
			'stream'   => ROOT.'errorlog/web-%F.log'
		),
	),
	'muzolog' => array(
		'writerName' => 'Stream',
		'writerNamespace' => 'It6_Log_Writer',
		'writerParams' => array('stream' => ROOT.'errorlog/muzo-%F.log'),
		'filterName' => 'Tag',
		'filterNamespace' => 'It6_Log_Filter',
		'filterParams' => array('tag' => It6_Log::TAG_MUZO_VERIFY),
	),
);

//TODO udelat blackhole na servru, jinak muzo z lokalu netestovat
$GLOBALS['MUZO_CONFIG'] = array(
	'defaultLang' => 'cs',
	'urlForRequest' => 'https://test.3dsecure.gpwebpay.com/rb/order.do',
	'urlForResponse' => array(
		'cs' => 'https://vicmuzo.compbet.com/response.php',
		'en' => 'https://vicmuzo.compbet.com/response.php',
		'sk' => 'https://vicmuzo.compbet.com/response.php',
	),
	'privateKeyFile' => ROOT.'common/ssl/muzo/victoria-tip.key',
	'privateKeyPassword' => 'eSp9TVnrXLru',
	'certificateFile' => ROOT.'common/ssl/muzo/muzo.signing_test.cer'
);

def('ACL_FACTORY_CLASS', 'It6_Acl_Factory_Web');

// used for WS over XMLRPC
def('WS_WRAPPER', 'Direct'); // one of: XmlRpc Direct
def('SSL_CERT', ROOT . 'web/ssl/branch.1.1.devel.pem');
def('SSL_CERT_PASSWD', '');

if ( WS_WRAPPER == 'Direct' ) {
	set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'betting-service/library'
	);
}

//Google analytics code
def('GOOGLE_ANALYTICS_CODE', null);
//Google ad conversion code
def('GOOGLE_AD_CONVERSION_REGISTRATION', null);
def('GOOGLE_AD_CONVERSION_DEPOSIT', null);
def('GOOGLE_AD_CONVERSION_BET_PLACED', null);
def('LAYOUT_PATH', ROOT.'web/application/views/layouts/');
def('GLOBAL_CACHE_ENABLED', 0);
def('ACL_CACHE_LIFETIME', 600); // 10 minutes
def('LIVESCORE_URL','http://www.livescore.scoreradar.com/?alias=compbet');
def('BETRADAR_STATS_URL','http://stats.betradar.com/s4/?clientid=80');
def('LIVEZILLA_ENABLED', 0);
def('MINIFY_PREFIX', '');
def('ALLOW_WEB_CANCEL_TICKET', 0);