<?php
class It6_Validate_UsernameCheckDb extends It6_Validate_AbstractCheckDb {

	const MSG_EXISTS = 'msgUsernameExists';
	private $userId;

	protected $_messageTemplates = array(
		self::MSG_EXISTS => "'%value%' is already taken.",
	);

	/**
	* name: __construct
	* @userId int if not null, the dbCheck will ignore the rows with this userid. Needed to be able to update existing user.
	*/
	public function __construct($userId = null) {
		$this->userId = $userId;
	}

	public function isValid($username) {
		$this->_setValue($username);
		$valid = true;

		if (!empty($this->userId))
			$exclude = array('userId' => $this->userId);
		else
			$exclude = array();

		if ($this->checkDb('User', 'userId', array('username' => $username), $exclude)) {
			$this->_error(self::MSG_EXISTS);
			$valid = false;
		}

		return $valid;
	}
}