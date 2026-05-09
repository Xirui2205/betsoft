<?php

//TODO: match SSL cert

define('ROOT', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
include(ROOT . 'common/includes.inc.php');
include(ROOT . 'common/config.php');
include(ROOT . 'common/init-global-cache.inc.php');

if (defined('CHECK_SSL_CERT') && CHECK_SSL_CERT) {
	$certOk = false;
	if (!empty($_SERVER['SSL_CLIENT_VERIFY']) && 'SUCCESS' == $_SERVER['SSL_CLIENT_VERIFY']) {
		if (defined('WS_SSL_TRUSTED_CA_DN')) {
			if ( in_array($_SERVER['SSL_CLIENT_I_DN'], explode("\0", WS_SSL_TRUSTED_CA_DN)) )
				$certOk = true;
		}
	}
	if (!$certOk) {
		header("HTTP/1.0 503 Forbidden");
		exit;
	}
}

require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();

if (1 != preg_match('!^/([^/]+)(/.*|)?$!', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $matches))
	die("Error");

$path = '/' . ltrim($matches[2], '/');

if (1 == preg_match('!^/output/(\d+)\.(pdf|txt)$!', $path, $matches)) {
	$file = ROOT . "offergen/output/{$matches[1]}.{$matches[2]}";
	if (file_exists($file)) {
		$mimeTypes = array('pdf' => 'application/pdf', 'txt' => 'text/plain');
		header('Content-Type: ' . $mimeTypes[$matches[2]]);
		header('Content-Length: ' . filesize($file));
		readfile($file);
	}
	else {
		header("HTTP/1.0 404 Not Found");
	}
}
else {
	if ( It6_GlobalCache::getKey(md5($_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_URI']))) {
		header("HTTP/1.0 503 Service unavailable");
	} else {
		It6_GlobalCache::setKey(md5($_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_URI']), 1, 14);
		$url = 'http://127.0.0.1:14445' . $path;
		$clientCfg = array(
			'maxredirects' => 0,
			'timeout' => 300,
		);
		$client = new Zend_Http_Client("$url?{$_SERVER['QUERY_STRING']}", $clientCfg);
		//$client->setParameterGet($_GET);
		$response = $client->request();
		foreach ($response->getHeaders() as $name => $value)
			header("$name: $value");
		echo $response->getRawBody();
	}
}