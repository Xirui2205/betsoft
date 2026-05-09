<?php
class It6_Cron_Job_BranchesDailyReport extends It6_Cron_Job_Abstract {

	const CLIENT_HOST = 'http://localhost:14445';
	const DELIMITER = ',';

	public function execute(array $params = array(), &$errorMessage = null) {
		$db = Zend_Registry::get('db');

		$yesterday = date("d.m.Y", strtotime("-1 day"));
		list($dateFrom, $dateTo) = It6_Date::dateToDbInterval($yesterday, $yesterday);

		$month_start = strtotime('first day of this month', time());
		list($monthStart) = It6_Date::dateToDbInterval($month_start);

		Help::createPath(ROOT.'tmp/branches_reports/');

		$data = array();
		$branches = Webservice_Branch::getDataStemBranch(null, $dateFrom, $dateTo, $monthStart);

		foreach ($branches as $branch) {
			$data[$branch->id]["handle"] = $branch->handle;
			$data[$branch->id]["town"] = $branch->town;
			$data[$branch->id]["street"] = $branch->street;
			$data[$branch->id]["desc"] = $branch->desc;
			$data[$branch->id]["count"] = $branch->count;
			$data[$branch->id]["amount"] = $branch->amount;
			$data[$branch->id]["count_month"] = $branch->count_month;
			$data[$branch->id]["amount_month"] = $branch->amount_month;
			$data[$branch->id]["payout_month"] = $branch->payout_month;
			$data[$branch->id]["startBalance"] = Webservice_Host::getBalance(null, $dateFrom, true, null, $branch->handle);
			$data[$branch->id]["endBalance"] = Webservice_Host::getBalance(null, $dateTo, true, null, $branch->handle);

			$onTheWay = Webservice_Branch::getWithdrawsForReport($branch->handle);
			$data[$branch->id]["onTheWayDeposit"] = $onTheWay["onTheWayDeposit"];
			$data[$branch->id]["onTheWayWithdraw"] = $onTheWay["onTheWayWithdraw"];
		}

		$columns = array();
		foreach ($data as $values) {
			foreach ($values as $key => $value) {
				array_push($columns, $key);
			}
			break;
		}

		$csvFile = fopen(ROOT.'tmp/branches_reports/branches_daily.csv', 'w');
		fwrite($csvFile, "\xEF\xBB\xBF", 3);
		fputcsv($csvFile, $columns);
		foreach ($data as $key => $values) {
			fputcsv($csvFile, $values);
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

		$params = array(
			It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'BranchesDaily',
			It6_Cron_Job_Email_ReportEmail::PARAM_TO => explode(self::DELIMITER, Webservice_Parameter::getGlobalParameter('report.branchesDaily.to')),
			It6_Cron_Job_Email_ReportEmail::PARAM_CSV_PATH => ROOT.'tmp/branches_reports/branches_daily.csv',
			It6_Cron_Job_Email_ReportEmail::PARAM_FILENAME => 'branches_daily.csv',
		);
		$cronJob = array(
			'typeName' => It6_Models_CronJobType::TYPENAME_EMAIL,
			'date' => It6_Date::dbNow(),
			'attemptsMax' => 1,
			'params' => $params
		);
		Webservice_CronJob::insert($cronJob);
	}
}