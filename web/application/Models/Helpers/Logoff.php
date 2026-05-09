<?php

class Models_Helpers_Logoff {

	public static function off(){

		if($GLOBALS['ses_status'] != 2 && $GLOBALS['ses_status'] == 3) return;

		try {
			if (Zend_Registry::isRegistered('user_id')) {
				$user_id = Zend_Registry::get('user_id');
				
				if (isset($user_id)) 
					$select = Zend_Registry::get('dbSes')->delete('session','user_id='.$user_id);
			
				$GLOBALS['ses_status'] = 1;
			}
		}  catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}

}
