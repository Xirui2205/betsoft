<?php 

class It6_Models_Reports{

	const REPORT_USER_ACCOUNTS 	= 1;
	const REPORT_HOST_ACCOUNTS 	= 2;

	const REPORT_NAME_USER_ACCOUNTS = "user_accounts";
	const REPORT_NAME_HOST_ACCOUNTS = "host_accounts";
	const REPORT_NAME_PRODUCTION    = "production";

	const REPORT_ITEM_START_BALANCE 		= 1;	//Počáteční zůstatek
	const REPORT_ITEM_END_BALANCE			= 2;	//Konečný zůstatek
	const REPORT_ITEM_BONUSES				= 3;	//Bonusy
	const REPORT_ITEM_HOST_DEPOSIT			= 4;	//Dobití konta hrace na pobocce
	const REPORT_ITEM_TICKET_PAYOUT 		= 5;	//Vyplata vyhry na konto hrace
	const REPORT_ITEM_TICKET_CREATE 		= 6;	//Zalozeni tiketu na internetu - z konta
	const REPORT_ITEM_BANK_DEPOSIT 			= 7;	//Dobití konta hrace bankou - kartou nebo prevodem
	const REPORT_ITEM_HOST_WITHRAW 			= 8;	//Vyber z konta na pobocce
	const REPORT_ITEM_BANK_WITHDRAW			= 9;	//Vyber z konta prevodem	
	const REPORT_ITEM_FEES					= 10;	//Poplatky
	const REPORT_ITEM_DEPOSIT 				= 11;	//Dotace kasy
	const REPORT_ITEM_WITHDRAW 				= 12;	//Odvod z kasy
	const REPORT_ITEM_HOST_TICKET_CREATE 	= 13;	//zalozeni tiketu na pobocce
	const REPORT_ITEM_HOST_TICKET_PAYOUT	= 14;	//vyplata tiketu na pobocce
	const REPORT_ITEM_MP 					= 15;   //manipulacni poplatek

	const REPORT_ITEM_NAME_START_BALANCE 		= "start_balance";	//Počáteční zůstatek
	const REPORT_ITEM_NAME_END_BALANCE			= "end_balance";	//Konečný zůstatek
	const REPORT_ITEM_NAME_BONUSES				= "bonuses";	//Bonusy
	const REPORT_ITEM_NAME_HOST_DEPOSIT			= "host_deposit";	//Dobití konta hrace na pobocce
	const REPORT_ITEM_NAME_TICKET_PAYOUT 		= "ticket_payout";	//Vyplata vyhry na konto hrace
	const REPORT_ITEM_NAME_TICKET_CREATE 		= "ticket_create";	//Zalozeni tiketu na internetu - z konta
	const REPORT_ITEM_NAME_BANK_DEPOSIT 		= "bank_deposit";	//Dobití konta hrace bankou - kartou nebo prevodem
	const REPORT_ITEM_NAME_HOST_WITHRAW 		= "host_withdraw";	//Vyber z konta na pobocce
	const REPORT_ITEM_NAME_BANK_WITHDRAW		= "bank_withdraw";	//Vyber z konta prevodem	
	const REPORT_ITEM_NAME_FEES					= "fees";	//Poplatky
	const REPORT_ITEM_NAME_DEPOSIT 				= "deposit";	//Dotace kasy
	const REPORT_ITEM_NAME_WITHDRAW 			= "withdraw";	//Odvod z kasy
	const REPORT_ITEM_NAME_HOST_TICKET_CREATE 	= "host_ticket_create";	//zalozeni tiketu na pobocce
	const REPORT_ITEM_NAME_HOST_TICKET_PAYOUT	= "host_ticket_payout";	//vyplata tiketu na pobocce
	const REPORT_ITEM_NAME_MP					= "mp"; //manipulacni poplatek

	const REPORT_TABLE 	= "report";
	const REPORT_ITEM_TABLE = "report_item";

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

			case self::REPORT_NAME_PRODUCTION:
				return self::getProductionReport($filterData);
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
			
