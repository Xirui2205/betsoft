<?php

class It6_Models_CronJob extends It6_Models_DbDependent {

	protected static $_cache = array();

	public static function scheduleJob($type, array $params, $execTime = null, $attemptsMax = null, &$db = null) {
		static::assureDbParam($db);
		It6_DbTransaction::begin($db);
		try {
			// bylo:
			// $cronjobId = It6_Models_CronJobSequence::nextId($db);
			// což nastavilo id špatně (způsobilo chybu, duplicitní id v tabulce cronjob)
			if (!isset($execTime))
				$execTime = It6_Date::dbNow();
			else if (is_numeric($execTime))
				$execTime = It6_Date::timestampToDb($execTime);
			$db->insert('cronjob', array(
				// 'cronjob_id' => $cronjobId, // ? je tam přece autoincrement
				'cronjob_type' => $type,
				'attempts_max' => (isset($attemptsMax) ? $attemptsMax : 1),
				'exec_at' => $execTime,
			));
			$cronjobId = $db->lastInsertId(); // cronjobId nově a klasicky
			foreach ($params as $name => $value) {
				if ( !isset($value) ) continue;
				if ( is_object($value) || is_array($value) )
					$value = It6_ArrayWrapper::toNativeArray($value);
				$json = Zend_Json::encode($value);
				$db->insert('cronjob_param', array(
					'cronjob_id' => $cronjobId,
					'param_name' => $name,
					'param_value' => $json,
				));
			}
			It6_DbTransaction::commit($db);
			return true;
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			throw $e;
		}
	}

	public static function scheduleJobNamed($typeName, array $params, $execTime = null, $attemptsMax = null, &$db = null) {
		static::assureDbParam($db);
		$typeId = It6_Models_CronJobType::getTypeIdByName($typeName, $db);
		if (empty($typeId))
			throw new Exception('Unknown cronjob type name: "' . $typeName . '"');
		return static::scheduleJob($typeId, $params, $execTime, $attemptsMax, $db);
	}

	/**
	 * @param $ticketIds array IDs of tickets.
	 * @param $sendTime int UNIX timestamp when email should be sent.
	 */
	public static function scheduleBetResultEmail(array $ticketIds, $sendTime = null){
		//TODO: check params and types
		if (count($ticketIds) > 0) {
			$entity = array();
			$entity['type'] = It6_Models_CronJobType::getTypeIdByName(It6_Models_CronJobType::TYPENAME_EMAIL);
			$entity['params'] = array(
				'ticketIds' => $ticketIds,
				It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'TicketResults'
			);
			return Zend_Registry::get('ws')->CronJob->insert($entity);
//				return self::scheduleJob(self::JOB_TYPE_EMAIL, $params, $sendTime);
		}
	}

	public static function scheduleBetResultSms( array $ticketIds, $sendTime = null ) {
		if ( count($ticketIds) > 0 ) {
			$entity = array();
			$entity['type'] = It6_Models_CronJobType::getTypeIdByName(It6_Models_CronJobType::TYPENAME_SMS);
			$entity['params'] = array(
				'ticketIds' => $ticketIds,
				It6_Cron_Job_Sms::PARAM_SMS_TYPE => 'TicketResults'
			);
			return Zend_Registry::get('ws')->CronJob->insert($entity);
		}
	}
}
