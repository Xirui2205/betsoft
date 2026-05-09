<?php

require_once(dirname(__FILE__) . '/FormatFloat.php');

class It6_View_Helper_FormatCurrency extends It6_View_Helper_FormatFloat {

	
	/**
	 * @param float|integer $value The amount to be formatted
	 * @param string $currency The currency abbrivation
	 * @param string $type Specifies ow to format the currncy. take parameters:
	 * <ul>
	 * <li>html</li>
	 * <li>text</li>
	 * </ul>
	 * @return string The formatted amount with currency
	 */
	public function formatCurrency($value, $currency = '', $type='html') {
		return static::formatCurrencyStatic($value, $currency, $this->view->currencyPrecision, $type);
	}

	
	/**
	 * @param float|integer $value The amount to be formatted
	 * @param string $currency The currency abbrivation
	 * $param integer $currencyPrecision The float prcision for the amoun
	 * @param string $type Specifies ow to format the currncy. take parameters:
	 * <ul>
	 * <li>html</li>
	 * <li>text</li>
	 * </ul>
	 * @return string The formatted amount with currency
	 */
	public static function formatCurrencyStatic($value, $currency = '', $currencyPrecision = 2, $type='html') {
		$str = static::formatFloatStatic($value, $currencyPrecision);
		if (!empty($currency))
			if($type == 'html')
				$str .= '&nbsp;' . $currency;
			elseif($type == 'text')
				$str .= ' ' . $currency;
		return $str;
	}
}
