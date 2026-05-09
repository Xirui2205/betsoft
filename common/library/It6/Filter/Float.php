<?php

class It6_Filter_Float extends It6_Filter_Abstract {

	private $form;



	public function __construct($value, $form) {
		$this->form = $form;
	}



	public function filter($value) {
		if($this->form->getAttrib(It6_Filter::ATTR_WEB2DB) == 'true'){
			$filteredValue = str_replace(',', '.', $value);
		}
		else
			$filteredValue = str_replace('.', ',', $value);

		return $filteredValue;
	}
	
	public static function commaToPoint($value) {
		$filteredValue = str_replace(',', '.', $value);
		
		return $filteredValue;
	}
}
