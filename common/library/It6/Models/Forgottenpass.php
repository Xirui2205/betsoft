<?php

class It6_Models_Forgottenpass{

	private static $ACTION_VALID_FOR = 86400;

	public static function newPassByNick($email/*, $nick*/) {
		$filterDef = array(
			array('?'=> array('email' => $email, 'OP' => '=')),
			/*array('?'=> array('username' => $nick, 'OP' => '='))*/
		);
		$extensions['filter'] = new It6_WsExtension_Client_Filter('filter', $filterDef);
		$extensions['columns'] = new It6_WsExtension_Client_Columns('columns', array('userId'));

		$user = Zend_Registry::get('ws')->ext($extensions)->User->getAll();
		$user = It6_ArrayWrapper::toNativeArray($user);
		$user = reset($user);

		if(empty($user)) {
			It6_Log::info(
				'User password reset not successful.',
				It6_Log::TAG_USER_OPERATION,
				array(
					/*'nick' => $nick,*/
					'email' => $email,
					'ip' => It6_Php::getRemoteAddr()
				)
			);
			return false;
		}

		//IT6 change pass by email confirmation START
		/*
		else {
			self::createAction($user[0]['userId']);
			It6_Log::info(
				'User password reset.',
				It6_Log::TAG_USER_OPERATION,
				array(
					'userId' => $user[0]['userId'],
					'email' => $email,
					'ip' => It6_Php::getRemoteAddr()
				)
			);
			return true;
		}
		*/
		//IT6 change pass by email confirmation END


		//IT6 change pass with no confirmation START
		else {
			$newPass = It6_Models_User::generatePassword(false);
			Webservice_User::setPassword(true, $user['userId'], $newPass, true);
			return true;
		}
		//IT6 change pass with no confirmation END
	}



	public static function createAction($userId){
		$actionHash = md5(time().$userId);
		$ws = Zend_Registry::get('ws');

		$data = array(
			'userId'		=> $userId,
			'actionType'	=> '1',
			'validFrom'		=> It6_Date::dbNow(),
			'validUntil'	=> It6_Date::dbNow(self::$ACTION_VALID_FOR),
			'actionHash'	=> $actionHash,
			'dateCreated'	=> It6_Date::dbNow(),
			'dateExecuted'	=> null
		);

		$ws->UserAction->insert($data);

		$params = array(
			It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'ConfirmPasswordChange',
			'user_id'		=> $userId,
			'hash'			=> $actionHash
		);

		$email = array(
			'type' 		=> 1,
			'date'		=> It6_Date::dbNow(),
			'params'	=> $params
		);

		if($ws->CronJob->insert($email))
			return true;
		else
			return false;
	}
}
