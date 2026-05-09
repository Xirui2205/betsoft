<?php

class Models_Calculation {
    
    private static $branch_in = 0;
    private static $branch_out = 0;
	private static $mp = 0;
	private static $vyplaceno_predepsano = 0;
	private static $tax_value = 0;
	private static $DPH = 0;
	private static $bez_dph = 0;
	private static $fond_rizik = 0;
	private static $zaklad_provize = 0;
	private static $provize = 0;
	private static $provize_celkem = 0;
	private static $pohledavky = 0; 
	private static $k_vyplate = 0;
	private static $internet_balance_in = 0;
	private static $internet_balance_out = 0;


	public static function generateBranchCalculationPdf($filterData, $provisions){
		$ws = Zend_Registry::get("ws");
		$filter = new It6_WsForm_Filter(array(
			array('From', 'fromDate', 'date', array(array('dateFrom'),'>=','?'), 'It6_Validate_Date',null,'start'),
			array('To', 'toDate', 'date', array(array('dateTo','<=','?')), 'It6_Validate_Date',null,'end'),
			array(i18n::tr('branch_handle'), 'branchHandle', array('text' => function($data){return explode(";", $data);}), array(array('handle', 'IN (?)'))),				
		));
		//$filter->getExtension($filterData, $extensions);

		//$filter->getExtension($filter, $extensions);

		$branches = $ws->Branch->getAllWhere(array("br.handle IN (?)" => explode(";", $filterData["branchHandle"])));
		$branches = It6_ArrayWrapper::toNativeArray($branches);

		$files = array();

        //$provisions = It6_ArrayWrapper::toNativeArray($ws->Branch->getProvisions());
	
		foreach ($branches as $branch) {			
			if ( $branch['calculation'] == 1 ) {				
			    foreach ($provisions as $key => $value) {
			    	if ($provisions[$key]['branchHandle'] == $branch['handle']) {
				    	$provisionKey = $key;
				    	break;
			    	}
			    }

	            $address = explode(chr(10), $branch['correspondenceAddress']);
				$exportOdt = new It6_Export_Odt();

				self::calculateProvisionBranch($provisionKey, $provisions, $branch, $filterData);
				
				$templateFileName = 'provize_' . $branch['contractName'] . '.odt';
				
				$filePath = $exportOdt->generate(
				 		array("year" => date("Y", strtotime($filterData["fromDate"])),
				 			  "branch_name" => $branch['name'],
				 			  "branch_street" => $branch['street'],
				 			  "address_1" => isset($address[0]) ? $address[0] : '',
				 			  "address_2" => isset($address[1]) ? $address[1] : '',
				 			  "address_3" => isset($address[2]) ? $address[2] : '',
				 			  "address" => $branch['providerName'] . ', ' . $branch['providerAddress'],
				 			  "ico" => $branch['providerIc'],
				 			  "dic" => $branch['providerDic'],
				 			  "bank_account" => (!empty($branch['accountPrefix'])) ? $branch['accountPrefix'].'-' . $branch['accountNumber'] . '/' . $branch['bankCode'] : '' . $branch['accountNumber'] . '/' . $branch['bankCode'],
				 			  "from" => date("d.m.Y", strtotime($filterData["fromDate"])),
				 			  "to" => date("d.m.Y", strtotime($filterData["toDate"])),
				 			  "branch_in" =>  number_format(self::$branch_in, 2, ',', ' '),
				 			  "branch_out" => number_format(self::$branch_out, 2, ',', ' '),
				 			  "mp" => number_format(self::$mp, 2, ',', ' '),
				 			  "vyplaceno_predepsano" => number_format(self::$vyplaceno_predepsano, 2, ',', ' '),
				 			  "tax_value" => self::$tax_value * 100,
				 			  "DPH" => number_format(self::$DPH, 2, ',', ' '),
				 			  "bez_dph" => number_format(self::$bez_dph, 2, ',', ' '),
				 			  "fond_rizik" => number_format(self::$fond_rizik, 2, ',', ' '),
				 			  "zaklad_provize" => number_format(self::$zaklad_provize, 2, ',', ' '),
				 			  "provize" => number_format(self::$provize, 2, ',', ' '),
				 			  "provize_celkem" => number_format(self::$provize_celkem, 2, ',', ' '),
				 			  "pohledavky" => number_format(self::$pohledavky, 2, ',', ' '),
				 			  "k_vyplate" => number_format(self::$k_vyplate, 2, ',', ' '),
				 			  "date" => date("d.m.Y", time()),
				 			  "pct_naber" => $provisions[$provisionKey]["pctNaber"]
				 			), $templateFileName, It6_Export_Odt::$OUTPUT_FORMAT_PDF);

				$files[] = $filePath;
			}
		}
		if ( count($files) > 0 ) {
			$filename = 'branch_' . str_replace(".", "-", $filterData["fromDate"]) . '_' .  str_replace(".", "-", $filterData["toDate"]) . '.pdf';
			It6_Export_Abstract::joinPDFs($files, "$filename", true);
		}
	}

