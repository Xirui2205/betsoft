<?php
	class It6_WebServiceEntityXmlRpcWrapper {
	
		private $entityName;
		private $xmlRpcClient;
		
	
		public function __construct($entityName, $arguments) {
			$this->entityName = $entityName;
			$this->xmlRpcClient = $arguments[1];
		}
			
		
		public function __call($name, $arguments) {
			if (Zend_Registry::isRegistered('acl'))
				Zend_Registry::get('acl')->setHttpAuthForWs($this->xmlRpcClient->getHttpClient());
			$arguments = $this->processArguments($arguments);
			$ret = $this->xmlRpcClient->call($this->entityName . '.' . $name, $arguments);
			return is_array($ret) ? new It6_ArrayWrapper($ret) : $ret;
		}
		
		protected function processArguments($arguments) {
			return It6_ArrayWrapper::toNativeArray($arguments);

		}
	}
