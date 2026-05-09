<?php

class It6_Validate_Town extends Zend_Validate_Regex {

	public function __construct($anonymous = null) {
		parent::__construct('/^[-a-z0-9_.,áéíóúůýžščřďťňě() ]+$/ui');
	}
}