	public static function generateInternetCalculationPdf($filterData, $provisions){
		$ws = Zend_Registry::get("ws");
		$filter = new It6_WsForm_Filter(array(
			array('From', 'fromDate', 'date', array(array('dateFrom'),'>=','?'), 'It6_Validate_Date',null,'start'),
			array('To', 'toDate', 'date', array(array('dateTo','<=','?')), 'It6_Validate_Date',null,'end'),
			array(i18n::tr('branch_handle'), 'branchHandle', array('text' => function($data){return explode(";", $data);}), array(array('handle', 'IN (?)'))),				
		));
		$filter->getExtension($filterData, $extensions);

		$branches = $ws->Branch->getAllWhere(array("br.handle IN (?)" => explode(";", $filterData["branchHandle"])));
		$branches = It6_ArrayWrapper::toNativeArray($branches);

		$files = array();

        //$provisions = It6_ArrayWrapper::toNativeArray($ws->Branch->getProvisions());
		foreach ($branches as $branch) {
			if ( $branch['calculationNet'] == 1 ) {				
			    foreach ($provisions as $key => $value) {
			    	if ($provisions[$key]['branchHandle'] == $branch['handle']) {
				    	$provisionKey = $key;
				    	break;
			    	}
			    }

	            $address = explode(chr(10), $branch['correspondenceAddress']);
				$exportOdt = new It6_Export_Odt();

				self::calculateProvisionInternet($provisionKey, $provisions, $branch, $filterData);   
				$filePath = $exportOdt->generate(
				 		array("year" => date("Y", strtotime($filterData["fromDate"])),
				 			  "branch_name" => $branch['name'],
				 			  "branch_street" => $branch['street'],
				 			  "address_1" => isset($address[0]) ? $address[0] : '',
				 			  "address_2" => isset($address[1]) ? $address[1] : '',
				 			  "address_3" => isset($address[2]) ? $address[2] : '',
				 			  "address" => $branch['providerName'] . ', ' . $branch['providerAddress'],
				 			  "ico" => $branch['providerIc'],
				 			  "dic" => $branch['providerDic'],
				 			  "bank_account" => (!empty($branch['accountPrefix'])) ? $branch['accountPrefix'].'-' . $branch['accountNumber'] . '/' . $branch['bankCode'] : '' . $branch['accountNumber'] . '/' . $branch['bankCode'],
				 			  "from" => date("d.m.Y", strtotime($filterData["fromDate"])),
				 			  "to" => date("d.m.Y", strtotime($filterData["toDate"])),
				 			  "internet_balance_in" =>  number_format(self::$internet_balance_in, 2, ',', ' '),
				 			  "internet_balance_out" => number_format(self::$internet_balance_out, 2, ',', ' '),
				 			  "vyplaceno_predepsano" => number_format(self::$vyplaceno_predepsano, 2, ',', ' '),
				 			  "tax_value" => self::$tax_value * 100,
				 			  "DPH" => number_format(self::$DPH, 2, ',', ' '),
				 			  "bez_dph" => number_format(self::$bez_dph, 2, ',', ' '),
				 			  "fond_rizik" => number_format(self::$fond_rizik, 2, ',', ' '),
				 			  "zaklad_provize" => number_format(self::$zaklad_provize, 2, ',', ' '),
				 			  "provize" => number_format(self::$provize, 2, ',', ' '),
				 			  "provize_celkem" => number_format(self::$provize_celkem, 2, ',', ' '),
				 			  "pohledavky" => number_format(self::$pohledavky, 2, ',', ' '),
				 			  "k_vyplate" => number_format(self::$k_vyplate, 2, ',', ' '),
				 			  "date" => date("d.m.Y", time()),
				 			), It6_Export_Odt::$INTERNET_CALCULATION_ODT, It6_Export_Odt::$OUTPUT_FORMAT_PDF);

				$files[] = $filePath;
			}
		}
		if ( count($files) > 0 ) {
			$filename = 'internet_' . str_replace(".", "-", $filterData["fromDate"]) . '_' .  str_replace(".", "-", $filterData["toDate"]) . '.pdf';
			It6_Export_Abstract::joinPDFs($files, "$filename", true);
		}
	}

