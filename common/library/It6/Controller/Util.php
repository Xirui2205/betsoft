<?php

class It6_Controller_Util {

/**
 * Sets "Expires" header of response
 * @param Zend_Controller_Response_Http $response
 * @param integer|NULL $timestamp UNIX timestamp, if NULL then current timestamp will be used
 */
public static function setResponseExpiration(Zend_Controller_Response_Http $response, $timestamp = null) {
	if (!isset($timestamp))
		$timestamp = time();
	$response->setHeader('Expires', gmdate(DATE_RFC822, $timestamp), true);
}

} // 