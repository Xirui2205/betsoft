<?php 

class Models_Reports{

	const REPORT_NAME_USER_ACCOUNTS = "user_accounts";
	const REPORT_NAME_HOST_ACCOUNTS = "host_accounts";

	public static function getReport($filterData){

		if (!isset($filterData["report_type"])) 
			return array();

		switch ($filterData["report_type"]) {
			case self::REPORT_NAME_HOST_ACCOUNTS:
				return self::getHostAccountsReport($filterData);
				break;
			
			case self::REPORT_NAME_USER_ACCOUNTS:
				return self::getUserAccountsReport($filterData);
				break;
		}

	}

	public static function getUserAccountsReport($filterData) {

		$logger = new Zend_Log(new Zend_Log_Writer_Firebug());

		$db = Zend_Registry::get("db");

		$rows = array();

		if (empty($filterData["dateFrom"]) || empty($filterData["dateTo"]))
			return array();
		else {
			$fnFixDate = function($d) {
			if (1 == preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $d, $matches)) {
				return It6_Date::timestampToDate(mktime(0,0,0,$matches[2],$matches[3],$matches[1]));
			} else {
				return $d;
			}
			};
			$dateFrom = $fnFixDate($filterData["dateFrom"]);
			$dateTo = $fnFixDate($filterData["dateTo"]);
			list($dateFrom, $dateTo) = It6_Date::dateToDbInterval($dateFrom, $dateTo);
		}

		$amount = 0;
		$users = Webservice_User::getAll();

		foreach ($users as $user) {
			
			 $amount += Webservice_User::getBalance($user->userId, $dateFrom);

		}

			$rows[] = array(
					"itemName" => "Stav minuleho obdobi",
					"amount" => $amount,
					"correction" => 0,
					"total" => ($amount + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userBonusEntry = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_BONUS_ENTRY,
			));

		$rows[] = array(
					"itemName" => "Bonusy",
					"amount" => $userBonusEntry,
					"correction" => 0,
					"total" => ($userBonusEntry + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userCashDeposit = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_DEPOSIT_CASH,
					Webservice_TransactionType::NAME_USER_DEPOSIT_CASH_TICKET_WIN,
					Webservice_TransactionType::NAME_USER_DEPOSIT_CASH_TICKET_WIN_CANCEL,
			));

		$rows[] = array(
					"itemName" => "Vklad pokladnou",
					"amount" => $userCashDeposit,
					"correction" => 0,
					"total" => ($userCashDeposit + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userTicketPayout = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_TICKET_COLLECT_NOCASH,
					Webservice_TransactionType::NAME_USER_TICKET_COLLECT_NOCASH_CANCEL,
			));


		$rows[] = array(
					"itemName" => "Vyplacení tiketu",
					"amount" => $userTicketPayout,
					"correction" => 0,
					"total" => ($userTicketPayout + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userTicketCreate = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_TICKET_CREATE,
					Webservice_TransactionType::NAME_USER_TICKET_CREATE_MP,
					Webservice_TransactionType::NAME_USER_TICKET_CANCEL,
					Webservice_TransactionType::NAME_USER_TICKET_CANCEL_MP,
					Webservice_TransactionType::NAME_USER_TICKET_RENEW_CANCELED,
					Webservice_TransactionType::NAME_USER_TICKET_RENEW_CANCELED_MP,
					Webservice_TransactionType::NAME_USER_EXCHANGE_FROM_POINTS,
					Webservice_TransactionType::NAME_USER_EXCHANGE_FROM_POINTS_CANCEL,
			));


		$rows[] = array(
					"itemName" => "Založení tiketu",
					"amount" => $userTicketCreate,
					"correction" => 0,
					"total" => ($userTicketCreate + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userBankDeposit = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_DEPOSIT_BANK,
					Webservice_TransactionType::NAME_USER_DEPOSIT_CARD,
					Webservice_TransactionType::NAME_USER_DEPOSIT_MANUAL,

			));


		$rows[] = array(
					"itemName" => "Vklad bankou",
					"amount" => $userBankDeposit,
					"correction" => 0,
					"total" => ($userBankDeposit + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userCashWithdraw = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_WITHDRAW_CASH,
					Webservice_TransactionType::NAME_USER_WITHDRAW_MANUAL,
			));

		$rows[] = array(
					"itemName" => "Výběr pokladnou",
					"amount" => $userCashWithdraw,
					"correction" => 0,
					"total" => ($userCashWithdraw + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userBankWithdraw = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_WITHDRAW_BANK,
			));


