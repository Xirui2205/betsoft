<?php

class It6_Validate_BetTypeName extends Zend_Validate_Regex {

	public function __construct($anonymous = null) {
		parent::__construct('/^[-a-z0-9_.]+$/ui');
	}
}
