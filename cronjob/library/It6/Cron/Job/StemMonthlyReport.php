<?php
class It6_Cron_Job_StemMonthlyReport extends It6_Cron_Job_Abstract {

	const CLIENT_HOST = 'http://localhost:14445';
	const DELIMITER = ',';

	public function execute(array $params = array(), &$errorMessage = null) {
		$db = Zend_Registry::get('db');

		$first_day = strtotime('first day of this month', time());
		$last_day = strtotime('last day of this month', time());
		list($firstDay, $lastDay) = It6_Date::dateToDbInterval($first_day, $last_day);

		Help::createPath(ROOT."tmp/stem_reports/");

		$stem = Webservice_Stem::getAll();

		$data = array();

		foreach ($stem as $s) {
			$data[$s->stemId]["desc"] = $s->desc;
			$branchs = Webservice_Branch::getDataStemBranch($s->stemId, $firstDay, $lastDay);

			foreach ($branchs as $branch) {
				$data[$s->stemId][$branch->id]["handle"] = $branch->handle;
				$data[$s->stemId][$branch->id]["town"] = $branch->town;
				$data[$s->stemId][$branch->id]["street"] = $branch->street;
				$data[$s->stemId][$branch->id]["count"] = $branch->count;
				$data[$s->stemId][$branch->id]["amount"] = $branch->amount;
				$data[$s->stemId][$branch->id]["payout_month"] = $branch->payout_month;
				$data[$s->stemId][$branch->id]["startBalance"] = Webservice_Host::getBalance(null, $firstDay, true, null, $branch->handle);
				$data[$s->stemId][$branch->id]["endBalance"] = Webservice_Host::getBalance(null, $lastDay, true, null, $branch->handle);

				$onTheWay = Webservice_Branch::getWithdrawsForReport($branch->handle);
				$data[$s->stemId][$branch->id]["onTheWayDeposit"] = $onTheWay["onTheWayDeposit"];
				$data[$s->stemId][$branch->id]["onTheWayWithdraw"] = $onTheWay["onTheWayWithdraw"];
			}

			$columns = array();
			foreach ($data[$s->stemId] as $k => $values) {
				if ($k == 'desc') continue;
				foreach ($values as $key => $value) {
					array_push($columns, $key);
				}
				break;
			}

			$csvFile = fopen(ROOT.'tmp/stem_reports/monthly_stem_'.$s->stemId.'.csv', 'w');
			fwrite($csvFile, "\xEF\xBB\xBF", 3);
			fputcsv($csvFile, $columns);
			foreach ($data[$s->stemId] as $key => $values) {
				if ($key == 'desc') continue;
				fputcsv($csvFile, $values);
			}

			$to = explode(self::DELIMITER, Webservice_Parameter::getGlobalParameter('report.stemDaily.to'));
			if ($s->sendMail == 1) {
				$to_stem =  Webservice_Admin::getOneWhereColumns(array('adminId = ?' => $s->adminId), array('email'));
				array_push($to, $to_stem->email);
			}

			$params = array(
				It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'StemDaily',
				It6_Cron_Job_Email_ReportEmail::PARAM_TO => $to,
				It6_Cron_Job_Email_ReportEmail::PARAM_CSV_PATH => ROOT.'tmp/stem_reports/daily_stem_'.$s->stemId.'.csv',
				It6_Cron_Job_Email_ReportEmail::PARAM_FILENAME => 'daily_stem_'.$s->fileName.'.csv',
			);
			$cronJob = array(
				'typeName' => It6_Models_CronJobType::TYPENAME_EMAIL,
				'date' => It6_Date::dbNow(),
				'attemptsMax' => 1,
				'params' => $params
			);
			Webservice_CronJob::insert($cronJob);
		}

		$dataJson = json_encode($data);

		$clientCfg = array(
			'maxredirects' => 0,
			'timeout' => 120
		);
		$postParams = array(
			'data' => $dataJson,
		);

		$client = new Zend_Http_Client(self::CLIENT_HOST.'/reports', $clientCfg);
		$client->setParameterPost($postParams);
		// $response = $client->request();
	}
}