	public static function generateBranchKbBankSequence($filterData, $provisions){
		//ziskam provize zaskrtnutych handlu
		$ws = Zend_Registry::get("ws");
		
		$filter = new It6_WsForm_Filter(array(
			array('From', 'fromDate', 'date', array(array('dateFrom'),'>=','?'), 'It6_Validate_Date',null,'start'),
			array('To', 'toDate', 'date', array(array('dateTo','<=','?')), 'It6_Validate_Date',null,'end'),
			array(i18n::tr('branch_handle'), 'branchHandle', array('text' => function($data){return explode(";", $data);}), array(array('handle', 'IN (?)'))),
		));

		$branches = $ws->Branch->getAllWhere(array("br.handle IN (?)" => explode(";", $filterData["branchHandle"])));
		$branches = It6_ArrayWrapper::toNativeArray($branches);

		$transactions = array();
		
		foreach ($branches as $branch) {
			if ( $branch['calculation'] == 1) {
			    foreach ($provisions as $key => $value) {
			    	if ($provisions[$key]['branchHandle'] == $branch['handle']) {
				    	$provisionKey = $key;
				    	break;
			    	}
			    }

				self::calculateProvisionBranch($provisionKey, $provisions, $branch, $filterData);
				
				if ( self::$k_vyplate > 0 ) {
					$bankInfo = Models_Branch::getBankInfo($branch['branchId']);				
					if ( isset($bankInfo["provision"]) ) {
						$transactions[] = array(
							"value" 				=> self::$k_vyplate,
							"bankAccountPrefix"		=> isset($bankInfo["provision"]["accountPrefix"]) ? $bankInfo["provision"]["accountPrefix"] : '',
							"bankAccountNumber"		=> isset($bankInfo["provision"]["accountNumber"]) ? $bankInfo["provision"]["accountNumber"] : '',
							"accountType"			=> "host",
							"bankAccountBankCode"	=> isset($bankInfo["provision"]["bankCode"]) ? (string)$bankInfo["provision"]["bankCode"] : '',
							"branchHandle"			=> $branch["handle"] . date("my", strtotime($filterData['fromDate']))
						);
					}
				}
			}
		}

		$dbAdmin = Zend_Registry::get('zdb_admin');
		$export = new It6_Bank_Batch_Export_Kb_Best($dbAdmin);
		$content = $export->export($transactions);
		$filePath = uniqid("/tmp/");
		file_put_contents($filePath, $content);
		return $content;
		// $filePath = It6_Bank_Batch_Export_Kb_Best::getExportsDir() . $file;
	}

