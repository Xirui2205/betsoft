<?php

class Url {

private $uri;
private $params;
private $host;
private $protocol; // eg. 'http' or 'https'

public function __construct($uri = null, array $params = null, $host = null, $protocol = null) {
	$this->uri = $uri;
	$this->params = (empty($params) ? array() : $params);
	$this->host = $host;
	$this->protocol = $protocol;
}

public function setUri($uri) {
	$this->uri = $uri;
}

public function getUri() {
	return $this->uri;
}

public function setParams(array $params) {
	$this->params = $params;
}

public function getParams() {
	return $this->params;
}

public function addParam($name, $value, $overwrite = false) {
	if (!array_key_exists($name, $this->params) || $overwrite)
		$this->params[$name] = $value;
}

public function addParams(array $params, $overwrite = false) {
	foreach ($params as $name => $value)
		$this->addParam($name, $value, $overwrite);
}

public function getParam($name) {
	if (isset($this->params[$name]))
		$this->params[$name];
	else
		return null;
}

public function setHost($host) {
	$this->host = $host;
}

public function getHost() {
	return $this->host;
}

public function setProtocol($protocol) {
	$this->protocol = $protocol;
}

public function getProtocol() {
	return $this->protocol;
}

public function getUrl($asHtml = false, $additionalParams = array(), $includeParams = true, $overwriteParams = false) {
	$url = '';
	if (!empty($this->protocol))
		$url .= $this->protocol . '://';
	else if (!empty($this->host))
		$url .= ( !empty($_SERVER['HTTPS']) && (0 != strcasecmp('off', $_SERVER['HTTPS'])) ? 'https' : 'http' ) . '://';
	if (!empty($url) || !empty($this->host)) {
		if (empty($this->host))
			$url .= (empty($_SERVER['HTTP_HOST']) ? 'localhost' : $_SERVER['HTTP_HOST']);
		else
			$url .= $this->host;
	}
	if (!empty($url)) {
		if (  ( !empty($this->uri) && ('/' != substr($this->uri, 0, 1)) )
			|| (empty($this->uri) && !empty($this->params))  )
			$url .= '/';
	}
	if (!empty($this->uri))
		$url .= $this->uri;
	if ($includeParams) {
		if ($overwriteParams)
			$params = array_merge($this->params, $additionalParams);
		else
			$params = array_merge($additionalParams, $this->params);
		if (!empty($params)) {
			$url .= '?';
			$first = true;
			foreach ($params as $name => $value) {
				if ($first)
					$first = false;
				else
					$url .= '&';
				$url .= urlencode($name) . '=' . urlencode($value);
			}
		}
	}
	return ($asHtml ? htmlspecialchars($url) : $url);
}

public function getXhtmlFormFields(array $additionalParams = array(), $overwriteParams = false) {
	$ret = '';
	if ($overwriteParams)
		$params = array_merge($this->params, $additionalParams);
	else
		$params = array_merge($additionalParams, $this->params);
	if (!empty($params)) {
		foreach ($params as $name => $value)
			$ret .= '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />';
	}
	return $ret;
}

}
