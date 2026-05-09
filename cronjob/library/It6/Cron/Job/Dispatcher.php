<?php

class It6_Cron_Job_Dispatcher {

	const ACTIVITY_LOG_ID = 1;
	//const EXEC_INTERVAL = 60; //Interval ve kterem systemovy cron spousti tento skript v sekundach

	function dispatch() {
		$db	 = Zend_Registry::get('db');
		$lastExecutionTime = $this->getLastExecutionTime();
		$jobs = $this->getJobsToExecute($lastExecutionTime);

		$stmtArchiveJob = $db->prepare( //new Zend_Db_Statement_Pdo_Mysql($db,
			'INSERT INTO archive_cronjob (cronjob_id, cronjob_type, result, attempts, exec_at, executed_at)
			SELECT cronjob_id, cronjob_type, result, attempts, exec_at, executed_at
			FROM cronjob
			WHERE cronjob_id=?'
		);
		$stmtArchiveJobParams = $db->prepare(
			'INSERT INTO archive_cronjob_param (archive_cronjob_id, cronjob_id, param_name, param_value)
			SELECT ?, a.cronjob_id, a.param_name, a.param_value
			FROM cronjob_param a
			WHERE a.cronjob_id=?'
		);
		$stmtDeleteJobParams = $db->prepare('DELETE FROM cronjob_param WHERE cronjob_id=?');
		$stmtDeleteJob = $db->prepare('DELETE FROM cronjob WHERE cronjob_id=?');

		$stmt = $db->select()
			->from('cronjob_type', array('type_id', 'type_name'))
			->query();
		$types = array();

		foreach ($stmt->fetchAll() as $type) {
			$types[$type['type_id']] = $type['type_name'];
		}

		$this->updateLastExecutionTime();

		foreach ($jobs as &$job) {
			$cronjobId = $job['cronjob_id'];

			if (empty($types[$job['cronjob_type']])) {
				It6_Log::err(
					'Unknown cronjob type found: [ID: %id%; TypeID: %type%]',
					It6_Log::TAG_CRONJOB,
					array('id' => $cronjobId, 'type' =>  $job['cronjob_type'])
				);
				continue;
			}

			$typeName = $types[$job['cronjob_type']];
			$className = 'It6_Cron_Job_' . ucfirst($typeName);
			$jobObj = new $className;

			++$job['attempts'];
			try {
				$result = $jobObj->execute($job['parameters'], $errorMessage);
			}
			catch (Exception $e) {
				$result = -1;
				It6_Log::err($e);
			}
			$data = array(
				'result' => $result,
				'attempts' => new Zend_Db_Expr('attempts+1'),
				'executed_at' => It6_Date::dbNow(),
			);
			$where = 'cronjob_id=' . $cronjobId;
			It6_DbTransaction::begin($db);
			try {
				$db->update('cronjob', $data, $where);
				if (0 == $result)
					It6_Log::info(
						'Job executed. [ID: %id%; TypeID: %type%]',
						It6_Log::TAG_CRONJOB,
						array('id' => $cronjobId, 'type' => $typeName, 'data' => $data)
					);
				else
					It6_Log::err(
						'Job execution failed! [ID: %id%; TypeID: %type%] (%message%)',
						It6_Log::TAG_CRONJOB,
						array('id' => $cronjobId, 'type' =>  $typeName, 'message' => $errorMessage, 'data' => $data)
					);
				// Archiving:
				//    copy: repetitive jobs always; one-time jobs on success or max attempts reached
				//    delete: repetivie jobs never; one-time jobs on success or max attempts reached
				if ( $job['exec_constantly'] || 0 == $result || $job['attempts'] >= $job['attemptsMax'] ) {
					$stmtArchiveJob->execute(array($cronjobId));
					$archiveCronJobId = $db->lastInsertId();
					$stmtArchiveJobParams->execute(array($archiveCronJobId, $cronjobId));
				}
				if ( !$job['exec_constantly'] && (0 == $result || $job['attempts'] >= $job['attemptsMax']) ) {
					$stmtDeleteJobParams->execute(array($cronjobId));
					$stmtDeleteJob->execute(array($cronjobId));
				}
				It6_DbTransaction::commit($db);
			}
			catch (Exception $e) {
				It6_DbTransaction::rollback($db);
				It6_Log::err($e);
			}
		}
	}


