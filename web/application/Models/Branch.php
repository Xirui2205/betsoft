<?php

class Models_Branch {

	private static $mapCenterLat	= 49.915862;
	private static $mapCenterLong	= 15.324096;
	
	private static $countryZoom		= 7;
	private static $locationZoom	= 9;
	private static $cityZoom		= 11;
	private static $detailZoom		= 18;


	public static function getMapZoom($zoomLevel='country') {
		if($zoomLevel == 'location')
			return self::$locationZoom;
		else if($zoomLevel == 'city')
			return self::$cityZoom;
		else if($zoomLevel == 'detail')
			return self::$detailZoom;
		else
			return self::$countryZoom;
	}
	
	
	public static function prepareView($view, $request, $topOnly=true) {
		$ws = Zend_Registry::get('ws');

		$extensions = array();
		$cols = array('branchId', 'place', 'zip','street','town','latitude','longitude','locationName','info');
		$extensions[] = new It6_WsExtension_Client_Columns('columns', $cols);

		$filterDef = array(
			array('?'=> array('branchId' => array(It6_Models_Branch::ID_INTERNET,It6_Models_Branch::ID_INTERNET_LIVE)), 'OP' => 'NOT IN (?)'),
			array('?'=> array('isTesting' => '0'), 'OP' => '='),
			array('?'=> array('isListed' => '1'), 'OP' => '='),
			array('?'=> array('isActive' => '1'), 'OP' => '='),
			array('?'=> 'latitude', 'OP' => 'IS NOT NULL'),
			array('?'=> 'longitude', 'OP' => 'IS NOT NULL')
		);
		if($topOnly === true)
			$filterDef[] = array('?' => array('isTop' => '1'), 'OP' => '=');
		$extensions[] = new It6_WsExtension_Client_Filter('def-filter', $filterDef);
		$extensions[] = new It6_WsExtension_Client_Order('def-order', array('town', 'street'));


		$branches = $ws->ext($extensions)->Branch->getAll();
		$branches = It6_ArrayWrapper::toNativeArray($branches);


		$addr		= $request->getParam('addr');
		$zoom		= $request->getParam('zoom');
		$markerType	= $request->getParam('markerType');
		$branchId	= $request->getParam('branchId');
		if(!empty($addr))
			$view->addr	= $addr;
		if(!empty($zoom))
			$view->zoom = self::getMapZoom($zoom);

		$view->branchesJson		= json_encode($branches);
		$view->branches			= $branches;
		$view->mapCenterLat		= self::$mapCenterLat;
		$view->mapCenterLong	= self::$mapCenterLong;
		$view->countryZoom		= Models_Branch::getMapZoom('country');
		$view->locationZoom		= Models_Branch::getMapZoom('location');
		$view->cityZoom			= Models_Branch::getMapZoom('city');
		$view->detailZoom		= Models_Branch::getMapZoom('detail');
		$locations				= $ws->BranchLocation->getAll();
		$locations				= It6_ArrayWrapper::toNativeArray($locations);
		$view->locationsJson	= json_encode($locations);
		$view->locations		= $locations;
		$view->markerType		= $markerType;
		$view->branchId			= $branchId; 

		$towns		= $ws->Branch->getTowns();
		$view->townsJson = json_encode(It6_ArrayWrapper::toAssocLikeArray($towns, 'id'));
		$view->towns	= It6_Models_Form_Util::getSelectOptions($towns, 'id', 'town');
		$view->topOnly = $topOnly;
	}

}
