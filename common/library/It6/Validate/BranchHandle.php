<?php


class It6_Validate_BranchHandle extends It6_Validate_AbstractCheckDb {

	const MSG_CHARS		= 'msgChars';
	const MSG_EXISTS	= 'msgHandleExists';

	private $branchId;


	protected $_messageTemplates = array(
		self::MSG_CHARS		=> "'%value%' contains invalid characters. Only digits are allowed.",
		self::MSG_EXISTS	=> "'%value%' is already taken.",
	);



	/**
	 * name: __construct
	 * @branchId int if not null, the dbCheck will ignore the rows with this branchId. Needed to be able to update existing branch.
	 */
	public function __construct($branchId = null) {
		$this->branchId = $branchId;
	}


	public function isValid($value) {
		$this->_setValue($value);
		$valid = true;

		if(!mb_ereg_match("^[0-9]+$", $value)) {
			$this->_error(self::MSG_CHARS);
			$valid = false;
		}


		if(!empty($this->branchId))
			$exclude = array('branchId' => $this->branchId);
		else
			$exclude = array();


		if($this->checkDb('Branch', 'handle', array('handle' => $value), $exclude)) {
			$this->_error(self::MSG_EXISTS);
			$valid = false;
		}


		return $valid;
	}
}
