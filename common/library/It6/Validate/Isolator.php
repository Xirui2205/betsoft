<?php
/**
 * Isolates the passed validator from the owner object (ie form, form_element)
 */
class It6_Validate_Isolator implements Zend_Validate_Interface{

	private $validator;


	public function __construct($validator, $validatorOptions=null) {
		if(!is_object($validator))
			$validator = new $validator($validatorOptions);
		
		$this->validator = $validator;
		return $this->validator;
	}



	public function isValid($value) {
		return $this->validator->isValid($value);
	}



	public function getMessages() {
		$errorMessages = $this->validator->getMessages();

		return $errorMessages;
	}
}
