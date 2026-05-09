<?php

/**
 * Branch type dao.
 * @author Martin Bohal
 * @see Webservice_Country
 */
class Entities_Country extends Entities_AbstractEntity {

	/**
	 * id of the country
	 * @var int
	 */
	public $countryId;

	//FIXME:
	//Commented out by Martin as I do not know what they are and what they should be called.When someone needs them they can uncomment them and come up with some sense making name.
	//Same thing has to be done in Webservice_Country
	//public $zeme_T;
	//public $zeme_TA;
	//public $zeme_ADM;

	/**
	 * indicates active / not active
	 * @var string
	 */
	public $isActive;

	/**
	 * name of the country
	 * @var string
	 */
	public $name;

	/**
	 * indicates shown / not shown
	 * @var boolean
	 */
	public $isShown;

	/**
	 * Country code fo the country
	 * @var string
	 */
	public $code;
}
