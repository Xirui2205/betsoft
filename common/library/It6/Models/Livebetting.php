<?php

class It6_Models_Livebetting {
	const PARAM_CID = 'cid';
	const PARAM_SESSION_ID = 'sesid';
	const PARAM_MATCH_ID = 'match_id';

	const PLACEHOLDER_SESSION_ID = '__LIVE_SESSION_ID__'; // to prevent replacement issues use only non-special chars for URLs and HTML

	public static function getSessionId() {
		return (empty($GLOBALS['ses_livebetting']) ? '' : $GLOBALS['ses_livebetting']);
	}

	public static function getUrl($matchId = null, $asHtml = false, $usePlaceholders = false) {
		$url = Zend_Uri_Http::fromString(LIVEBETTING_URI);
		$params = array(
			static::PARAM_CID => LIVEBETTING_CID, 
			static::PARAM_SESSION_ID => ($usePlaceholders ? self::PLACEHOLDER_SESSION_ID : self::getSessionId()),
		);
		if (!empty($matchId))
			$params[static::PARAM_MATCH_ID] = $matchId;
		$url->addReplaceQueryParameters($params);
		return ($asHtml ? htmlspecialchars((string)$url) : (string)$url);
	}

	public static function replaceSessionIdPlaceholders($subject, $sessionId = null) {
		if (!isset($sessionId))
			$sessionId = self::getSessionId();
		return str_replace(self::PLACEHOLDER_SESSION_ID, $sessionId, $subject);
	}
}
