<?php
class It6_XmlRpc_Response extends Zend_XmlRpc_Response_Http {
	
	public function setReturnValue($value, $type = null) {
		$value = It6_ArrayWrapper::toNativeArray($value);
		parent::setReturnValue($value, $type);
	}
}