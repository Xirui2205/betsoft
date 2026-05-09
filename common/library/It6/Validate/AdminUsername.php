<?php


class It6_Validate_AdminUsername extends It6_Validate_AbstractCheckDb {

	const MSG_SHORT		= 'msgShort';
	const MSG_LONG		= 'msgLong';
	const MSG_CHARS		= 'msgChars';
	const MSG_EXISTS	= 'msgUsernameExists';

	private $adminId;

	protected $minChars		= 4;
	protected $maxChars		= 20;


	protected $_messageVariables = array(
		'minChars'	=> 'minChars',
		'maxChars'	=> 'maxChars'
	);

	protected $_messageTemplates = array(
		self::MSG_SHORT		=> "'%value%' is too short. Username must have at least %minChars% characters.",
		self::MSG_LONG		=> "'%value%' is too long. Username must have no more then %maxChars% characters.",
		self::MSG_CHARS		=> "'%value%' contains invalid characters. Only letters and numbers are allowed.",
		self::MSG_EXISTS	=> "'%value%' is already taken.",
	);



	/**
	 * name: __construct
	 * @userId int if not null, the dbCheck will ignore the rows with this userid. Needed to be able to update existing user.
	 */
	public function __construct($adminId = null) {
		$this->adminId = $adminId;
	}


	public function isValid($username) {
		$this->_setValue($username);
		$valid = true;


		if(mb_strlen($username,'utf-8') < $this->minChars) {
			$this->_error(self::MSG_SHORT);
			$valid = false;
		}

		if(mb_strlen($username,'utf-8') > $this->maxChars) {
			$this->_error(self::MSG_LONG);
			$valid = false;
		}

		if(!mb_ereg_match("^[a-zA-Z][a-zA-Z0-9_.]+$", $username)) {
			$this->_error(self::MSG_CHARS);
			$valid = false;
		}


		if(!empty($this->adminId))
			$exclude = array('adminId' => $this->adminId);
		else
			$exclude = array();


		if($this->checkDb('Admin', 'adminId', array('loginName' => $username), $exclude)) {
			$this->_error(self::MSG_EXISTS);
			$valid = false;
		}


		return $valid;
	}
}
