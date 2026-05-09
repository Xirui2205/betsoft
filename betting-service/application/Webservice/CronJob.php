<?php

class Webservice_CronJob extends Webservice_AbstractWebService {

	/**
	 * Inserts a new cronjob into the db
	 * @param struct $entity
	 * @return boolean
	 */
	public static function insert($entity) {
		try {
			$db = static::getDb();
			It6_Log::info('params',null,$entity['params']);
			if (!empty($entity['type']))
				return It6_Models_CronJob::scheduleJob(
					$entity['type'],
					$entity['params'],
					isset($entity['date']) ? $entity['date'] : null,
					isset($entity['attemptsMax']) ? $entity['attemptsMax'] : null,
					$db
				);
			else if (!empty($entity['typeName']))
				return It6_Models_CronJob::scheduleJobNamed(
					$entity['typeName'],
					$entity['params'],
					isset($entity['date']) ? $entity['date'] : null,
					isset($entity['attemptsMax']) ? $entity['attemptsMax'] : null,
					$db
				);
			else
				throw new It6_XmlRpc_Exception('CronJob type not specified', 0, $e);
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('insert.', 0, $e);
		}
	}
}
