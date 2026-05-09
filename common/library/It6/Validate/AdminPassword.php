<?php

class It6_Validate_AdminPassword extends It6_Validate_AbstractCheckDb {

	const MSG_SHORT				= 'msgShort';
	const MSG_LONG				= 'msgLong';
	const MSG_CHARS				= 'msgChars';
	const MSG_EQ_USRNAME		= 'msgEqUsername';
	const MSG_NEQ_PASS2			= 'msgNeqPass2';
	const MSG_OLD_PASS			= 'msgEqOldPass';
	const MSG_REQUIRED	= 'msqRequired';


	private $username;
	private $dbPasswd;
	private $required;

	protected $minChars = 6;
	protected $maxChars = 16;


	protected $_messageVariables = array(
		'minChars'	=> 'minChars',
		'maxChars'	=> 'maxChars'
	);

	protected $_messageTemplates = array(
		self::MSG_SHORT			=> "Password must have at least %minChars% characters.",
		self::MSG_LONG			=> "Password must have no more then %maxChars% characters.",
		self::MSG_EQ_USRNAME	=> "Password can not be the same as your username.",
		self::MSG_NEQ_PASS2		=> "The two passwords do not mach.",
		self::MSG_REQUIRED		=> "This field is mandatory.",
		self::MSG_OLD_PASS		=> "The old password you entered in incorrect.",
		self::MSG_CHARS			=> "You are only allowed to use letters, numbers, '_', '-' and '.' in the password."
	);

	public function __construct($username = null, $dbPasswd = null, $required = false, $passVarName1='password', $passVarName2='passwordAgain') {
		//var_dump($required);exit;
		$this->username = $username;
		$this->dbPasswd = $dbPasswd;
		$this->required = $required;
		$this->passVarName1 = $passVarName1;
		$this->passVarName2 = $passVarName2;
	}



	public function isValid($pass) {
		$this->_setValue($pass);
		$valid = true;

		$encValue = It6_Models_Admin::cryptPasswd($pass);

		if (empty($pass) && $this->required === true) {
			$this->_error(self::MSG_REQUIRED);
			$valid = false;
		}
		elseif(!empty($pass)) {
			if(isset($this->dbPasswd) && ($encValue != $this->dbPasswd)) {
				$exists = $this->checkDb('Admin', 'adminId', array('passwd' => $encValue), array());
				if(empty($exists)) {
					$this->_error(self::MSG_OLD_PASS);
					$valid = false;
				}
			}
			else if(empty($this->dbPasswd)) {
				if(mb_strlen($pass,'utf-8') < $this->minChars) {
					$this->_error(self::MSG_SHORT);
					$valid = false;
				}

				if(mb_strlen($pass,'utf-8') > $this->maxChars) {
					$this->_error(self::MSG_LONG);
					$valid = false;
				}

				if(!preg_match('/^[-a-z0-9_.áéíóúůýžščřďťňě]+$/ui', $pass)) {
					$this->_error(self::MSG_CHARS);
					$valid = false;
				}

				$username = (empty($this->username) ? $_POST['loginName'] : $this->username);
				if(isset($username) && $pass == $username) {
					$this->_error(self::MSG_EQ_USRNAME);
					$valid = false;
				}

				if($_POST[$this->passVarName1] != $_POST[$this->passVarName2]) {
					$this->_error(self::MSG_NEQ_PASS2);
					$valid = false;
				}
			}
		}

		return $valid;
	}
}
