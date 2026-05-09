<?php

class Models_Branch {

	protected static $ws;
	protected static $conv;

	public static $validatorConv = array(
		'float'		=> 'floatValidator',
		'string'	=> 'alnumValidator'
	);

	private static $call2formName = array(
		'BranchBankForm-balance'	=> 'BranchBankForm',
		'BranchBankForm-provision'	=> 'BranchBankForm',
		'BranchBankForm'			=> 'BranchBankForm',
		'BranchParameterForm'		=> 'BranchParameterForm',
		'BranchInfoForm'			=> 'BranchInfoForm',
		'BranchParameterForm'		=> 'BranchParameterForm',
		'BranchContractForm1'		=> 'BranchContractForm1',
		'BranchContractForm2'		=> 'BranchContractForm2',
		'BranchContractForm3'		=> 'BranchContractForm3',
		'BranchContractForm4'		=> 'BranchContractForm4',
		'BranchHostForm'			=> 'BranchHostForm',
	);




	public static function init(){
		self::$ws	= Zend_Registry::get('ws');
		self::$conv	= Zend_Registry::get('ws')->Branch->getConvTable();
	}



	public static function getBankInfo($branchId){

		self::init();
		$bankInfo = self::$ws->BankAccount->getByBranch( (integer)$branchId );
		$retdata = array();

		foreach($bankInfo as $key => $account){
			if($account['typeId'] == '1' && $account['isCurrent'] == '1'){
				foreach($account as $fieldName => $fieldData){
					$retdata['provision'][$fieldName] = $fieldData;
				}
			}
			else if($account['typeId'] == '2' && $account['isCurrent'] == '1'){
				foreach($account as $fieldName => $fieldData){
					$retdata['balance'][$fieldName] = $fieldData;
				}
			}
			else{
				$retdata['archive'][] = $account;
			}
		}

		$retdata['provision']['branchId'] = $branchId;
		$retdata['balance']['branchId'] = $branchId;

		return It6_ArrayWrapper::toNativeArray($retdata);
	}



	public static function getContractTemplate($data){

		$templateName = 'Templates/Contract-'.$data['templateName'].'.phtml';
		$template	= require($templateName);
		return $template;
	}


	//FIXME move to a helper

	public static function getBranchesComboBox() {
		self::init();
		$exts = array(
			new It6_WsExtension_Client_Columns('columns', array('branchId', 'name')),
			new It6_WsExtension_Client_Order('order', array('(name COLLATE utf8_czech_ci)')),
		);
		$bs = self::$ws->ext($exts)->Branch->getAll();

		$out = '<select name="newBranchId">';
		//FIXME: tohle do helpru nebo zendForm
		foreach ($bs as $k => $v)
			$out .="<option value=".$v['branchId'].">".$v['name']."</option>";

		$out .= '</select>';
		return $out;
	}


	public static function getContractInfo($branchId){

		$outdata	= array();
		$ws			= Zend_Registry::get('ws');


		$openContract		= $ws->Contract->getOpenByBranch( (integer)$branchId );
		$openContract		= It6_ArrayWrapper::toNativeArray($openContract);

		foreach($openContract as $oCon){
			if(!isset($outdata['openContract']) || !is_array($outdata['openContract']))
				$outdata['openContract'] = array();
			$outdata['openContract'][] = array_merge(Models_Utils::parseParameters($oCon['parameters']), $oCon);
		}

		$activeContract		= $ws->Contract->getActiveByBranch( (integer)$branchId);
		$activeContract		= It6_ArrayWrapper::toNativeArray($activeContract);
		if(!empty($activeContract)){
			$outdata['activeContract'] = array_merge(Models_Utils::parseParameters($activeContract[0]['parameters']), $activeContract[0]);
		}

		$expiredContract	= $ws->Contract->getExpiredByBranch( (integer)$branchId );
		$expiredContract	= It6_ArrayWrapper::toNativeArray($expiredContract);
		foreach($expiredContract as $eCon){
			if(!isset($outdata['expiredContract']) || !is_array($outdata['expiredContract']))
				$outdata['expiredContract'] = array();
			$outdata['expiredContract'][] = array_merge(Models_Utils::parseParameters($eCon['parameters']), $eCon);
		}

		$canceledContract	= $ws->Contract->getCanceledByBranch( (integer)$branchId );
		$canceledContract	= It6_ArrayWrapper::toNativeArray($canceledContract);
		foreach($canceledContract as $cCon){
			if(!isset($outdata['canceledContract']) || !is_array($outdata['canceledContract']))
				$outdata['canceledContract'] = array();
			$outdata['canceledContract'][] = array_merge(Models_Utils::parseParameters($cCon['parameters']), $cCon);
		}

		$signedNAContract	= $ws->Contract->getSignedButNotActiveByBranch( (integer)$branchId );
		$signedNAContract	= It6_ArrayWrapper::toNativeArray($signedNAContract);
		foreach($signedNAContract as $sCon){
			if(!isset($outdata['signedNAContract']) || !is_array($outdata['signedNAContract']))
				$outdata['signedNAContract'] = array();
			$outdata['signedNAContract'][] = array_merge(Models_Utils::parseParameters($sCon['parameters']), $sCon);
		}


		foreach($outdata as $key => $data){
			if(isset($data[0]) && is_array($data[0])){
				foreach($data as $subKey => $cont){
					$outdata[$key][$subKey]['dateValidTo']		= ($cont['dateValidTo'] == null?'':It6_Date::fromDbAsDate($cont['dateValidTo']));
					$outdata[$key][$subKey]['dateValidFrom']	= ($cont['dateValidFrom'] == null?'':It6_Date::fromDbAsDate($cont['dateValidFrom']));
					$outdata[$key][$subKey]['dateSigned']		= ($cont['dateSigned'] == null?'':It6_Date::fromDbAsDate($cont['dateSigned']));
					$outdata[$key][$subKey]['dateCanceled']		= ($cont['dateCanceled'] == null?'':It6_Date::fromDbAsDate($cont['dateCanceled']));
				}
			}
			else{
				$outdata[$key]['dateValidTo']	= It6_Date::fromDbAsDate($data['dateValidTo']);
				$outdata[$key]['dateValidFrom']	= It6_Date::fromDbAsDate($data['dateValidFrom']);
				$outdata[$key]['dateSigned']	= It6_Date::fromDbAsDate($data['dateSigned']);
				$outdata[$key]['dateCanceled']	= It6_Date::fromDbAsDate($data['dateCanceled']);
			}
		}

		return $outdata;
	}



	public static function getBranchTypes() {

		$result		= Zend_Registry::get('ws')->BranchType->getAll();
		$outdata	= array();

		foreach($result as $res) {
			$outdata[$res['id']] = $res['name'];
		}

		return $outdata;
	}



	public static function getBranchLocations() {

		$result		= Zend_Registry::get('ws')->BranchLocation->getAll();
		$outdata	= array();

		foreach($result as $res) {
			$outdata[$res['id']] = $res['name'];
		}

		return $outdata;
	}



	public static function getCurrency() {
		$columns = array(
			'currencyId',
			'name'
		);

		$extensions = array(new It6_WsExtension_Client_Order('order', array('name')));
		$result		= Zend_Registry::get('ws')->ext($extensions)->Currency->getAllColumns($columns);
		$outdata	= array();

		foreach($result as $res) {
			$outdata[$res['currencyId']] = $res['name'];
		}

		return $outdata;
	}
}

