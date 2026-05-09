<?php

class It6_Validate_FirstName extends Zend_Validate_Regex {

	public function __construct() {
		parent::__construct('/^[-a-záéíóúůýžščřďťňě ]+$/ui');
	}
}
