<?php

require_once(dirname(__FILE__) . '/FormatFloat.php');

class It6_View_Helper_FormatCurrencyPrecision extends It6_View_Helper_FormatFloat {

	
	/**
	 * @param float|integer $value The amount to be formatted
	 * @param string $currency The currency abbrivation
	 * @param int $currencyPrecision precision
	 * @param string $type Specifies ow to format the currncy. take parameters:
	 * <ul>
	 * <li>html</li>
	 * <li>text</li>
	 * </ul>
	 * @return string The formatted amount with currency
	 */


	public  function formatCurrencyPrecision($value, $currency = '', $currencyPrecision = 2, $type='html') {
		$str = static::formatFloatStatic($value, $currencyPrecision);
		if (!empty($currency))
			if($type == 'html')
				$str .= '&nbsp;' . $currency;
			elseif($type == 'text')
				$str .= ' ' . $currency;
		return $str;
	}
}
