<?php
class It6_Cron_Job_MonthlyUsersBalanceReport extends It6_Cron_Job_Abstract {

	const DELIMITER = ',';

	public function execute(array $params = array(), &$errorMessage = null) {
		$db = Zend_Registry::get('db');
		SetLocale(LC_ALL, "cs_CZ.utf8");

		$first_day = strtotime('first day of previous month', time());
		$last_day = strtotime('last day of previous month', time());
		list($firstDay, $lastDay) = It6_Date::dateToDbInterval($first_day, $last_day);

		Help::createPath(ROOT."tmp/users_reports/");

		$db = Zend_Registry::get('db');
		$users = It6_Models_User::getBalanceUsers($firstDay, $lastDay, $db);

		$columns = array();
		foreach ($users as $values) {
			foreach ($values as $key => $value) {
				array_push($columns, $key);
			}
			break;
		}

		$csvFile = fopen(ROOT.'tmp/users_reports/users_monthly.csv', 'w');
		fwrite($csvFile, "\xEF\xBB\xBF", 3);
		fputcsv($csvFile, $columns);

		foreach ($users as $key => $values) {
			fputcsv($csvFile, $values);
		}

		$params = array(
			It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'UsersBalanceMonthly',
			It6_Cron_Job_Email_ReportEmail::PARAM_SUBJECT => 'users_balance_monthly',
			It6_Cron_Job_Email_ReportEmail::PARAM_TO => explode(self::DELIMITER, Webservice_Parameter::getGlobalParameter('report.usersBalanceMonthly.to')),
			It6_Cron_Job_Email_ReportEmail::PARAM_TO_BCC => explode(self::DELIMITER, Webservice_Parameter::getGlobalParameter('report.usersBalanceMonthly.to_bcc')),
			It6_Cron_Job_Email_ReportEmail::PARAM_CSV_PATH => ROOT.'tmp/users_reports/users_monthly.csv',
			It6_Cron_Job_Email_ReportEmail::PARAM_FILENAME => 'users_monthly.csv',
			It6_Cron_Job_Email_ReportEmail::PARAM_DATE => StrFTime("%B %Y", strtotime('previous month')),
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