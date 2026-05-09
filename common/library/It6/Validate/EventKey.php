<?php


class It6_Validate_EventKey extends It6_Validate_AbstractCheckDb {

	const MSG_CHARS		= 'msgChars';
	const MSG_EXISTS	= 'msgUsernameExists';

	private $eventId;


	protected $_messageTemplates = array(
		self::MSG_CHARS		=> "'%value%' contains invalid characters. Only digits, a-z and - are allowed.",
		self::MSG_EXISTS	=> "'%value%' is already taken.",
	);



	/**
	 * name: __construct
	 * @branchId int if not null, the dbCheck will ignore the rows with this branchId. Needed to be able to update existing branch.
	 */
	public function __construct($eventId = null) {
		$this->eventId = $eventId;
	}


	public function isValid($value) {
		$this->_setValue($value);
		$valid = true;

		if(!mb_ereg_match("^[-a-zA-Z0-9]+$", $value)) {
			$this->_error(self::MSG_CHARS);
			$valid = false;
		}


		if(!empty($this->eventId))
			$exclude = array('eventId' => $this->eventId);
		else
			$exclude = array();


		if($this->checkDb('Event', 'eventId', array('name' => $value), $exclude)) {
			$this->_error(self::MSG_EXISTS);
			$valid = false;
		}


		return $valid;
	}
}
