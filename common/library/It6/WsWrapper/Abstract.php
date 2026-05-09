<?php
abstract class It6_WsWrapper_Abstract {
	protected $_entityName;
	protected $_extensions;

	public function __construct($entityName, &$extensions = array()) {
		$this->_entityName = $entityName;
		$this->_extensions = &$extensions;
	}

	protected function _addExtensionsToArguments(&$arguments) {
		$addArgs = array();
		if (!empty($this->_extensions)) foreach ($this->_extensions as $extension) {
			$extArgs = $extension->getWsCallArguments();
			if (!empty($extArgs))
				$addArgs[] = $extArgs;
		}
		if (!empty($addArgs))
			$arguments[] = $addArgs;
	}

	abstract protected function _callWebservice($name, $arguments);

	protected function _updateExtensionsFromResult($result) {
		$ret = true;
		if (!empty($this->_extensions)) foreach ($this->_extensions as $extension) {
			if (!$extension->handleWsResponse($result))
				$ret = false;
		}
		return $ret;
	}

	public function __call($name, $arguments) {
		$this->_addExtensionsToArguments($arguments);
		$result = $this->_callWebservice($name, $arguments);
		$this->_updateExtensionsFromResult($result);
		if (is_array($result) || $result instanceof It6_ArrayWrapper)
			unset($result[It6_WsExtension::KEY_EXTENSIONS]);
		$empty = array();
		$this->extensions = &$empty;
		return $result;
	}
}
