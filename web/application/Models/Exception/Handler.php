<?php

/*
 Description
 * @author
 * @date
 * @copyright
 * @version
 * @link


 */

class Models_Exception_Handler {


	/**
	 * zakladni handler
	 * @param string $text
	 * @param int $type
	 * @return void
	 */
	public static function handle($text,$type=0) {
		$array = debug_backtrace();
		It6_Log::err(
			$text,
			It6_Log::TAG_WEB,
			array('type' => $type),
			null,
			$array[1]['file'],
			$array[1]['line']);

	}


}