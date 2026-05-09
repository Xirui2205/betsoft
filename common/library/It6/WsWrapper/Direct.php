<?php
class It6_WsWrapper_Direct extends It6_WsWrapper_Abstract {

	public function __construct($entityName, $arguments, &$extensions = array()) {
		parent::__construct($entityName, $extensions);
	}

	protected function _callWebservice($name, $arguments) {
		$ret = call_user_func_array('Webservice_' . $this->_entityName . '::' . $name, $arguments);
		return is_array($ret) ? new It6_ArrayWrapper($ret) : $ret;
	}

}
