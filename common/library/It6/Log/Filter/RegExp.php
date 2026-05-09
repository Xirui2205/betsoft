<?php

class It6_Log_Filter_RegExp extends Zend_Log_Filter_Abstract {

	protected $patterns = null;
	protected $filterUnmatched = false;

	public function __construct($pattern, $filterUnmatched) {
		if (!empty($pattern)) {
			if (!is_array($pattern))
				$pattern = array($pattern);
			$this->patterns = $pattern;
		}
		if ($filterUnmatched)
			$this->filterUnmatched = true;
	}

	static public function factory($config) {
		$config = self::_parseConfig($config);
		$pattern = ( empty($config['pattern']) ? array() : $config['pattern'] );
		$filterUnmatched = ( array_key_exists('unmatched', $config) && $params['unmatched'] );
		return new self($pattern, $filterUnmatched);
	}

	public function accept($event) {
		if (empty($this->patterns))
			return true;
		foreach ($this->patterns as $pattern) {
			$matched = (1 == preg_match($pattern, $event['message']));
			if ($matched == !$this->filterUnmatched)
				return false;
		}
		return true;
	}

}
