<?php

class ProviderRegistrationEmail extends It6_Cron_Job_Email_ControllerAbstract {

	const COMPANY_NAME				= 'companyName';
	const COMPANY_ADDRESS			= 'companyAddress';
	const COMPANY_REPRESENTATIVE	= 'companyRepresentative';
	const COMPANY_PHONE				= 'companyPhone';
	const COMPANY_MOBILE			= 'companyMobile';
	const COMPANY_FAX				= 'companyFax';
	const COMPANY_EMAIL				= 'companyEmail';
	const COMPANY_DIC				= 'companyDic';
	const COMPANY_IC				= 'companyIc';

	const LOCATION_NAME		= 'locationName';
	const LOCATION_ADDRESS	= 'locationAddress';
	const LOCATION_TYPE		= 'locationType';
	const LOCATION_PHONE	= 'locationPhone';
	const LOCATION_MOBILE	= 'locationMobile';
	const LOCATION_FAX		= 'locationFax';
	const LOCATION_EMAIL	= 'locationEmail';

	const OPENING_HOURS_MONDAY		= 'openingHoursMon';
	const OPENING_HOURS_TUESDAY		= 'openingHoursTue';
	const OPENING_HOURS_WEDNESDAY	= 'openingHoursWed';
	const OPENING_HOURS_THURSDAY	= 'openingHoursThu';
	const OPENING_HOURS_FRIDAY		= 'openingHoursFri';
	const OPENING_HOURS_SATUDAY		= 'openingHoursSat';
	const OPENING_HOURS_SUNDAY		= 'openingHoursSun';



	public function prepareEmail() {
		parent::prepareEmail();

		$this->companyName				= $this->params[self::COMPANY_NAME];
		$this->companyAddress			= $this->params[self::COMPANY_ADDRESS];
		$this->companyRepresentative	= $this->params[self::COMPANY_REPRESENTATIVE];
		$this->companyPhone				= $this->params[self::COMPANY_PHONE];
		$this->companyMobile			= $this->params[self::COMPANY_MOBILE];
		$this->companyFax				= $this->params[self::COMPANY_FAX];
		$this->companyEmail				= $this->params[self::COMPANY_EMAIL];
		$this->companyDic				= $this->params[self::COMPANY_DIC];
		$this->companyIc				= $this->params[self::COMPANY_IC];

		$this->locationName		= $this->params[self::LOCATION_NAME];
		$this->locationAddress	= $this->params[self::LOCATION_ADDRESS];
		$this->locationType		= $this->params[self::LOCATION_TYPE];
		$this->locationPhone	= $this->params[self::LOCATION_PHONE];
		$this->locationMobile	= $this->params[self::LOCATION_MOBILE];
		$this->locationFax		= $this->params[self::LOCATION_FAX];
		$this->locationEmail	= $this->params[self::LOCATION_EMAIL];

		$this->openingHoursMon	= $this->params[self::OPENING_HOURS_MONDAY];
		$this->openingHoursTue	= $this->params[self::OPENING_HOURS_TUESDAY];
		$this->openingHoursWed	= $this->params[self::OPENING_HOURS_WEDNESDAY];
		$this->openingHoursThu	= $this->params[self::OPENING_HOURS_THURSDAY];
		$this->openingHoursFri	= $this->params[self::OPENING_HOURS_FRIDAY];
		$this->openingHoursSat	= $this->params[self::OPENING_HOURS_SATUDAY];
		$this->openingHoursSun	= $this->params[self::OPENING_HOURS_SUNDAY];
	}


	public function getToField() {
		$emails = Zend_Registry::get('ws')->Parameter->getGlobalParameter('provider.registration.to');
		return explode(',', $emails);
	}

	public function getBodyHtml($layoutScript=null) {
		return parent::getBodyHtml('plain');
	}
}
