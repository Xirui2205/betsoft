<?php

require_once(dirname(__FILE__) . '/FormatFloat.php');

class It6_View_Helper_FormatRate extends It6_View_Helper_FormatFloat {

	public function formatRate($value) {
		return self::formatRateStatic($value);
	}

	static function formatRateStatic($value) {
		//return self::formatFloatStatic($value, 2);
		return (is_numeric($value) ? sprintf('%.2f', floatval($value)) : 'NaN');
	}
}
