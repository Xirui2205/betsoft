<?php

class It6_View_Helper_FormatFloat extends Zend_View_Helper_Abstract {

	public static function formatFloatStatic($value, $precision = null) {
		if (!is_numeric($value))
			return 'NaN';
		if (!isset($precision))
			$precision = 2;
		$options = array('precision' => $precision);
		if (Zend_Registry::isRegistered('Zend_Locale'))
			$options['locale'] = Zend_Registry::get('Zend_Locale');
		return Zend_Locale_Format::toFloat($value, $options);
	}


	public function formatFloat($value, $precision=null){
		return self::formatFloatStatic($value, $precision);
	}
}
