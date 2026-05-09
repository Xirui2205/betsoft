<?php

abstract class It6_Cron_Job_Abstract implements It6_Cron_Job {

protected $params = array();

public function __construct() {
}

public function getParamAsArray($paramName) {
	if (!array_key_exists($paramName, $this->params))
		return array();
	if (is_array($this->params[$paramName]))
		return $this->params[$paramName];
	else
		return array($this->params[$paramName]);
}

public function execute(array $params = array(), &$errorMessage = null) {
	$this->params = $params;
	return 0;
}

} // class
