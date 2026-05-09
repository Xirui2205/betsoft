<?php
include_once(ROOT . 'common/config_util.inc.php');

define('RUNNING_FROM_CLI', 1);

set_include_path (
	'.' . PATH_SEPARATOR
	. get_include_path() . PATH_SEPARATOR
	. ROOT . 'confirmd/library' . PATH_SEPARATOR
	. ROOT . 'common/library/'. PATH_SEPARATOR
	. ROOT . 'betting-service/application/'
);

$LOG_CONFIG = array(
	"mainlog" => array(
		'writerParams' => array(
			'stream'   => ROOT.'errorlog/confirmd-%F.log'
		)
	)
);

def('ACL_FACTORY_CLASS', 'It6_Acl_Factory_Confirmd');
def('WS_WRAPPER', 'Direct'); // one of: XmlRpc Direct

def('SSL_CERT', ROOT . 'web/ssl/branch.1.1.devel.pem');
def('SSL_CERT_PASSWD', '');

if ( WS_WRAPPER == 'Direct' ) {
	set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'betting-service/library'
	);
}

def('SLEEP_TIME', 500000); // in microseconds