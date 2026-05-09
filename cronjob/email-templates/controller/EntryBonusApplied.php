<?php

class EntryBonusApplied extends It6_Cron_Job_Email_UserEmail {

	public function prepareEmail(){
		$userId = $this->params[static::PARAM_USER_ID];
		$bonus = Zend_Registry::get('ws')->Campaign->getEntryBonusInfo($userId);

		if (empty($bonus) || empty($bonus['from']) || empty($bonus['applied']))
			throw new Exception('Tryied to notify user about applied entry bonus, but bonus was not applied. userId=' . $userId);
		else{
			parent::prepareEmail();
			$this->bonus = $bonus;
		}
		return true;
	}

}
