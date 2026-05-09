<?php
class It6_XmlRpc_Server extends Zend_XmlRpc_Server {
	public function __construct() {
		parent::__construct();
		Zend_XmlRpc_Server_Fault::attachFaultException('It6_XmlRpc_Exception');
		Zend_XmlRpc_Server_Fault::attachFaultException('It6_XmlRpc_HostException');
		$this->setResponseClass('It6_XmlRpc_Response');
	}
	
	public function setClasses($classes) {
		foreach ( $classes as $class => $v ) {
			$namespace = $v[0];
			$methods = $v[1];
			$dispatchable = Zend_Server_Reflection::reflectClass(
				$class, null, $namespace);
			foreach ($dispatchable->getMethods() as $method) {
				if ( in_array($method->getName(), $methods) ) {
					$this->_buildSignature($method, $class);
				}
			}
		}
		
	}

	public static function outputFault(Exception $e, $code = null, $asString = false) {
		if (!isset($code))
			$fault = new Zend_XmlRpc_Server_Fault($e);
		else
			$fault = new Zend_XmlRpc_Fault($code, $e->getMessage());
		$str = $fault->saveXml();
		if ($asString)
			return $str;
		else {
			echo $str;
			return;
		}
	}
}