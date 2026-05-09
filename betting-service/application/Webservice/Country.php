<?php

/**
 * Branch type related static methods.
 * @author Martin Bohal
 * @see Entities_Country
 */
class Webservice_Country extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_Country";
	public static $TABLE		= "zeme";
	public static $TABLE_PREFIX	= "ctr";
	public static $IDENTITY		= "zeme_id";
	public static $TABLE_CURRENCY			= "mena";
	public static $TABLE_CURRENCY_PREFIX	= "mn";

	public static $CONV = array(
		'zeme_id'		=> 'countryId',
		//FIXME:
		//Commented out by Martin as I do not know what they are and what they should be called.When someone needs them they can uncomment them and come up with some sense making name.
		//Same thing has to be done in Entities_Country
		//'zeme_T'		=> 'name',
		//'zeme_TA'		=> '',
		//'zeme_ADM'	=> '',
		'zeme_allow'	=> 'isActive',
		//TODO Add multi languiages support
		'TRANSLATE(nazev,1)' => 'name', // 1 .. CZ
		'zobrazeno'		=> 'isShown',
		'kod'			=> 'code',
		'predvolba'		=> 'callingCode',
		'mn.mena_text'		=> 'currency',
		'mn.mena_id'		=> 'currencyId'
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->join(
				array(self::$TABLE_CURRENCY_PREFIX => self::$TABLE_CURRENCY),
				self::$TABLE_CURRENCY_PREFIX.'.mena_id = '.self::$TABLE_PREFIX.'.mena_id',
				null
			);
	}
	
	/**
	 * Returns all branch types
	 * @return struc structure of the possible types of branch
	 */
	public static function getAll($extensions = null) {

		return parent::getAll($extensions);
	}



	/**
	 * Returns type with given Id
	 * @param array $typeId id of the type we need info about
	 * @return struc structure of the type with the given id
	 */
	public static function getById($typeId, $extensions = null){

		return parent::getById($typeId, $extensions);
	}
	
	/**
	 * Zakazané země, tzn. atributem zeme_allow (isActive) = 0
	 * @return struc
	 */
	public static function getForbiddenCountries(){
		return static::getAllWhere(array("isActive = 0"));
	}
	
	public static function getForbiddenCountryCodes(){
		$countries = self::getForbiddenCountries();
		$countryCodes = array();
		if(!empty($countries)) foreach($countries as $country)
			$countryCodes[] = $country['code'];
		return $countryCodes;
	}
}
