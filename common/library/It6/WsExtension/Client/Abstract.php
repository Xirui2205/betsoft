<?php
//require_once 'It6/WsExtension.php';

abstract class It6_WsExtension_Client_Abstract extends It6_WsExtension {

	protected $_namespace = 'It6_WsExtension_Client_';
	protected $_id = null;
	protected $_params = array();
	protected $_responseParams = array();
	protected $_response = array();

	/**
	 * @param string $id ID of exntesion instance
	 * @param array $params name-value pairs of parameters for WS call
	 * @param array $responseParams names of response parameters that will be read from WS response
	 */
	public function __construct($id, $params = array(), $responseParams = array()) {
		$this->_id = $id;
		$this->_params = $params;
		$this->_responseParams = $responseParams;
	}

	/**
	 * @param string $name Name of request parameter/response field
	 * @return mixed values of (with priority in order) response field or request param or null if not found
	 */
	public function __get($name) {
		if (array_key_exists($name, $this->_response))
			return $this->_response[$name];
		else if (array_key_exists($name, $this->_params))
			return $this->_params[$name];
		else 
			return null;
	}

	public function getParam($name) {
		return (array_key_exists($name, $this->_params) ? $this->_params[$name] : null);
	}

	public function setParam($name, $value) {
		$this->_params[$name] = $value;
	}

	public function getResponse($name = null) {
		if (!isset($name))
			return $this->_response;
		else
			return (array_key_exists($name, $this->_response) ? $this->_response[$name] : null);
	}

	protected function _getClassName() {
		$class = get_class($this);
		if (0 == strpos($class, $this->_namespace))
			return substr($class, strlen($this->_namespace));
		else
			return $class;
	}

	protected function _getWsCallParams() {
		return $this->_params;
	}

	/**
	 * @return array with extension data to be added to WS call arguments
	 */
	public function getWsCallArguments() {
		return array(
			static::PARAM_CLASS => $this->_getClassName(),
			static::PARAM_ID => $this->_id,
			static::PARAM_PARAMS => $this->_getWsCallParams(),	
		);
	}

	protected function _handleWsResult(&$result) {
		foreach ($this->_responseParams as $name) {
			if (isset($result[$name]))
				$this->_response[$name] = $result[$name];
		}
		return true;
	}

	public function handleWsResponse(&$response) {
		$ret = true;
		$this->_response = array();
		if ( (is_array($response) || $response instanceof It6_ArrayWrapper)
			&& isset($response[It6_WsExtension::KEY_EXTENSIONS])) {
			foreach ($response[It6_WsExtension::KEY_EXTENSIONS] as $id => $result) {
				if ($this->_id == $id) {
					if (!$this->_handleWsResult($result))
						$ret = false;
					$response[static::KEY_EXTENSIONS] = $result;
				}
			}
		}
		return $ret;
	}
}