	public static function generateInternetKbBankSequence($filterData, $provisions){
		//ziskam provize zaskrtnutych handlu
		$ws = Zend_Registry::get("ws");

		$filter = new It6_WsForm_Filter(array(
			array('From', 'fromDate', 'date', array(array('dateFrom'),'>=','?'), 'It6_Validate_Date',null,'start'),
			array('To', 'toDate', 'date', array(array('dateTo','<=','?')), 'It6_Validate_Date',null,'end'),
			array(i18n::tr('branch_handle'), 'branchHandle', array('text' => function($data){return explode(";", $data);}), array(array('handle', 'IN (?)'))),
		));

		$branches = $ws->Branch->getAllWhere(array("br.handle IN (?)" => explode(";", $filterData["branchHandle"])));
		$branches = It6_ArrayWrapper::toNativeArray($branches);

		$transactions = array();

		foreach ($branches as $branch) {
			if ( $branch['calculationNet'] == 1 ) {				
			    foreach ($provisions as $key => $value) {
			    	if ($provisions[$key]['branchHandle'] == $branch['handle']) {
				    	$provisionKey = $key;
				    	break;
			    	}
			    }

				self::calculateProvisionInternet($provisionKey, $provisions, $branch, $filterData);
				if ( self::$k_vyplate > 0 ) {
					$bankInfo = Models_Branch::getBankInfo($branch['branchId']);				
					if ( isset($bankInfo["provision"]) ) {
						$transactions[] = array(
							"value" 				=> self::$k_vyplate,
							"bankAccountPrefix"		=> isset($bankInfo["provision"]["accountPrefix"]) ? $bankInfo["provision"]["accountPrefix"] : '',
							"bankAccountNumber"		=> isset($bankInfo["provision"]["accountNumber"]) ? $bankInfo["provision"]["accountNumber"] : '',
							"accountType"			=> "host",
							"bankAccountBankCode"	=> isset($bankInfo["provision"]["bankCode"]) ? (string)$bankInfo["provision"]["bankCode"] : '',
							"branchHandle"			=> $branch["handle"] . date("my", strtotime($filterData['fromDate']))
						);
					}
				}
			}
		}

		$dbAdmin = Zend_Registry::get('zdb_admin');
		$export = new It6_Bank_Batch_Export_Kb_Best($dbAdmin);
		$content = $export->export($transactions);
		$filePath = uniqid("/tmp/");
		file_put_contents($filePath, $content);
		return $content;
		// $filePath = It6_Bank_Batch_Export_Kb_Best::getExportsDir() . $file;
	}

	public static function calculateProvisionBranch($provisionKey, $provisions = null, $branch, $filterData){

		$activeContract = Webservice_Contract::getActiveByBranch($branch['branchId']);
		$activeContract = It6_ArrayWrapper::toNativeArray($activeContract);

		$templateId = 0;
		if (!empty($activeContract)) {
			$templateId = $activeContract[0]['templateId'];
		}

		switch ( $templateId ) {
			case Webservice_ContractTemplate::TEMPLATE_VSAZENO_VYPLACENO:
				self::$branch_in = self::$branch_out = self::$mp = self::$vyplaceno_predepsano = self::$tax_value = self::$DPH = self::$bez_dph =
				self::$fond_rizik = self::$zaklad_provize = self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate =
				self::$internet_balance_in = self::$internet_balance_out = 0;
				break;

			case Webservice_ContractTemplate::TEMPLATE_VSAZENO_PREDEPSANO:
				self::$branch_in = $provisions[$provisionKey]['branchIn'];
				self::$branch_out = $provisions[$provisionKey]['branchPayoutOut']; // branchOut
				self::$mp = $provisions[$provisionKey]['branchMp'];
				self::$vyplaceno_predepsano = self::$branch_in - self::$branch_out; // + self::$mp;
				self::$tax_value = $provisions[$provisionKey]['taxValue']; // isset($params['odvod-statu']) ? $params['odvod-statu'] * 100 : 0;
				self::$DPH = self::$vyplaceno_predepsano * self::$tax_value;
				self::$bez_dph = self::$vyplaceno_predepsano - self::$DPH;
				self::$fond_rizik = (self::$bez_dph * Constant::get('RISK_LIMIT_PCT')) / 100;
				self::$zaklad_provize = self::$bez_dph - self::$fond_rizik;
				if (self::$zaklad_provize > 0) {
					self::$provize = $provisions[$provisionKey]['provision'];
					self::$provize_celkem = self::$provize;
					self::$pohledavky = 0; 
					self::$k_vyplate = self::$provize_celkem;
				} else {
					self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate = 0;
				}
				break;

			case Webservice_ContractTemplate::TEMPLATE_PAUSAL:
				self::$branch_in = self::$branch_out = self::$mp = self::$vyplaceno_predepsano = self::$tax_value = self::$DPH = self::$bez_dph =
				self::$fond_rizik = self::$zaklad_provize = self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate =
				self::$internet_balance_in = self::$internet_balance_out = 0;
				break;

			case Webservice_ContractTemplate::TEMPLATE_NABER:
				self::$branch_in = $provisions[$provisionKey]['branchIn'];
				self::$tax_value = $provisions[$provisionKey]['taxValue'];
				self::$provize = self::$provize_celkem = self::$k_vyplate = self::$provize = $provisions[$provisionKey]['provision'];
				self::$branch_out = self::$mp = self::$vyplaceno_predepsano = self::$DPH = self::$bez_dph =
				self::$fond_rizik = self::$zaklad_provize = self::$pohledavky =
				self::$internet_balance_in = self::$internet_balance_out = 0;
				break;

			default:
				self::$branch_in = self::$branch_out = self::$mp = self::$vyplaceno_predepsano = self::$tax_value = self::$DPH = self::$bez_dph =
				self::$fond_rizik = self::$zaklad_provize = self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate =
				self::$internet_balance_in = self::$internet_balance_out = 0;
				break;
		}
		return;
	}

