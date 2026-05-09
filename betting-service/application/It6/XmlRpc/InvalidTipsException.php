<?php

/**
 * This class purpose is only to wrap array of tips that caused error.
 * In many places in WS there is no way how to pass error data, only
 * throwing an exception is possible.
 */
class It6_XmlRpc_InvalidTipsException extends Exception {

private $tips = array();

public function __construct($tips, $message, $code = 0, $previous = null) {
	$this->tips = $tips;
	parent::__construct($message, $code, $previous);
}

public function getTips() {
	return $this->tips;
}

} // class