<?php

class It6_Log_Writer_Stream extends Zend_Log_Writer_Stream {

	public function __construct($streamOrUrl, $mode = 'a', $format = null) {
		parent::__construct($streamOrUrl, $mode);
		if ( !empty($format) )
			$this->_formatter = new Zend_Log_Formatter_Simple($format);
	}

	static public function factory($config) {
		$config = self::_parseConfig($config);
		$config = array_merge(array(
			'stream' => null, 
			'mode'   => null,
			'format' => null,
		), $config);

		$streamOrUrl = isset($config['url']) ? $config['url'] : strftime($config['stream']); 

		return new self(
			$streamOrUrl, 
			$config['mode'],
			$config['format']
		);
	}

	protected function _write($event) {
		if (array_key_exists('timestamp', $event) && is_numeric($event['timestamp']))
			$event['timestamp'] = It6_Date::timestampToDateTime(intval($event['timestamp']));
		parent::_write($event);
	}

}