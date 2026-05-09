<?php

class It6_Cron_Job_AccountingReports extends It6_Cron_Job_Abstract {

	public function execute(array $params = array(), &$errorMessage = null) {

		$db = Zend_Registry::get('db');

		Zend_Registry::set('translate', new It6_Translate_Ws());

		$date = mktime(12, 0, 0, date("m") - 1, 1, date("Y"));
		
		$month = date("n", $date);
		$year = date("Y", $date);

		$dateFrom = date("j.n.Y", $date);
		$dateTo = date("t.n.Y", $date);

		$filter = array(
			"dateFrom" 	=> $dateFrom,
			"dateTo"	=> $dateTo,  
		);

		$userAccounts = It6_Models_Reports::getUserAccountsReport($filter);

		$db->insert(It6_Models_Reports::REPORT_TABLE, array(
			"id_report_type" 	=> It6_Models_Reports::REPORT_USER_ACCOUNTS,
			"time"			 	=> It6_Date::dbNow(),
			"month"				=> $month,
			"year"				=> $year
			));

		$reportUserAccountsId = $db->lastInsertId();

		foreach ($userAccounts as $reportItemTypeId => $reportItem) {
			$db->insert(It6_Models_Reports::REPORT_ITEM_TABLE, array(
					"id_report" 			=> $reportUserAccountsId,
					"id_report_item_type" 	=> $reportItemTypeId,
					"value"					=> $reportItem["amount"]
				));
		}

		//obnovim spojeni s db abych se vyhnul erroru  mysql server has gone away
		$db->getConnection();
		Zend_Registry::set("db", $db);

		$hostAccounts = It6_Models_Reports::getHostAccountsReport($filter);

		$db->insert(It6_Models_Reports::REPORT_TABLE, array(
			"id_report_type" 	=> It6_Models_Reports::REPORT_HOST_ACCOUNTS,
			"time"			 	=> It6_Date::dbNow(),
			"month"				=> $month,
			"year"				=> $year
			));

		$reportHostAccountsId = $db->lastInsertId();

		foreach ($hostAccounts as $reportItemTypeId => $reportItem) {
			$db->insert(It6_Models_Reports::REPORT_ITEM_TABLE, array(
					"id_report" 			=> $reportHostAccountsId,
					"id_report_item_type" 	=> $reportItemTypeId,
					"value"					=> $reportItem["amount"]
				));
		}

		}

}
