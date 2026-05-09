<?php

class It6_NodeComm {

const CMD_PING = 'PING';
const CMD_IDENTIFY = 'IDENTIFY';
const CMD_I18N_REGENERATE = 'I18N_REGENERATE';
const CMD_FLUSH_APC = 'FLUSH_APC'; 
const CMD_INVALIDATE_CC = 'INVALIDATE_CC';
const CMD_INVALIDATE_ACL = 'INVALIDATE_ACL';

/**
 * Makes nodecomm HTTP request(s) to another nodes.
 * List of other nodes is given in ROOT/common/node.inc.php
 * by NODECOMM_PEERS constant from config.
 * @param string $cmd see CMD_* class constants
 * @param array $additionalParams Optional associative array with command specific and other parameters
 * @return struct Map (peer IP => {code: responseCode, body: response}), responseCode can be NULL for unknown response or FALSE for execution error
 */
public static function execCommand($cmd, $additionalParams = null) {
	if (!defined('NODECOMM_PEERS'))
		return array();
	$peers = explode(';', NODECOMM_PEERS);
	if (empty($peers))
		return array();
	$result = array();
	foreach ($peers as $peer) {
		$peer = trim($peer);
		if (!empty($peer)) {
			$result[$peer] = static::_makeRequest($peer, $cmd, $additionalParams);
		}
	}
	return $result;
}

/**
 * Sends command to another node and processes the response.
 * @param string $peer Host part of target URI
 * @param string $cmd
 * @param array $additionalParams
 * @return struct Fields:
 *                <ul>
 *                <li>integer|NULL|boolean code ...  NULL for unknown response or FALSE for execution error</li>
 *                <li>string body ... can be empty, command specific</li>
 *                </ul>
 */
protected static function _makeRequest($peer, $cmd, $additionalParams = null) {
	$client = new Zend_Http_Client("http://$peer/node-ctl.php");
	if (!empty($additionalParams)) {
		foreach ($additionalParams as $name => $value) {
			$client->setParameterGet($name, $value);
		}
	}
	$client->setParameterGet('cmd', $cmd);
	$code = null;
	$text = '';
	try {
		$response = $client->request();
		if (200 == $response->getStatus()) {
			$code = false;
		}
		$text = $response->getBody();
		if (preg_match('/^(\\d+)( .*|$)/', $text, $matches)) {
			$code = intval($matches[1]);
			$text = substr($matches[2], 1);
		}
		else {
			$code = null;
		}
	}
	catch (Exception $e) {
		$code = false;
	}
	return array('code' => $code, 'body' => $text);
}

/**
 * Recreates dictionary files, saves output into log file nodecomm-translate.log.
 * Expects initialized main database in 'db' Zend_Registry entry.
 * @return boolean TRUE on success, FALSE otherwise. 
 */
public static function i18nRegenerate() {
	try {
		ob_start();
		error_reporting(E_ALL);
		
		echo strftime('%Y-%m-%d %H:%M:%S ') . NODE_NAME . " PROCESSING...\n";
		
		$trans = new It6_Translate_Web(null, null, null, null);
		$trans->checkReported();
		$trans->generateDictionaryFiles();

		echo "FINISHED.\n"; 

		file_put_contents(ROOT . 'errorlog/nodecomm-translate.log', ob_get_clean());
		return true;
	}
	catch (Exception $e) {
		return false;
	}
}

} // class
