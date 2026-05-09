<?php
abstract class It6_Alert_MailAbstract extends It6_Alert {

	const EMAIL_TYPE_PREFIX = 'Alert';	
	const MAIL_DELIMITER = ',';
	
	const PARAM_TO = 'to';
	
	public static function check($params, $userId = null, $branchId = null) {
		throw new Exception("Call of abstract method");
	}

	protected static function run($params, $userId = null, $branchId = null) {
		$entity = array();
		$entity['type'] = It6_Models_CronJobType::getTypeIdByName(It6_Models_CronJobType::TYPENAME_EMAIL);
		$entity['params'] = static::getMailParameters($params, $userId, $branchId);
		Webservice_CronJob::insert($entity);
		return true;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = array();
		$ret['userId'] = $userId;
		$ret['branchId'] = $branchId;
		$ret[It6_Cron_Job_Email::PARAM_EMAIL_TYPE] = static::getEmailType();
		$ret[It6_Cron_Job_Email_AlertEmail::PARAM_TO] = static::getTo($userId, $branchId);
		return $ret;
	}
	
	protected static function getEmailType() {
		return self::EMAIL_TYPE_PREFIX . static::getType();
	}

	protected static function getTo($userId = null, $branchId = null) {
		return explode(
			static::MAIL_DELIMITER,
			static::getParameter(self::PARAM_TO, $userId, $branchId));
	}
}