		$rows[] = array(
					"itemName" => "Výběr bankou",
					"amount" => $userBankWithdraw,
					"correction" => 0,
					"total" => ($userBankWithdraw + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);


		$fees = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_FEE_GENERAL,
			));


		$rows[] = array(
					"itemName" => "Poplatky",
					"amount" => $fees,
					"correction" => 0,
					"total" => ($fees + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);


		//konecny zustatek kont hracu

		$end = 0;

		foreach ($rows as $row) {
			$end += $row["amount"];
		}

		$rows[] = array(
					"itemName" => "Koncový stav",
					"amount" => $end,
					"correction" => 0,
					"total" => ($end + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		return $rows;
	}

	public static function getHostAccountsReport($filterData) {

		$db = Zend_Registry::get("db");

		$rows = array();

		if (empty($filterData["dateFrom"]) || empty($filterData["dateTo"]))
			return array();
		else {
			$fnFixDate = function($d) {
			if (1 == preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $d, $matches)) {
				return It6_Date::timestampToDate(mktime(0,0,0,$matches[2],$matches[3],$matches[1]));
			} else {
				return $d;
			}
			};
			$dateFrom = $fnFixDate($filterData["dateFrom"]);
			$dateTo = $fnFixDate($filterData["dateTo"]);
			list($dateFrom, $dateTo) = It6_Date::dateToDbInterval($dateFrom, $dateTo);
		}


		$amount = 0;
		$hosts = Webservice_Host::getAll();

		foreach ($hosts as $host) {
			
			 $amount += Webservice_Host::getBalance($host->hostId, $dateFrom, true, null, null);

		}

		$rows[] = array(
					"itemName" => "Počáteční stav",
					"amount" => $amount,
					"correction" => 0,
					"total" => ($amount + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$hostTicketCreate = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_TICKET_CREATE_CASH,
					Webservice_TransactionType::NAME_BRANCH_TICKET_CREATE_CASH_MP,
					Webservice_TransactionType::NAME_BRANCH_TICKET_CANCEL_CASH,
					Webservice_TransactionType::NAME_BRANCH_TICKET_CANCEL_CASH_MP,
					Webservice_TransactionType::NAME_BRANCH_TICKET_RENEW_CANCELED_CASH,
					Webservice_TransactionType::NAME_BRANCH_TICKET_RENEW_CANCELED_CASH_MP,
			));


		$rows[] = array(
					"itemName" => "Vsazeno",
					"amount" => $hostTicketCreate,
					"correction" => 0,
					"total" => ($hostTicketCreate + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$hostTicketPayout = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_TICKET_COLLECT,
					Webservice_TransactionType::NAME_BRANCH_TICKET_COLLECT_MP,
					Webservice_TransactionType::NAME_BRANCH_TICKET_COLLECT_CANCEL,
			));


		$rows[] = array(
					"itemName" => "Vyplaceno",
					"amount" => $hostTicketPayout,
					"correction" => 0,
					"total" => ($hostTicketPayout + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$hostWithdraw = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_WITHDRAW,
			));


		$rows[] = array(
					"itemName" => "Odvod",
					"amount" => $hostWithdraw,
					"correction" => 0,
					"total" => ($hostWithdraw + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$hostDeposit = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_DEPOSIT,
			));


		$rows[] = array(
					"itemName" => "Dotace",
					"amount" => $hostDeposit,
					"correction" => 0,
					"total" => ($hostDeposit + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$hostUserDeposit = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_USER_DEPOSIT_CASH,
					Webservice_TransactionType::NAME_BRANCH_USER_DEPOSIT_CASH_TICKET_WIN,
					Webservice_TransactionType::NAME_BRANCH_USER_DEPOSIT_CASH_TICKET_WIN_CANCEL,
			));


		$rows[] = array(
					"itemName" => "Dotace hráčskeho konta",
					"amount" => $hostUserDeposit,
					"correction" => 0,
					"total" => ($hostUserDeposit + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);


		$hostUserWithdraw = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_USER_WITHDRAW_CASH,
			));


		$rows[] = array(
					"itemName" => "Výběr z hráčskeho konta",
					"amount" => $hostUserWithdraw,
					"correction" => 0,
					"total" => ($hostUserWithdraw + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$end = 0;

		foreach ($rows as $row) {
			$end += $row["amount"];
		}

		$rows[] = array(
					"itemName" => "Koncový stav",
					"amount" => $end,
					"correction" => 0,
					"total" => ($end + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		return $rows;

	}
	
}