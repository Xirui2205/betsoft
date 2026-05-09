<?php
	class It6_WebServiceEntityDirectWrapper {
	
		private $entityName;
		
	
		public function __construct($entityName, $arguments) {
			$this->entityName = $entityName;
		}
	
		public function __call($name, $arguments) {
			return call_user_func_array('Webservice_' . $this->entityName . '::' . $name, $arguments);
			//return $this->xmlRpcClient->call($this->entityName . '.' . $name, $arguments);
		}
	}
