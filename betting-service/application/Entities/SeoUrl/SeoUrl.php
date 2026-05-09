<?php

/**
 * @see Webservice_SeoUrl_Event
 * 
 */
class Entities_SeoUrl_Event extends Entities_SeoUrl {
	
	/**
	 * The sport of the event to which the url is tied
	 * @var integer
	 */
	public $sportId;

	/**
	 * The region of the event to which the url is tied
	 * @var integer
	 */
	public $regionId;
	
	/**
	 * The key defining the event to which the url is tied
	 * @var string
	 */
	public $key;
}
