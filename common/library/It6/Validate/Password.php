<?php
class It6_Validate_Password extends It6_Validate_AbstractCheckDb {

	const MSG_SHORT				= 'msgShort';
	const MSG_LONG				= 'msgLong';
	const MSG_CHARS				= 'msgChars';
	const MSG_EQ_USRNAME		= 'msgEqUsername';
	const MSG_NEQ_PASS2			= 'msgNeqPass2';
	const MSG_OLD_PASS			= 'msgEqOldPass';
	const MSG_REQUIRED			= 'msqRequired';
	const MSG_SIM_USRNAME		= 'msgSimUsername';
	const MSG_FORBIDDEN_PASS	= 'msgForbiddenPass';

	private $username;
	private $dbPasswd;
	private $required;

	protected $minchars = 6;
	protected $maxchars = 16;
	protected $maxsimchars = 6;

	protected $_messageVariables = array(
		'minchars' => 'minchars',
		'maxchars' => 'maxchars',
		'maxsimchars' => 'maxsimchars'
	);

	protected $_messageTemplates = array(
		self::MSG_SHORT			=> "password must have at least '%minchars%' characters.",
		self::MSG_LONG			=> "password must have no more then '%maxchars%' characters.",
		self::MSG_EQ_USRNAME	=> "password can not be the same as your username.",
		self::MSG_SIM_USRNAME	=> "password can't be similar to the username. can contain a max of '%ms%' consecutive repeating the same characters with username.",
		self::MSG_FORBIDDEN_PASS=> "this password is forbidden.",
		self::MSG_NEQ_PASS2		=> "the two passwords do not mach.",
		self::MSG_REQUIRED		=> "this field is mandatory.",
		self::MSG_OLD_PASS		=> "the old password you entered in incorrect.",
		self::MSG_CHARS			=> "you are only allowed to use letters, numbers, '_', '-' and '.' in the password."
	);

	public function __construct($username = null, $dbPasswd = null, $required = true, $passVarName1='pass_new', $passVarName2='pass_again') {
		$this->username = $username;
		$this->dbPasswd = $dbPasswd;
		$this->required = $required;
		$this->passVarName1 = $passVarName1;
		$this->passVarName2 = $passVarName2;
	}

	public function isValid($pass) {
		$this->_setValue($pass);
		$valid = true;

		$encValue = Models_Helpers_Help::cryptPass( Models_Helpers_Help::DecodeUnicodeUrl($pass) );

		if (empty($pass) && $this->required === true) {
			$this->_error(self::MSG_REQUIRED);
			$valid = false;

		} elseif(!empty($pass)) {

			if (isset($this->dbPasswd) && ($encValue != $this->dbPasswd)) {
				$exists = $this->checkDb('User', 'userId', array('password' => $encValue), array());
				if (empty($exists)) {
					$this->_error(self::MSG_OLD_PASS);
					$valid = false;
				}
			} else if(empty($this->dbPasswd)) {
				if (mb_strlen($pass,'utf-8') < $this->minchars) {
					$this->_error(self::MSG_SHORT);
					$valid = false;
				}

				if (mb_strlen($pass,'utf-8') > $this->maxchars) {
					$this->_error(self::MSG_LONG);
					$valid = false;
				}

				if (!preg_match('/^[-a-z0-9_.áéíóúůýžščřďťňě]+$/ui', $pass)) {
					$this->_error(self::MSG_CHARS);
					$valid = false;
				}

				$ws = Zend_Registry::get('ws');

				$username = (empty($this->username) ? Zend_Controller_Front::getInstance()->getRequest()->getParam('username') : $this->username);
				if (isset($username) && $pass == $username) {
					$this->_error(self::MSG_EQ_USRNAME);
					$valid = false;
				}/* else if (isset($username) && similar_text(strtolower($pass), strtolower($username)) > $ws->Parameter->getGlobalParameter('password.maxSimChars')) {
					$this->_error(self::MSG_SIM_USRNAME);
					$valid = false;
				}*/

				$forbiddenRegexps = array();
				$forbiddenRegexps = explode(';', $ws->Parameter->getGlobalParameter('password.forbidden'));

				foreach ($forbiddenRegexps as $regexp) {
					if ($regexp == $pass) {
						$this->_error(self::MSG_FORBIDDEN_PASS);
						$valid = false;
					}
				}

				if ($_POST[$this->passVarName1] != $_POST[$this->passVarName2]) {
					$this->_error(self::MSG_NEQ_PASS2);
					$valid = false;
				}
			}
		}
		return $valid;
	}
}