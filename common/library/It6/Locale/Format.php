<?php

class It6_Locale_Format extends Zend_Locale_Format {

	// used in strtr with all its specifics
	protected static $_formatTable = array(
		'cs' => array(
			'd' => 'D',
			'm' => 'M',
			'y' => 'R',
			'Y' => 'R',
		),
	);



	public static function getLocalizedFormat($format, $langIso = null) {
		if (!isset($langIso))
			$langIso = (empty($_SESSION['lang']) ? DEFAULT_LANG : $_SESSION['lang']);
		if (empty(static::$_formatTable[$langIso]))
			return $format;
		return strtr($format, static::$_formatTable[$langIso]);
	}



	public static function getNumber($number, array $options=array()) {
		if(empty($options['locale']))
			$options['locale'] = Zend_Registry::get('Zend_Locale');

		if(!empty($number))
			$number = parent::getNumber($number, $options);

		return $number;
	}



	public static function toNumber($number, array $options=array()) {
		if(empty($options['locale']))
			$options['locale'] = Zend_Registry::get('Zend_Locale');

		if(!empty($number))
			$number = parent::toNumber($number, $options);

		return $number;
	}
} // class
