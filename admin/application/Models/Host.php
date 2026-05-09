<?php

class Models_Host {

	private static $filterData		= array();
	private static $paginatorData	= array('recsPerPage' => 10);
	private static $orderData		= array('hostId ASC');

	private static $TBODY_LAYOUT = 'host-tbody';

	/**
	 * Add fields 'lastAccess' (FALSE|string) and 'onlineStatus' (boolean)
	 * @param array $host One or more host structures
	 * @param boolean $isArray TRUE if $host is array of structures, FALSE if it is one structure
	 */
	public static function addOnlineStatus(&$host, $isArray) {
		if ($isArray)
			$hosts = &$host;
		else
			$hosts = array( &$host );

		$keys = array_map(function($h) { return "SHS:{$h['hostId']}"; }, $hosts);
		$times = It6_GlobalCache::getKeys($keys);
		$now = time();
		foreach ($hosts as &$h) {
			$key = "SHS:{$h['hostId']}";
			if (empty($times[$key])) {
				$h['lastAccess'] = false;
				$h['onlineStatus'] = false;
			}
			else {
				$time = $times[$key];
				$h['lastAccess'] = It6_Date::timestampToDateTime($time);
				$h['onlineStatus'] = ($now - $time <= 120);
			}
		}
	}

	public static function createTable(&$view, $inData) {
		$ws = Zend_Registry::get('ws');

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			self::$filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			self::$paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			self::$orderData = array_keys($inData['order']);

		//create filter
		$branches = It6_ArrayWrapper::toNativeArray($ws->Branch->getAllWhereOrder(
			array('branchId <> ?' => It6_Models_Branch::ID_INTERNET),
			array('name')
		));
		
		$branches = Models_Utils::getCollection($branches, 'branchId','name',array('' => i18n::tr('--Select--')));

		$onOffOpts = array(
			'' => i18n::tr('--Select--'),
			'NOT NULL'	=> 'on',
			'NULL'		=> 'off'
		);
		
		$filter = new It6_WsForm_Filter(array(
			array('Host Id', 'hostId', 'text', array(array('hostId', '=')), 'Zend_Validate_Digit'),
			array('Branch Id', 'branchId', 'text', array(array('branchId', '=')), 'Zend_Validate_Digit'),
			array('Branch handle', 'branchHandle', 'text', array(array('branchHandle', '=')), 'Zend_Validate_Digit'),
			array('Branch Name', 'branchName', 'select', array(array('branchId', '=')), null, null, $branches),
			array('In-allowed', 'inAllowed', 'checkbox', array(array('inAllowed', '='))),
			array('Out-allowed', 'outAllowed', 'checkbox', array(array('outAllowed', '='))),
			array('Hw Fingerprint', 'fingerprint', 'select', array(array('fingerprint', 'IS')), null, null, $onOffOpts, array('fingerprintFilter', 'OR')),
			array('Banned',			'banned',	   'select', array(array('banned',      'IS')), null, null, $onOffOpts, array('bannedFilter', 'OR'))
		));
		$filter->getExtension(self::$filterData, $extensions);


		//create pagination
		$paginator = new It6_WsForm_Paginator(self::$paginatorData['recsPerPage']);
		$paginator->getExtension(self::$paginatorData, $extensions);


		//create table
		$table = new It6_WsForm_Table(array(
			array(null, null),
			array('host_id', 'hostId'),
			array('host_name', 'name'),
			array('branch_handle', 'branchHandle'),
			array('branch_name', 'branchName'),
			array('ip_address','ip'),
			array('Version', 'version'),
			//TODO: figure out what to with this
			//array('Admin', 'admin'),
			array('address', 'branchAddress'),
			array('Phone', 'branchPhone'),
			array('Online', 'isOnline'),
			array('hw_fingerprint', 'fingerprint'),
			array('Banned', 'banned'),
			array('in-allowed', 'inAllowed'),
			array('out-allowed', 'outAllowed')
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension(self::$orderData, $extensions);

		if ( !empty($inData) ) {
			$hosts = $ws->ext($extensions)->Host->getAllWhere(array(
				'branch_id <> ?' => It6_Models_Branch::ID_INTERNET
			));
		}
		else {
			$hosts = array();
			$view->notFilter = true;
		}
		
		if (!empty($hosts)) {
			$hosts = It6_ArrayWrapper::toNativeArray($hosts);
			self::addOnlineStatus($hosts, true);
		}
		//$users = It6_ArrayWrapper::toNativeArray($users);

		$view->filter		= $filter->getLayout(null, self::$filterData);
		$view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$view->thead		= $table->getTheadLayout(null,self::$orderData);
		$view->tbody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $hosts);

	}
}
