<?php

abstract class It6_Cron_Job_Sms_Abstract {
	
	protected $_db;
	protected $_ws;

	public function init() {
		$this->_db = Zend_Registry::get('db');
		$this->_ws = Zend_Registry::get('ws'); 
	}

	protected function getType() {
		return get_called_class();
	}

	protected function formatRate($value) {
		return It6_View_Helper_FormatRate::formatRateStatic($value);
	}

	protected function formatCurrency($value, $currency = '', $precision = 2, $type='text') {
		return It6_View_Helper_FormatCurrency::formatCurrencyStatic($value, $currency, $precision, $type);
	}
}
