<?php


if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');

include ROOT . 'admin/include/class/init.inc.php';
include "template/class.TemplatePower.inc.php";
/*
//NOTE: SSL certificate is needed for authentization but wouldn't be better other way for DIRECT mode?
$httpClient = null;
if (defined('SSL_CERT')) {
	$sslAdapter = new Zend_Http_Client_Adapter_Socket();
	$sslAdapter->setStreamContext(array(
		'ssl' => array('local_cert' => SSL_CERT, 'passphrase' => SSL_CERT_PASSWD)
	));
	$httpClient = new Zend_Http_Client();
	$httpClient->setAdapter($sslAdapter);
}
$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL, $httpClient);
$client->setSkipSystemLookup(true);
Zend_Registry::set('ws',new It6_WS(It6_WS::XML_RPC, $client));
//Zend_Registry::set('ws',new It6_WS(It6_WS::DIRECT, $client));
*/

//END BOOTSTRAP STUFF



try{
   $ob = new Live();
}
catch(exHandler $e){

   $e->produceAllError();

}

?>
