<?php
/**
 * node-ctl.php
 * @see It6_NodeComm
 * Script for external control of node's local resources.
 * Supported request parameters:
 * <ul>
 * <li>
 *    cmd ... command to be performed. Known commands:
 *            <ul>
 *            <li>PING : just returns "0 OK"</li>
 *            <li>INVALIDATE_CC : invalidate controller convert cache</li>
 *            <li>INVALIDATE_ACL : invalidate ACL cache</li>
 *            </ul>
 * </li>
 * </ul>
 * 
 * Response has form "N RESULT\n", where N is 0 for success, and non-zero error number
 * for failure, RESULT is specific to request and can be empty (giving response "N\n").
 * 
 * Error numbers (message texts are returned as RESULT):
 *    1 Unknown command
 *    2 Server internal error - unknown command callback
 *    3 Command failed 
 */

define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

include_once(ROOT . 'common/includes.inc.php');
include_once(ROOT . 'common/node.inc.php');
include_once(ROOT . 'common/config.php');
require_once('Zend/Loader.php');
require_once('Zend/Loader/Autoloader.php');

function __autoload($className) {
	if ('Zend_' == substr($className, 0, 5) || 'It6_' == substr($className, 0, 4)) {
		require_once(str_replace('_', '/', $className) . '.php');
	}
	else
		require_once("class.$className.php");
}

include_once(ROOT . 'common/init-global-cache.inc.php');

$knownCommands = array(
	It6_NodeComm::CMD_PING => array(
		'callback' => 'cmd_ping',
	),
	It6_NodeComm::CMD_IDENTIFY => array(
		'callback' => 'cmd_identify',
	),
	It6_NodeComm::CMD_I18N_REGENERATE => array(
		'callback' => 'cmd_i18n_regenerate',
	),
	It6_NodeComm::CMD_FLUSH_APC => array(
		'callback' => 'cmd_flush_apc',
	),
	It6_NodeComm::CMD_INVALIDATE_CC => array(
		'callback' => 'cmd_invalidate_cc',
	),
	It6_NodeComm::CMD_INVALIDATE_ACL => array(
		'callback' => 'cmd_invalidate_acl',
	),
);

$cmd = (empty($_REQUEST['cmd']) ? '' : $_REQUEST['cmd']);

if (empty($knownCommands[$cmd])) {
	send_result(1, 'Unknown command');
}
else if (empty($knownCommands[$cmd]['callback'])) {
	send_result(2, 'Server internal error - unknown command callback');
}
else {
	$data = call_user_func($knownCommands[$cmd]['callback']);
	$errorNumber = 0;
	$result = 'OK';
	if (!empty($data)) {
		if (isset($data['errorNumber'])) {
			$errorNumber = $data['errorNumber'];
			if (0 != $errorNumber) {
				$result = 'FAILED';
			}
		}
		$result = (isset($data['result']) ? $data['result'] : null);
	}
	send_result($errorNumber, $result);
}

function make_cmd_result($errorNumber, $result = null) {
	return array('errorNumber' => $errorNumber, 'result' => $result);
}

function send_result($errorNumber, $result = null) {
	header('Content-Type: text/plain; charset=utf-8');
	echo (empty($errorNumber) ? 0 : $errorNumber);
	echo (isset($result) ? " $result" : '');
	echo "\n";
}

function cmd_ping() {
}

function cmd_identify() {
	if (defined('NODE_NAME')) {
		return array('result' => NODE_NAME);
	}
	else {
		return array('errorNumber' => 3, 'result' => 'Undefined node name');
	}
}

function cmd_i18n_regenerate() {
	$dbPlugin = new Zend_Controller_Plugin_DbPLugin();
	$dbPlugin->connectDbMain();
	$result = It6_NodeComm::i18nRegenerate();
	return array('errorNumber' => ($result ? 0 : 3));	
}

function cmd_flush_apc() {
	It6_LocalCache::flush();
}

function cmd_invalidate_cc() {
	It6_Models_ControllerConvert::clearCache();
}

function cmd_invalidate_acl() {
	It6_Acl_Admin::invalidatePersistentCache();
}
