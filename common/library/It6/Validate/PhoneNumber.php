<?php
class It6_Validate_PhoneNumber extends Zend_Validate_Regex {

	public function __construct() {
		//parent::__construct('/^((\+|00)\d{3} ?)?\d{9}$/');
		if (Zend_Controller_Front::getInstance()->getRequest()->getParam('areaCode') == 29) {
			parent::__construct('/^\d{9}$/');
		} else {
			parent::__construct('/^[0-9]{4,}$/');
		}
	}
}