	/**
	 * @return NULL|integer UNIX timestamp of last dispatcher execution, NULL when timestamp is unknown
	 */
	private function getLastExecutionTime() {
		$rows = Zend_Registry::get('db')->select()
			->from('activity_log', array('t' => 'last_at'))
			->where('activity_type=?', self::ACTIVITY_LOG_ID)
			->query()
			->fetchAll();
		if (empty($rows) || empty($rows[0]['t']))
			return null;
		else
			return It6_Date::fromDbAsTimestamp($rows[0]['t']);
	}

	private function updateLastExecutionTime() {
		Zend_Registry::get('db')->update(
			'activity_log',
			array('last_at' => It6_Date::dbNow()),
			array('activity_type=?' => self::ACTIVITY_LOG_ID)
		);
	}

	/**
	 * @param integer $lastExecutionTime UNIX timestamp of last dispatcher execution
	 * @return array Data for jobs to execute (key is job ID)
	 */
	private function getJobsToExecute($lastExecutionTime) {

		$select = Zend_Registry::get('db')->select()
			->from(
				array('j' => 'cronjob'),
				array('cronjob_id', 'cronjob_type', 'exec_at', 'attempts', 'attempts_max', 'exec_constantly_at','executed_at')
			)
			->joinLeft(
				array('p' => 'cronjob_param'),
				'j.cronjob_id=p.cronjob_id',
				array('param_name', 'param_value')
			)
			->where('j.exec_constantly_at IS NOT NULL OR j.result IS NULL OR j.result<>0')
			->where('j.exec_constantly_at IS NOT NULL OR j.exec_at<=?', It6_Date::dbNow())
			->order(array('exec_at', 'cronjob_id'));

		$result = $select->query()->fetchAll();

		$jobs = array();
		foreach ($result as $row) {
			try {
				if ( !empty($row['exec_constantly_at']) ) {
					require_once('CronParser/CronParser.php');
					$cron = new CronParser();
					if ( $cron->calcLastRan($row['exec_constantly_at']) ) {

						$lastRan = $cron->getLastRanUnix($row['exec_constantly_at']);
						$lastRan = It6_Date::toTimestamp(strftime('%d.%m.%Y %H:%M:%S', $lastRan));

						$now = It6_Date::nowAsTimestamp();

						if (empty($lastExecutionTime)) {
							if (empty($row['executed_at']))
								continue;
							$executed = It6_Date::fromDbAsTimestamp($row['executed_at']);
						}
						else
							$executed = $lastExecutionTime;
						if ($lastRan <= $executed)
								continue;
					}
					else {
						throw new Exception("Invalid cron string: '" . $row['exec_constantly_at'] ."'");
					}
				}

				$cronjobId = $row['cronjob_id'];
				$paramName = $row['param_name'];

				if (empty($jobs[$cronjobId]))
					$jobs[$cronjobId] = array('parameters' => array());
				$job = &$jobs[$cronjobId];
				if (isset($row['param_value'])) {
					$value = Zend_Json::decode($row['param_value']);
					if (!array_key_exists($paramName, $job['parameters']))
						$job['parameters'][$paramName] = $value;
					else {
						if (!is_array($job['parameters'][$paramName]))
							$job['parameters'][$paramName] = array($job['parameters'][$paramName]);
						$job['parameters'][$paramName][] = $value;
					}
				}
				$job['cronjob_id'] = $cronjobId;
				$job['cronjob_type'] = $row['cronjob_type'];
				$job['attempts'] = (empty($row['attempts']) ? 0 : $row['attempts']);
				$job['attemptsMax'] = (empty($row['attempts_max']) ? 0 : $row['attempts_max']);
				$job['exec_constantly'] = !empty($row['exec_constantly_at']);
			}
			catch (Exception $e) {
				It6_Log::err($e);
			}
		}

		return $jobs;
	}
}