	public static function calculateProvisionInternet($provisionKey, $provisions = null, $branch, $filterData){
		$activeContract = Webservice_Contract::getActiveByBranch($branch['branchId']);
		$activeContract = It6_ArrayWrapper::toNativeArray($activeContract);

		$templateId = 0;
		if (!empty($activeContract)) {
			$templateId = $activeContract[0]['templateId'];
		}

		switch ( $templateId ) {
			case Webservice_ContractTemplate::TEMPLATE_VSAZENO_VYPLACENO:
				self::$branch_in = self::$branch_out = self::$mp = self::$vyplaceno_predepsano = self::$tax_value = self::$DPH = self::$bez_dph =
				self::$fond_rizik = self::$zaklad_provize = self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate =
				self::$internet_balance_in = self::$internet_balance_out = 0;
				break;

			case Webservice_ContractTemplate::TEMPLATE_VSAZENO_PREDEPSANO:
				self::$internet_balance_in = $provisions[$provisionKey]['internetBalanceIn'];
				self::$internet_balance_out = $provisions[$provisionKey]['internetBalanceOut'];

				/*if ( $branch['calculationNetType'] == "NEW" ) {
					$payouts = self::getBranchNewCalculationNetType($branch, $filterData);
					self::$internet_balance_in = $payouts[$branch['branchId']][0]['in'];
					self::$internet_balance_out = $payouts[$branch['branchId']][0]['out'];
		        }*/

				self::$vyplaceno_predepsano = self::$internet_balance_in - self::$internet_balance_out;
				self::$tax_value = $provisions[$provisionKey]['taxValue']; // isset($params['odvod-statu']) ? $params['odvod-statu'] * 100 : 0;
				self::$DPH = self::$vyplaceno_predepsano * self::$tax_value;
				self::$bez_dph = self::$vyplaceno_predepsano - self::$DPH;
				self::$fond_rizik = (self::$bez_dph * Constant::get('RISK_LIMIT_PCT')) / 100;
				self::$zaklad_provize = self::$bez_dph - self::$fond_rizik;
				if (self::$zaklad_provize > 0) {
					self::$provize = self::$zaklad_provize * 0.2; //$provisions[$provisionKey]['vyse-zalohy'] ? self::$zaklad_provize * $provisions[$provisionKey]['vyse-zalohy'] : 0;
					self::$provize_celkem = self::$provize;
					self::$pohledavky = 0; 
					self::$k_vyplate = self::$provize_celkem; // * 0.2;
				} else {
					self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate = 0;
				}

				if ( $branch['calculationNetType'] == "OLD" ) {
					switch (self::$provize_celkem) {
					    case (self::$provize_celkem <= 10000): 
					    	self::$k_vyplate = 0;
					    	break;
					    case (self::$provize_celkem > 10000 && self::$provize_celkem <= 50000):
					    	self::$k_vyplate = self::$provize_celkem * 0.06;
					    	break;
					    case (self::$provize_celkem > 50000 && self::$provize_celkem <= 100000): 
					    	self::$k_vyplate = self::$provize_celkem * 0.08;
					    	break;
					    case (self::$provize_celkem > 100000):
					    	self::$k_vyplate = self::$provize_celkem * 0.1;
					    	break;
					}
				}
				break;

			case Webservice_ContractTemplate::TEMPLATE_PAUSAL:
				self::$branch_in = self::$branch_out = self::$mp = self::$vyplaceno_predepsano = self::$tax_value = self::$DPH = self::$bez_dph =
				self::$fond_rizik = self::$zaklad_provize = self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate =
				self::$internet_balance_in = self::$internet_balance_out = 0;
				break;

			case Webservice_ContractTemplate::TEMPLATE_NABER:
				self::$branch_in = self::$branch_out = self::$mp = self::$vyplaceno_predepsano = self::$tax_value = self::$DPH = self::$bez_dph =
				self::$fond_rizik = self::$zaklad_provize = self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate =
				self::$internet_balance_in = self::$internet_balance_out = 0;
				break;

			default:
				self::$branch_in = self::$branch_out = self::$mp = self::$vyplaceno_predepsano = self::$tax_value = self::$DPH = self::$bez_dph =
				self::$fond_rizik = self::$zaklad_provize = self::$provize = self::$provize_celkem = self::$pohledavky = self::$k_vyplate =
				self::$internet_balance_in = self::$internet_balance_out = 0;
				break;
		}

		return;
	}

