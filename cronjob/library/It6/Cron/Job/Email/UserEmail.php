<?php

abstract class It6_Cron_Job_Email_UserEmail extends It6_Cron_Job_Email_ControllerAbstract {

	const PARAM_USER_ID = 'user_id';

	protected $userData = array();

	public function __construct(array $params) {
		$this->params = $params;
	}

	public function prepareEmail() {
		parent::prepareEmail();

		if(empty($this->emailLang))
			$this->emailLang = $this->getUserLang($this->params[self::PARAM_USER_ID]);
	}

	public function getToField() {
		return $this->getUserEmail($this->params[self::PARAM_USER_ID]);
	}

	protected function getUserData($userId) {
		if (!isset($this->userData[$userId])) {
			$stmt = Zend_Registry::get('db')->select()
				->from( array('a' => 'uzivatel'), array('email') )
				->joinLeft( array('b' => 'jazyky'), 'a.lang_id=b.lang_id', array('lang_iso' => 'iso', 'lang_id') )
				->where('a.user_id=?', $userId)
				->query();
			$this->userData[$userId] = $stmt->fetch();
		}
		return $this->userData[$userId];
	}

	protected function getUserLang($userId){
		$userData = $this->getUserData($userId);
		if(empty($userData['lang_iso']))
			return 'cs';
		return $userData['lang_iso'];
	}

	protected function getUserLangId($userId){
		$userData = $this->getUserData($userId);
		if(empty($userData['lang_id']))
			return '1';
		return $userData['lang_id'];
	}

	protected function getUserEmail($userId){
		$userData = $this->getUserData($userId);
		return $userData['email'];
	}

}
