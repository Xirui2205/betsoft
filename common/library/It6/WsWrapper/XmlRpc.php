<?php
class It6_WsWrapper_XmlRpc extends It6_WsWrapper_Abstract {

	private $xmlRpcClient;

	public function __construct($entityName, $arguments, &$extensions = array()) {
		parent::__construct($entityName, $extensions);
		$this->xmlRpcClient = $arguments[1];
	}

	protected function _callWebservice($name, $arguments) {
		if (Zend_Registry::isRegistered('acl'))
			Zend_Registry::get('acl')->setHttpAuthForWs($this->xmlRpcClient->getHttpClient());
		$arguments = $this->processArguments($arguments);
		$ret = $this->xmlRpcClient->call($this->_entityName . '.' . $name, $arguments);
		return is_array($ret) ? new It6_ArrayWrapper($ret) : $ret;
	}

	protected function processArguments($arguments) {
		return It6_ArrayWrapper::toNativeArray($arguments);
	}

}
