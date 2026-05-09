<?php
class It6_Validate_UserCheck extends It6_Validate_AbstractCheckDb {

	const MSG_EXISTS = 'userCheck';

	protected $_messageTemplates = array(
		self::MSG_EXISTS => "'%value%' is alreade associated with another account",
	);

	protected $_messageVariables = array(
		'field' => '_field'
	);

	protected $_field;

	protected $_listOfFields;

	public function __construct(array $listOfFields) {
		$this->_listOfFields = $listOfFields;
	}

	public function isValid($value, $context = null) {
		$this->_setValue('user');

		$valid = true;

		$data = Webservice_IncompleteRegistration::getIncompleteReg(Zend_Session::getId());

		if (!is_array($context)) {
			$this->_error( self::NOT_PRESENT );
			$valid = false;
		}

		$where = array(
			'firstName' => $data["jmeno"],
			'lastName' => $data["prijmeni"],
			'street' => $context[$this->_listOfFields[0]].' '.$context[$this->_listOfFields[1]],
			'zip' => $context[$this->_listOfFields[2]],
			'town' => $context[$this->_listOfFields[3]],
			'birthDate' => $data["datum_narozeni"]
		);

		if ($this->checkDb('User', 'userId', $where)) {
			$this->_error(self::MSG_EXISTS);
			$valid = false;
		}

		return $valid;
	}
}