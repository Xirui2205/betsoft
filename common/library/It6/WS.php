<?php
class It6_WS {

	const XML_RPC = 'XmlRpc';
	const DIRECT = 'Direct';

	public $entityWrapperPool;
	public $type;
	public $arguments;
	private $extensions = array();

	public function __construct($type) {
		$this->type = $type;
		$this->entityWrapperPool = array();
		$this->arguments = func_get_args();
	}

	public function __get($name) {
		//TODO Memoize thru $this->entityWrapperPool.
		$class = 'It6_WsWrapper_' . $this->type;
		$result = new $class($name, $this->arguments, $this->extensions);
		$empty = array();
		$this->extensions = &$empty;
		return $result;
	}

	public function ext(&$extensions = array()) {
		$this->extensions = &$extensions;
		return $this;
	}

}
