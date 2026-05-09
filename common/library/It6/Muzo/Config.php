<?php

class It6_Muzo_Config {
	public $defaultLang;
	public $merchantNumber = null;
	public $urlForRequest;
	public $urlForResponse;
	public $certificateFile;
	public $privateKeyFile;
	public $privateKeyPassword;

	private $names = array(
		'defaultLang', 'merchantNumber', 'urlForRequest', 'urlForResponse', 'certificateFile', 'privateKeyFile', 'privateKeyPassword'
	);

	public function __construct($params = null) {
		if (is_array($params)) {
			foreach ($this->names as $name) {
				if (!empty($params[$name]))
					$this->$name = $params[$name];
			}
		}
	}

/*
	public function readXml($xml, $isFile = false) {
		if ($isFile)
			$doc = new SimpleXMLElement($xml, null, true);
		else
			$doc = new SimpleXMLElement($xml);

		foreach ($this->names as $name) {
			$nodes = $doc->xpath($name . '/text()');
			if (!empty($nodes))
				$this->$name = (string)$nodes[0];
		}
	}
*/

	public function isValid(&$errorMessages = null) {
		$messages = array();
		$result = true;
		if (isset($this->merchantNumber) && empty($this->merchantNumber)) {
			$result = false;
			$errorMessages[] = 'Invalid merchant number';
		}
		if (1 != preg_match('!^https://.+!', $this->urlForRequest)) {
			$result = false;
			$errorMessages[] = 'Invalid URL for request (must use https:// protocol)';
		}
		if (empty($this->urlForResponse)) {
			$result = false;
			$errorMessages[] = 'URL(s) for response not specified';
		}
		else if (is_array($this->urlForResponse)) {
			if (empty($this->defaultLang)) {
				$result = false;
				$errorMessages[] = 'Default language not specified';
			}
			else if (!array_key_exists($this->defaultLang, $this->urlForResponse)) {
				$result = false;
				$errorMessages[] = 'URL(s) for response not specified';
			}
			foreach ($this->urlForResponse as $lang => $url) {
				if (1 != preg_match('!^https://.+!', $url)) {
					$result = false;
					$errorMessages[] = 'Invalid URL(s) for response found (must use https:// protocol)';
				}
			}
		}
		else {
			if (1 != preg_match('!^https://.+!', $this->urlForResponse)) {
				$result = false;
				$errorMessages[] = 'Invalid URL for response (must use https:// protocol)';
			}
		}
		foreach (
			array('Certificate' => $this->certificateFile, 'Private key' => $this->privateKeyFile)
			as $name => $path
		) {
			if (empty($path)) {
				$result = false;
				$errorMessages[] = $name . ' file not specified';
			}
			else if (!file_exists($path)) {
				$result = false;
				$errorMessages[] = $name . ' file not found at "' . $path . '"';
			}
		}
		if (empty($this->privateKeyPassword)) {
			$result = false;
			$errorMessages[] = 'Private key password not specified';
		}
		return $result;
	}
}
