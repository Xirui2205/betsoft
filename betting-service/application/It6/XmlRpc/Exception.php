<?php

class It6_XmlRpc_Exception extends Exception {

	// exception codes - this should be central registry!
	const CODE_ACCESS_DENIED = 503;
	const CODE_WRONG_LOGIN_PASSWORD = 1;
	const CODE_DUPLICATED_ADMIN = 2;
	const CODE_BLOCKED_ADMIN = 3;
	const CODE_BRANCH_APP_VERSION_NOT_ALLOWED = 5000;

	public function __construct($message, $code = 0, $previous = null) {

		if (null != $previous ) {
			$message .= ":\n" . $previous->getMessage();
		}
		
		$backtrace = debug_backtrace();
		$message = '(' . $backtrace[1]['class'] . '::' . $backtrace[1]['function'] . ') ' . $message;
		parent::__construct($message, $code, $previous);
		It6_Log::err($this, It6_Log::TAG_WEBSERVICE);
	}
}