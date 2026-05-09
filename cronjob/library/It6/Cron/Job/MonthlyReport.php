<?php
class It6_Cron_Job_MonthlyReport extends It6_Cron_Job_Abstract {

	const CLIENT_HOST = 'http://localhost:14445';
	const DELIMITER = ',';
	const LANG_ID = 1;

	public function execute(array $params = array(), &$errorMessage = null) {
		$db = Zend_Registry::get('db');
		SetLocale(LC_ALL, "cs_CZ.utf8");

		$first_day = date('d.m.Y', strtotime('first day of previous month'));
		$last_day = date('d.m.Y', strtotime('last day of previous month'));
		list($dateFrom, $dateTo) = It6_Date::dateToDbInterval($first_day, $last_day);

		// ================== get payout tickets data ==============================================
		$payout = Webservice_Branch::getPayoutTicketsForReport($dateFrom, $dateTo);

		// ================== get collect tickets data ==============================================
		$collect = Webservice_Branch::getPayoutTicketsForReport($dateFrom, $dateTo, null, true);

		// ================== prepare data for reports all tickets =================================
		$data = Webservice_Branch::getDataForReport($dateFrom, $dateTo);

		$data_all['ticket_count'] = !empty($data['count']) ? $data['count'] : 0;
		$data_all['ticket_amount'] = !empty($data['amount']) ? (float)$data['amount'] : 0;
		$data_all['storno_ticket_count'] = !empty($data['countCancel']) ? $data['countCancel'] : 0;
		$data_all['storno_ticket_amount'] = !empty($data['amountCancel']) ? (float)$data['amountCancel'] : 0;
		$data_all['not_storno_ticket_count'] = $data_all['ticket_count'] - $data_all['storno_ticket_count'];
		$data_all['not_storno_ticket_amount'] = $data_all['ticket_amount'] - $data_all['storno_ticket_amount'];
		$data_all['point_not_storno_ticket_count'] = $data['countPoints'] - $data['countCancelPoints'];
		$data_all['point_not_storno_ticket_amount'] = (float)($data['amountPoints'] - $data['amountCancelPoints']);
		$data_all['collect_ticket_count'] = (empty($payout['countCollectionNet']) && empty($payout['countCollection'])) ? 0 : $payout['countCollectionNet'] + $payout['countCollection'];
		$data_all['collect_ticket_amount'] = (float)($payout['amountCollection'] + $payout['amountCollectionNet']);
		$data_all['diff_collect_not_storno_amount'] = $data_all['not_storno_ticket_amount'] - $data_all['collect_ticket_amount'];

		// ================== prepare data for reports internet tickets =================================
		$data = Webservice_Branch::getDataForReport($dateFrom, $dateTo, false, It6_Models_Host::ID_INTERNET);

		$data_internet['ticket_count'] = !empty($data['count']) ? $data['count'] : 0;
		$data_internet['ticket_amount'] = !empty($data['amount']) ? (float)$data['amount'] : 0;
		$data_internet['storno_ticket_count'] = !empty($data['countCancel']) ? $data['countCancel'] : 0;
		$data_internet['storno_ticket_amount'] = !empty($data['amountCancel']) ? (float)$data['amountCancel'] : 0;
		$data_internet['not_storno_ticket_count'] = $data_internet['ticket_count'] - $data_internet['storno_ticket_count'];
		$data_internet['not_storno_ticket_amount'] = (float)($data_internet['ticket_amount'] - $data_internet['storno_ticket_amount']);
		$data_internet['point_not_storno_ticket_count'] = $data['countPoints'] - $data['countCancelPoints'];
		$data_internet['point_not_storno_ticket_amount'] = (float)($data['amountPoints'] - $data['amountCancelPoints']);
		$data_internet['collect_ticket_count'] = !empty($payout['countCollectionNet']) ? $payout['countCollectionNet'] : 0;
		$data_internet['collect_ticket_amount'] = (float)$payout['amountCollectionNet'];
		$data_internet['diff_collect_not_storno_amount'] = $data_internet['not_storno_ticket_amount'] - $data_internet['collect_ticket_amount'];
		$data_internet['win_ratio'] = !empty($data_internet['ticket_amount']) ? (($data_internet['not_storno_ticket_amount'] - $data_internet['collect_ticket_amount'])/($data_internet['not_storno_ticket_amount']/100)) : 0;

		// ================== prepare data for reports branch tickets =================================
		foreach ($data_all as $key => $value) {
			if ($key == 'win_ratio') continue;
			$data_branch[$key] = $data_all[$key] - $data_internet[$key];
		}
		$data_branch['win_ratio'] = !empty($data_branch['ticket_amount']) ? (($data_branch['not_storno_ticket_amount'] - $data_branch['collect_ticket_amount'])/($data_branch['not_storno_ticket_amount']/100)) : 0;
		$data_branch['collect_ticket_count_real'] = !empty($collect['countCollection']) ? $collect['countCollection'] : 0;
		$data_branch['collect_ticket_amount_real'] = (float)$collect['amountCollection'];

		// ======================= prepare data for live ==============================================
		$data = Webservice_Host::getLiveBalancing(null, $dateFrom, $dateTo, true);

		$data_live['ticket_count'] = !empty($data['count']) ? $data['count'] : 0;
		$data_live['ticket_amount'] = !empty($data['amount']) ? (float)$data['amount'] : 0;
		$data_live['storno_ticket_count'] = !empty($data['countCancel']) ? $data['countCancel'] : 0;
		$data_live['storno_ticket_amount'] = !empty($data['amountCancel']) ? $data['amountCancel'] : 0;
		$data_live['not_storno_ticket_count'] = $data_live['ticket_count'] - $data_live['storno_ticket_count'];
		$data_live['not_storno_ticket_amount'] = $data_live['ticket_amount'] - $data_live['storno_ticket_amount'];
		$data_live['collect_ticket_count'] = !empty($data['countPaid']) ? $data['countPaid'] : 0;
		$data_live['collect_ticket_amount'] = !empty($data['amountPaid']) ? (float)$data['amountPaid'] : 0;
		$data_live['diff_collect_not_storno_amount'] = $data_live['not_storno_ticket_amount'] - $data_live['collect_ticket_amount'];
		$data_live['win_ratio'] = !empty($data_live['ticket_amount']) ? (($data_live['ticket_amount'] - $data_live['collect_ticket_amount'])/($data_live['ticket_amount']/100)) : 0;
		$data_live['unique_users'] = !empty($data['activeUsers']) ? $data['activeUsers'] : 0;

		// ======================== sum data all and data live ========================================
		$data_all['ticket_count'] += $data_live['ticket_count'];
		$data_all['ticket_amount'] += $data_live['ticket_amount'];
		$data_all['storno_ticket_count'] += $data_live['storno_ticket_count'];
		$data_all['storno_ticket_amount'] += $data_live['storno_ticket_amount'];
		$data_all['not_storno_ticket_count'] = $data_all['ticket_count'] - $data_all['storno_ticket_count'];
		$data_all['not_storno_ticket_amount'] = $data_all['ticket_amount'] - $data_all['storno_ticket_amount'];
		$data_all['collect_ticket_count'] += $data_live['collect_ticket_count'];
		$data_all['collect_ticket_amount'] += $data_live['collect_ticket_amount'];
		$data_all['diff_collect_not_storno_amount'] += $data_live['diff_collect_not_storno_amount'];
		$data_all['win_ratio'] = !empty($data_all['ticket_amount']) ? (($data_all['not_storno_ticket_amount'] - $data_all['collect_ticket_amount'])/($data_all['not_storno_ticket_amount']/100)) : 0;

		// ======================= create pdf file ====================================================
		$data_pdf = array();
		foreach ($data_all as $key => $value) $data_pdf["all_".$key] = $value;
		foreach ($data_internet as $key => $value) $data_pdf["net_".$key] = $value;
		foreach ($data_branch as $key => $value) $data_pdf["br_".$key] = $value;
		foreach ($data_live as $key => $value) $data_pdf["lv_".$key] = $value;

		foreach ($data_pdf as $key => $value) {
			if (strpos($key, 'amount') === false && strpos($key, 'win_ratio') === false) {
				$data_pdf[$key] = number_format($value, '0', '', ' ');
			} else {
				$data_pdf[$key] = number_format($value, '2', '.', ' ');
			}
		}

		$data_pdf["date"] = StrFTime("%B %Y", strtotime('previous month'));
		$data_pdf["type"] = It6_Models_Translator::translate('monthly_total_report', self::LANG_ID, $db);

		$exportOdt = new It6_Export_Odt();
		$filePath = $exportOdt->generate(
			$data_pdf,
			It6_Export_Odt::$DAILY_TOTAL_ODT,
			It6_Export_Odt::$OUTPUT_FORMAT_PDF
		);

		// ======================= prepare email cronjob ==============================================
		$params = array(
			It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'TotalReport',
			It6_Cron_Job_Email_ReportEmail::PARAM_SUBJECT => 'monthly_total_report',
			It6_Cron_Job_Email_ReportEmail::PARAM_TO => explode(self::DELIMITER, Webservice_Parameter::getGlobalParameter('report.totalReport.to')),
			It6_Cron_Job_Email_ReportEmail::PARAM_TO_BCC => explode(self::DELIMITER, Webservice_Parameter::getGlobalParameter('report.totalReport.to_bcc')),
			It6_Cron_Job_Email_ReportEmail::PARAM_PDF_PATH => $filePath,
			It6_Cron_Job_Email_ReportEmail::PARAM_FILENAME => 'monthly_report.pdf',
			It6_Cron_Job_Email_ReportEmail::PARAM_DATA_ALL => $data_all,
			It6_Cron_Job_Email_ReportEmail::PARAM_DATA_INTERNET => $data_internet,
			It6_Cron_Job_Email_ReportEmail::PARAM_DATA_BRANCH => $data_branch,
			It6_Cron_Job_Email_ReportEmail::PARAM_DATA_LIVE => $data_live,
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