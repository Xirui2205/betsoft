<?php

class Webservice_SeoUrl_Event  extends Webservice_SeoUrl {

	//as secified in vic_main.seo_url in the comment for the column type
	protected static $TYPE_ID = 3;
	
	protected static $CONV			= array();
	protected static $ENTITY_NAME	= "Entities_SeoUrl_Event";

	public static function init() {
		self::$CONV = array_merge(
			parent::$CONV,
			array(
				'ev.sport_id'	=> 'sportId',
				'ev.oblast_id'	=> 'regionId',
				'ev.nazev'		=> 'key',
			)
		);
	}



	protected static function defaultJoins($query) {

		$query = parent::defaultJoins($query);
		$query
			->join(
				array(Webservice_Event::$TABLE_PREFIX => Webservice_Event::$TABLE),
				self::$TABLE_PREFIX.'.event_id = '.Webservice_Event::$TABLE_PREFIX.'.udalost_id',
				null
			);

		return $query;
	}



	/**
	 * Returns all seoUrls in the system
	 * @return array array of the seoUrl structures
	 * @see Entities_SeoUrl_Event
	 */
	public static function getAll($extensions = null) {
		self::init();
		
		return parent::getAll($extensions);
	}



	/**
	 * Inserts a new seoUrl into the system
	 * @param struct $insert structure with the data to insert
	 * @return boolean
	 * @see Entities_SeoUrl_Event
	 */
	public static function insert($seoUrl) {
		self::init();
		
		parent::insert($seoUrl);
		
		It6_GlobalCache_Invalidator::invalidateSportsbook();
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
	}



	/**
	 * Update the seoUrl in the system
	 * @param struct $insert structure with the data to insert
	 * @return boolean
	 * @see Entities_SeoUrl_Event
	 */
	public static function update($seoUrl) {
		self::init();
		
		parent::update($seoUrl);
		
		It6_GlobalCache_Invalidator::invalidateSportsbook();
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
	}
}
