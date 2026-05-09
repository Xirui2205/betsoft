<?php
class It6_Validate_Float extends Zend_Validate_Float {

const MSG_MINIMUM = 'msgMinimum';
const MSG_MAXIMUM = 'msgMaximum';
public $minimum = false;
public $maximum = false;
public $precision = false;

public function __construct($cfg = null)  {
	parent::__construct($cfg);
	if ($cfg instanceof Zend_Config)
		$cfg = $cfg->toArray();
	if (is_array($cfg)) {
		if (isset($cfg['min']) && false !== $cfg['min'])
			$this->minimum = $cfg['min'];
		if (isset($cfg['max']) && false !== $cfg['max'])
			$this->maximum = $cfg['max'];
		if (isset($cfg['precision']) && false !== $cfg['precision'])
			$this->precision = $cfg['precision'];
	}
	$this->_messageVariables['min'] = 'minimum';
	$this->_messageVariables['max'] = 'maximum';
	$this->_messageTemplates[self::MSG_MINIMUM] = "'%value%' must be at least '%min%'";
	$this->_messageTemplates[self::MSG_MAXIMUM] = "'%value%' must be no more than '%max%'";
}

public function getLocale() {
	if (!empty($this->_locale))
		return $this->_locale;
	else
		return self::getAppLocale();
}

public static function getAppLocale() {
	if (Zend_Registry::isRegistered('Zend_Locale'))
		return Zend_Registry::get('Zend_Locale');
	else
		return new Zend_Locale(DEFAULT_LOCALE);
}
/**
 * Locale aware formating function, that produces string parseable by this validator
 */
public static function formatFloat($number, $decimals = false) {
	$options = array('locale' => self::getAppLocale());
	if (false !== $decimals)
		$options['precision'] = $decimals;
	return Zend_Locale_Format::toFloat($number, $options);
//	$locale = localeconv();
//	return number_format($number, $decimals, $locale['decimal_point'], $locale['thousands_sep']);
}

/**
 * Locale aware parsing of float (thousands grouping, decimal point)
 * @param options locale, precision
 * @see Zend_Locale_Format::getFloat()
 */
public static function parseFloat($str, $options = array()) {
	try {
		$str = preg_replace('/\\s+/', '', $str);
		if (empty($options['locale']))
			$options['locale'] = self::getAppLocale();
		return Zend_Locale_Format::getFloat($str, $options);
	}
	catch (Exception $e) {
		return false;
	}
}

public function isValid($value){
	$this->_setValue($value);

	try {
		$float = static::parseFloat($value, array('locale' => $this->getLocale()));
		if (false === $float) {
			$this->_error(parent::NOT_FLOAT);
			return false;
		}
	} catch (Zend_Locale_Exception $e) {
		$this->_error(parent::NOT_FLOAT);
		return false;
	}

	if (false !== $this->minimum && $float < $this->minimum) {
		$this->_error(self::MSG_MINIMUM);
		return false;
	}

	if (false !== $this->maximum && $float > $this->maximum) {
		$this->_error(self::MSG_MAXIMUM);
		return false;
	}

	return true;
}

}