			 $amount += Webservice_User::getBalance($user->userId, $dateFrom, false);

		}

			$rows[self::REPORT_ITEM_START_BALANCE] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_START_BALANCE),
					"amount" => $amount,
					"correction" => 0,
					"total" => ($amount + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userBonusEntry = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_BONUS_ENTRY,
			), false);

		$rows[self::REPORT_ITEM_BONUSES] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_BONUSES),
					"amount" => $userBonusEntry,
					"correction" => 0,
					"total" => ($userBonusEntry + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userCashDeposit = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_DEPOSIT_CASH,
					Webservice_TransactionType::NAME_USER_DEPOSIT_CASH_TICKET_WIN,
					Webservice_TransactionType::NAME_USER_DEPOSIT_CASH_TICKET_WIN_CANCEL,
			), false);

		$rows[self::REPORT_ITEM_HOST_DEPOSIT] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_HOST_DEPOSIT),
					"amount" => $userCashDeposit,
					"correction" => 0,
					"total" => ($userCashDeposit + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userTicketPayout = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_TICKET_COLLECT_NOCASH,
					Webservice_TransactionType::NAME_USER_TICKET_COLLECT_NOCASH_CANCEL,
			), false);


		$rows[self::REPORT_ITEM_TICKET_PAYOUT] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_TICKET_PAYOUT),
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
			), false);


		$rows[self::REPORT_ITEM_TICKET_CREATE] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_TICKET_CREATE),
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


		$rows[self::REPORT_ITEM_BANK_DEPOSIT] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_BANK_DEPOSIT),
					"amount" => $userBankDeposit,
					"correction" => 0,
					"total" => ($userBankDeposit + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userCashWithdraw = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_WITHDRAW_CASH,
					Webservice_TransactionType::NAME_USER_WITHDRAW_MANUAL,
			));

		$rows[self::REPORT_ITEM_HOST_WITHRAW] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_HOST_WITHRAW),
					"amount" => $userCashWithdraw,
					"correction" => 0,
					"total" => ($userCashWithdraw + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$userBankWithdraw = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_USER_WITHDRAW_BANK,
			), false, false);


		$rows[self::REPORT_ITEM_BANK_WITHDRAW] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_BANK_WITHDRAW),
					"amount" => $userBankWithdraw,
					"correction" => 0,
					"total" => ($userBankWithdraw + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);


		$fees = Webservice_User::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_FEE_GENERAL,
			), false);


		$rows[self::REPORT_ITEM_FEES] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_FEES),
					"amount" => $fees,
					"correction" => 0,
					"total" => ($fees + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);


		//konecny zustatek kont hracu

		$end = 0;

		foreach ($rows as $row) {
			$end += $row["amount"];
		}

		$rows[self::REPORT_ITEM_END_BALANCE] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_END_BALANCE),
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

		$rows[self::REPORT_ITEM_START_BALANCE] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_START_BALANCE),
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


		$rows[self::REPORT_ITEM_TICKET_CREATE] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_HOST_TICKET_CREATE),
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


		$rows[self::REPORT_ITEM_HOST_TICKET_PAYOUT] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_TICKET_PAYOUT),
					"amount" => $hostTicketPayout,
					"correction" => 0,
					"total" => ($hostTicketPayout + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$hostWithdraw = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_WITHDRAW,
			));


		$rows[self::REPORT_ITEM_WITHDRAW] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_WITHDRAW),
					"amount" => $hostWithdraw,
					"correction" => 0,
					"total" => ($hostWithdraw + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$hostDeposit = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_DEPOSIT,
			));


		$rows[self::REPORT_ITEM_DEPOSIT] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_DEPOSIT),
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


		$rows[self::REPORT_ITEM_HOST_DEPOSIT] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_HOST_DEPOSIT),
					"amount" => $hostUserDeposit,
					"correction" => 0,
					"total" => ($hostUserDeposit + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);


		$hostUserWithdraw = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_USER_WITHDRAW_CASH,
			));


		$rows[self::REPORT_ITEM_HOST_WITHRAW] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_HOST_WITHRAW),
					"amount" => $hostUserWithdraw,
					"correction" => 0,
					"total" => ($hostUserWithdraw + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$end = 0;

		foreach ($rows as $row) {
			$end += $row["amount"];
		}

		$rows[self::REPORT_ITEM_END_BALANCE] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_END_BALANCE),
					"amount" => $end,
					"correction" => 0,
					"total" => ($end + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		return $rows;

	}
	
	public static function getProductionReport($filterData) {

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


		$hostTicketCreate = Webservice_Host::getTransactionsSum(null, $dateFrom, $dateTo, 
			array(
					Webservice_TransactionType::NAME_BRANCH_TICKET_CREATE_CASH,
					Webservice_TransactionType::NAME_BRANCH_TICKET_CREATE_CASH_MP,
					Webservice_TransactionType::NAME_BRANCH_TICKET_CANCEL_CASH,
					Webservice_TransactionType::NAME_BRANCH_TICKET_CANCEL_CASH_MP,
					Webservice_TransactionType::NAME_BRANCH_TICKET_RENEW_CANCELED_CASH,
					Webservice_TransactionType::NAME_BRANCH_TICKET_RENEW_CANCELED_CASH_MP,
			));


		$rows[self::REPORT_ITEM_TICKET_CREATE] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_HOST_TICKET_CREATE),
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


		$rows[self::REPORT_ITEM_HOST_TICKET_PAYOUT] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_TICKET_PAYOUT),
					"amount" => $hostTicketPayout,
					"correction" => 0,
					"total" => ($hostTicketPayout + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		$select = $db->select()
					->from(array("ft" => "financial_transaction"), array(
						"value" => "SUM(value)"
						))
					->join(array("tt" => "financial_transaction_type"), "ft.type_id = tt.id", null)
					->where("tt.name IN (?)", array(Webservice_TransactionType::NAME_OTHER_TICKET_PAYOUT_MP, Webservice_TransactionType::NAME_OTHER_TICKET_PAYOUT_CANCEL_MP))
					->where("okTime > ?", $dateFrom)
					->where("okTime < ?", $dateTo);

		$row = $select->query()->fetch();

		$mp = $row["value"];
		
		$rows[self::REPORT_ITEM_MP] = array(
					"itemName" => I18n::tr(self::REPORT_ITEM_NAME_MP),
					"amount" => $mp,
					"correction" => 0,
					"total" => ($mp + 0) //zatim blbost, ale pro prehlednost, kdyby se nekdy pocitala korekce
				);

		return $rows;

	}
}