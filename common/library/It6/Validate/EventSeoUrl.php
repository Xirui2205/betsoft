<?php

class It6_Validate_EventSeoUrl extends Zend_Validate_Abstract {
	//constant taken fom vic_main.seo_url.type column comment
	//has to be same as in Webservice_SeoUrl
	const TYPE_ID		= 3;
	
	const MSG_FORMAT	= 'illegalChars';
	const MSG_EXISTS	= 'msgEmailExists';

	private $objectId;
	private $regionId;
	private $sportId;
	
	public $existsIn;
	

	protected $_messageTemplates = array(
		self::MSG_FORMAT	=> "You are only allowed to use characters 'a-z', '0-9', '/' and '-'. Url must begin and end with '/'.",
		self::MSG_EXISTS	=> "'%value%' is alreade associated with '%existsIn%'.",
	);



	public function __construct($objectId=null, $regionId=null, $sportId=null) {
		$this->_messageVariables['existsIn'] = 'existsIn';
		
		$this->objectId	= $objectId;
		$this->regionId	= $regionId;
		$this->sportId	= $sportId;
	}



	public function isValid($value) {
		
		$this->_setValue($value);
		$valid = true;

		if(!preg_match('/^\/[a-z0-9\/][-a-z0-9\/]+\/$/', $value)) {
			$this->_error(self::MSG_FORMAT);
			$valid = false;
		}
		
		$include = array(
			'url'		=> $value,
			'typeId'	=> self::TYPE_ID,
			'regionId'	=> $this->regionId,
			'sportId'	=> $this->sportId,
		);


		$filterData		= array();
		$filterDef[]	= array('?'=> array('objectId'	=> $this->objectId), 'OP' => '!=');
		
		foreach($include as $colName => $value) {
			$filterDef[] = array('?'=> array($colName => $value), 'OP' => '=');
		}


		$extensions[] = new It6_WsExtension_Client_Filter('filter', $filterDef);
		$extensions[] = new It6_WsExtension_Client_Columns('columns', array('key'));

		$usedUrls = Zend_Registry::get('ws')->ext($extensions)->SeoUrl_Event->getAll();
		$usedUrls	= It6_ArrayWrapper::toNativeArray($usedUrls);


		if(!empty($usedUrls)) {
			$this->existsIn = $usedUrls[0]['key'];

			$this->_error(self::MSG_EXISTS);
			$valid = false;
		}

		return $valid;
	}
}
