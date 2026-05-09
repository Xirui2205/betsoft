<?php
class It6_Validate_Email extends It6_Validate_AbstractCheckDb {

	//commented out by Martin (also lines 13, 35) as the error gets already added
	//by Zend_Validate_EmailAddress for which this is essentially just a wrapper
	//const MSG_FORMAT	= 'msgFormat';
	const MSG_EXISTS	= 'msgEmailExists';
	private $userId;

	protected $_messageTemplates = array(
		//self::MSG_FORMAT => "'%value%' is not in the right format.",
		self::MSG_EXISTS => "'%value%' is alreade associated with another account",
	);

	/**
	 * name: __construct
	 * @userId int if not null, the dbCheck will ignore the rows with this userid. Needed to be able to update existing user.
	 */
	public function __construct($userId = null) {
		$this->userId = $userId;
	}

	public function isValid($email) {
		$this->_setValue($email);
		$valid = true;

		$formatValidator = new Zend_Validate_EmailAddress();
		if (!$formatValidator->isValid($email)) $valid = false;

		if(!empty($this->userId))
			$exclude = array('userId' => $this->userId);
		else
			$exclude = array();

		if($this->checkDb('User', 'userId', array('email' => $email), $exclude)) {
			$this->_error(self::MSG_EXISTS);
			$valid = false;
		}

		return $valid;
	}
}