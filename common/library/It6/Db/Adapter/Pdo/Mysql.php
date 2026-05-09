<?php

class It6_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql {

protected function _connect() {

	if ($this->_connection)
		return;

	$charsetOrig = false;
	$charset = 'utf8';
	if (!empty($this->_config['charset'])) {
		$charsetOrig = $charset = $this->_config['charset'];
		unset($this->_config['charset']); // hack to avoid parent's behavior when this config option is set
	}
 
	$timeZone = 'GMT';
	if (!empty($this->_config['timeZone']))
		$timeZone = $this->_config['timeZone'];

	$this->_config['driver_options'][1002] = "SET NAMES '$charset', time_zone='$timeZone'"; // 1002 = PDO::MYSQL_ATTR_INIT_COMMAND

	parent::_connect();

	if (false !== $charsetOrig)
		$this->_config['charset'] = $charsetOrig; // restore hacked option
}

} // class It6_Db_Adapter_Pdo_Mysql