<?php

class NodeCommController extends It6_Controller_Abstract {

const SECTION_ID = 331;

const RESOURCENAME_TRANSLATE = 'general:translation';
const RESOURCENAME_MEMCACHE = 'general:memcache';
const RESOURCENAME_APC = 'general:apc';
const RESOURCENAME_CACHE_WEB_CONTENT = 'general:cache-web-content';
const RESOURCENAME_CACHE_ACL = 'general:cache-acl';

// command names must be safe for creating file name and for HTML
const CMD_PING = 'ping';
const CMD_IDENTIFY = 'identify';
const CMD_TRANS = 'trans';
const CMD_MEMC_FLUSH = 'memc-flush';
const CMD_APC_FLUSH = 'apc-flush';
const CMD_INVALIDATE_WEB_CONTENT = 'invalidate-wc';
const CMD_INVALIDATE_WEB_PAGE = 'invalidate-wp';
const CMD_INVALIDATE_ACL = 'invalidate-acl';

/**
 * Checks if all peers responded with OK code
 * @param struct $respone Map (peer IP => response struct from peer)
 */
private function checkResponse($response) {
	if (!is_array($response)) {
		return false;
	}
	foreach ($response as $peer => $data) {
		if (0 != $data['code']) {
			return false;
		}
	}
	return true;
}

/**
 * Helper method for creating nice message from response from one peer.
 * This implementation creates '(code) "formatted body"' messages.
 * @param struct $responses Map (peer IP => response struct)
 * @param Closure|string|NULL $fnFormatBody function(responseCode, responseBody) returning new body string,
 *                                          if NULL is passed then raw body is used,
 *                                          string can be on of predefined formats: 'OKFAIL' 
 */
private function formatPeerResponses($responses, $fnFormatBody = null) {
	foreach ($responses as &$response) {
		$body = null;
		if (isset($fnFormatBody)) {
			if ($fnFormatBody instanceof Closure) {
				$body = $fnFormatBody($response['code'], $response['body']);
			}
			else if ('OKFAIL' == $fnFormatBody) {
				$body = (0 == $response['code'] ? 'OK' : 'FAILED');
			}
			else { // defaults to raw body
				$body = $respone['body'];
			}
		}
		else {
			$body = $response['body'];
		}
		$response = "({$response['code']}) \"$body\"";
	}
	return $responses;
}

public function indexAction() {
	$acl = Zend_Registry::get('acl');
	$currentPrivileges = array(
		// current admin privileges
		'ping' => true, // given by acces to this section
		'translate' => $acl->isResourceAllowed(self::RESOURCENAME_TRANSLATE, 'update'),
		'memcached' => $acl->isResourceAllowed(self::RESOURCENAME_MEMCACHE, 'update'),
		'apc' => $acl->isResourceAllowed(self::RESOURCENAME_APC, 'update'),
		'cache-web-content' => $acl->isResourceAllowed(self::RESOURCENAME_CACHE_WEB_CONTENT, 'update'),
		'cache-acl' => $acl->isResourceAllowed(self::RESOURCENAME_CACHE_ACL, 'update'),
	);
	$commandRequiredPrivs = array(
		// command => one or list of keys from $privileges, empty when no privilege is required
	    // all appropriate value(s) in $currentPrivileges must be non-empty
	    // to have sufficient privileges for command
		self::CMD_PING => 'ping',
		self::CMD_IDENTIFY => 'ping',
		self::CMD_TRANS => 'translate',
		self::CMD_MEMC_FLUSH => 'memcached',
		self::CMD_APC_FLUSH => 'apc',
		self::CMD_INVALIDATE_WEB_CONTENT => 'cache-web-content',
		self::CMD_INVALIDATE_WEB_PAGE => 'cache-web-content',
		self::CMD_INVALIDATE_ACL => 'cache-acl',
	);
	
	// vymazat složku translate?
	$this->view->allDeleted = null;
	if (isset($_POST['deleteTranslateDir']) && $_POST['deleteTranslateDir']==1) {
		$files = glob('../../web/application/translate/*');
		$this->view->allDeleted = true;
		foreach($files as $file) {
			if(is_file($file)) {
				if (!unlink($file)) {
					$this->view->allDeleted = false;
				}
			}
		}
	}
	
	$commands = array();
	foreach ($commandRequiredPrivs as $cmd => $privs) {
		if (empty($privs)) {
			$access = true;
		}
		else if (is_array($privs)) {
			$access = true;
			foreach ($privs as $priv) {
				if (empty($currentPrivileges[$priv])) {
					$access = false;
					break;
				}
			}
		}
		else {
			$access = !empty($currentPrivileges[$privs]);
		}
		$commands[$cmd]['access'] = $access;
		$commands[$cmd]['viewScript'] = "node-comm/cmd-{$cmd}.phtml";
	}
	// initializations of data for forms etc.
	if ($commands[self::CMD_INVALIDATE_WEB_PAGE]['access']) {
		$commands[self::CMD_INVALIDATE_WEB_PAGE]['pages'] = Models_NodeComm::getAllWebPages();
	}
	// handling particullar command
	if ('POST' == $this->getRequest()->getMethod()) {
		$this->view->commandName = $cmd = $this->getRequest()->getParam('ncc');
		if (!empty($cmd)) {
			if (!empty($commands[$cmd]['access'])) {
				switch ($cmd) {
				case self::CMD_PING:
					$response = It6_NodeComm::execCommand(It6_NodeComm::CMD_PING);
					$commands[$cmd]['responseError'] = !$this->checkResponse($response);
					$commands[$cmd]['peerResponses'] = $this->formatPeerResponses($response, 'OKFAIL');
					break;
				case self::CMD_IDENTIFY:
					$response = It6_NodeComm::execCommand(It6_NodeComm::CMD_IDENTIFY);
					$commands[$cmd]['responseError'] = !$this->checkResponse($response);
					$commands[$cmd]['peerResponses'] = $this->formatPeerResponses($response);
					break;
				case self::CMD_TRANS:
					$localError = !It6_NodeComm::i18nRegenerate();
					$response = It6_NodeComm::execCommand(It6_NodeComm::CMD_I18N_REGENERATE);
					$commands[$cmd]['responseError'] = ($localError || !$this->checkResponse($response));
					$commands[$cmd]['peerResponses'] = array_merge(
						array('local' => ($localError ? 'FAILED' : 'OK')),
						$this->formatPeerResponses($response)
					);
					break;
				case self::CMD_MEMC_FLUSH:
					// no peer comunication needed
					$commands[$cmd]['responseError'] = !It6_GlobalCache::flush();
					break;
				case self::CMD_APC_FLUSH:
					$localError = !It6_LocalCache::flush();
					$response = It6_NodeComm::execCommand(It6_NodeComm::CMD_FLUSH_APC);
					$commands[$cmd]['responseError'] = ($localError || !$this->checkResponse($response));
					$commands[$cmd]['peerResponses'] = array_merge(
						array('local' => ($localError ? 'FAILED' : 'OK')),
						$this->formatPeerResponses($response, 'OKFAIL')
					);
					break;
				case self::CMD_INVALIDATE_WEB_CONTENT:
					$commands[$cmd]['responseError'] = false;
					It6_GlobalCache_Invalidator::invalidateAllWebContent();
					break;
				case self::CMD_INVALIDATE_WEB_PAGE:
					// no peer comunication needed
					$ccids = $this->getRequest()->getParam('ccids');
					$commands[$cmd]['responseError'] = false;
					$invalidatedCount = 0;
					if (!empty($ccids)) {
						It6_GlobalCache_Invalidator::invalidateWebPage($ccids);
						$invalidatedCount = count($ccids);
					}
					$commands[$cmd]['peerResponses'] = array(
						'local' => I18n::tr('nodecomm-cmd-invalidate-wp-count', $invalidatedCount)
					);
					break;
				case self::CMD_INVALIDATE_ACL:
					// ACL is in global cache, we do it from this node only
					$commands[$cmd]['responseError'] = false;
					It6_Acl_Admin::invalidatePersistentCache();
					break;
				default:
					$cmd = null;
				}
				if (!empty($cmd)) {
					$this->view->command = $commands[$cmd];
				}
			}
			else {
				$this->view->accessError = true;
			}
		}
	}
	$this->view->commands = $commands;
	
	// část Log
	$this->view->log = '';
	if (getAppEnv() == 'DEVEL_LOCAL') {
	$logMenu = '';
		// (jen pro devel local kvůli problémům na serveru: === 'DEVEL_LOCAL')
		$errorLogFiles = scandir('../../errorlog/');
		if (!empty($errorLogFiles)) foreach ($errorLogFiles as $fileName) {
			if (filemtime('../../errorlog/'.$fileName) >= strtotime("-12 hours")
					&& is_file('../../errorlog/'.$fileName)) {
				$logMenu .= "<a href='#log-$fileName' style='margin-right:5px'>$fileName</a>";
				$this->view->log .= "<h5 id='log-$fileName' style='margin:10px 0 5px 0'>$fileName</h5>";
				$file = file('../../errorlog/'.$fileName);
				$linesCount = count($file);
				if ($linesCount < 50) {
					for ($i = 0; $i < $linesCount; $i++) {
						$this->view->log .= $file[$i];
					}
				} else {
					for ($i = $linesCount - 50; $i < $linesCount; $i++) {
						$this->view->log .= $file[$i];
					}
				}
				$this->view->log = nl2br($this->view->log);
			}
		}
		$this->view->log = $logMenu.'<br>'.preg_replace('#(<br */?>\s*)+#i', '<br />', $this->view->log);
	}
	
}

} // class
