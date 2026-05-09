<?php

/**
 * Offer category related static methods.
 * @see Entities_Stem
 */
class Webservice_OfferCategory extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_OfferCategory";
	public static $TABLE		= "offer_category";
	public static $TABLE_PREFIX	= "oc";
	public static $IDENTITY		= "id";

	public static $CONV = array(
		'id'		=> 'offerCategoryId',
		'name'		=> 'name'
	);


	/**
	 * Returns all offer categories
	 * @return struc structure of the category offer
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}
}
