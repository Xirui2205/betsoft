<?php

class It6_Log_Filter_Container extends Zend_Log_Filter_Abstract {

	protected $_filters = null;

	public function __construct($filters) {
		$this->_filters = ( is_array($filters) ? $filters : array() );
	}

	static public function factory($config) {
		$config = self::_parseConfig($config);
		$filterConfigs = ( empty($config['filters']) ? array() : $config['filters']);
		$filters = array();
		foreach ($filterConfigs as $filterCfg) {
			$name = $filterCfg['filterName'];
			$params =  ( empty($filterCfg['filterParams']) ? 'null' : $filterCfg['filterParams'] );
			$namespace = ( empty($filterCfg['filterNamespace']) ? 'Zend_Log_Filter' : $filterCfg['filterNamespace'] );
			$class = $namespace . '_' . $name;
			$filters[] = $class::factory($params);
		}
		return new self($filters);
	}

	public function accept($event) {
		if (!is_array($this->_filters))
			return true;
		foreach ($this->_filters as $filter) {
			if (!$filter->accept($event))
				return false;
		}
		return true;
	}

}
