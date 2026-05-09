<?php

class It6_Validate_LastName extends Zend_Validate_Regex {

	public function __construct() {
		parent::__construct('/^[-a-záéíóúůýžščřďťňě ]+$/ui');
	}
}
