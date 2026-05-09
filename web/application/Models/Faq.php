<?php

/*
 Description
 * @author     tkbc.com
 * @date       25.7.2009
 * @copyright  TKBC
 * @version    1.0
 * @link       http://tkbc.cz


 */


class Models_Faq{


	/**
	 * pole textu a pismen
	 * @access private
	 * @var array
	 */
	public static $data = '';

	 
	/**
	 * Vraci aktivni pismena
	 * @return array
	 */
	public static function data($type){

			
		$translate = Zend_Registry::get('translate');


		self::$data .= $translate['faq_'. $type];
		 
		 

		if(mb_strlen(self::$data) ==  0) self::$data = $translate['faq_nodata'];

		return self::$data;

	}






}
