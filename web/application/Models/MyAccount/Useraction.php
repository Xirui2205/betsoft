<?php

class Models_MyAccount_Useraction {


	public static function getPrompt($actionHash) {
		$action = Zend_Registry::get('ws')->UserAction->getByHash($actionHash);
		$action = It6_ArrayWrapper::toNativeArray($action);

		if(!empty($action))
			return 'user_action_prompt_'.$action['actionType'];
		else
			return false;
	}
}
