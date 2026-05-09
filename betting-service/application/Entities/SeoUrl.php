<?php

/**
 * @see Webservice_SeoUrl
 * 
 */
class Entities_SeoUrl extends Entities_AbstractEntity {
	
	/**
	 * Language of the translation.
	 * @var integer
	 */
	public $langId;

	/**
	 * Type of the url resource:
	 *   1:sport;
	 *   2:oblast;
	 *   3:udalost;
	 *   4:druh;
	 *   5:hry;
	 *   6:tymy
	 * @var integer
	 */
	public $typeId;
	
	/**
	 * The url
	 * @var string
	 */
	public $url;
	
	/**
	 * The identifierof the object of the kind defined by $typeId
	 * @var integer 
	 */
	public $objectId;
}
