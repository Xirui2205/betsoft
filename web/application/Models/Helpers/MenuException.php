<?php

class Models_Helpers_MenuException {

	/**
	 * list of menu exceptions
	 * array ( controller_convert.c_id => data )
	 */
	public static $exceptions = array(
		 2 => true,
		 4 => true,
		 5 => true,
		 6 => true,
		 7 => true,
		 9 => true,
		 45 => true,
	);

	public static function isMenuException($cId) {
		return array_key_exists($cId, self::$exceptions);
	}
}