	public static function getBranchNewCalculationNetType($branch, $filterData){
		$db = Zend_Registry::get('db');

		$sqlInSumConds = array();
		$sqlOutSumConds = array('vyplacen=1', 'is_loss=0');
		if (!empty($filterData['fromDate'])) {
			$sqlInSumConds[] = $db->quoteInto('zalozen>=?', $filterData['fromDate']);
			$sqlOutSumConds[] = $db->quoteInto('vyplacen_date>=?', $filterData['fromDate']);
		}
		if (!empty($filterData['toDate'])) {
			$sqlInSumConds[] = $db->quoteInto('zalozen<=?', $filterData['toDate']);
			$sqlOutSumConds[] = $db->quoteInto('vyplacen_date<=?', $filterData['toDate']);
		}
		$sqlInSumConds = implode(' AND ', $sqlInSumConds);
		$sqlOutSumConds = implode(' AND ', $sqlOutSumConds);
		$sqlInSum = (empty($sqlInSumConds) ? 'castka': "IF($sqlInSumConds, castka, 0)");
		$sqlOutSum = (empty($sqlOutSumConds) ? 'win_real' : "IF($sqlOutSumConds, win_real, 0)");

		$branchIds = $branch['branchId'];

		$select = $db->select()
		->from(
			array('t' => Webservice_Ticket::$TABLE),
			array(
				'branchId' => 'b.id',
				'in' => "SUM($sqlInSum)",
				'out' => "SUM($sqlOutSum)",
			))
		->join(
			array('u' => 'vic_main.'.Webservice_User::$TABLE),
			't.user_id = u.user_id',
			null)
		->join(
			array('b' => 'vic_admin.'.Webservice_Branch::$TABLE),
			'u.branch_id = b.id',
			null)
		->where('u.branch_id IN (?)', $branch['branchId'])
		->where('u.datum_aktivace >= ?', date('Y-m-d', strtotime('-1 year')))
		->where('t.host_id = ?',It6_Models_Host::ID_INTERNET)
		->group('u.branch_id');

		return It6_ArrayWrapper::toAssocLikeArray($select->query()->fetchAll(), 'branchId');
	}

}