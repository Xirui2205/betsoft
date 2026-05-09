<?php

class EntryBonusTimeout extends It6_Cron_Job_Email_UserEmail {

	public function prepareEmail(){
		$userId = $this->params[static::PARAM_USER_ID];
		$bonus = Zend_Registry::get('ws')->Campaign->getEntryBonusInfo($userId);

		if (empty($bonus) || empty($bonus['from']) || !isset($bonus['applied']) || 0 != !isset($bonus['applied']))
			throw new Exception('Tryied to notify user about entry bonus timeout, but bonus was not applied with zero amount. userId=' . $userId);
		else{
			parent::prepareEmail();
			$this->bonus = $bonus;
		}
		return true;
	}

}
