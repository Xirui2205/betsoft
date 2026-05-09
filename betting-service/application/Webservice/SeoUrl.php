<?php

/**
 * SeoUrl related static methods.
 * @see Entities_SeoUrl
 *
 */
class Webservice_SeoUrl  extends Webservice_AbstractWebService {
	const TYPE_SPORT	= 1;
	const TYPE_REGION	= 2;
	const TYPE_EVENT	= 3;
	const TYPE_TYPE		= 4;
	const TYPE_GAME		= 5;
	const TYPE_TEAM		= 6;


	public static $TABLE		= 'seo_url';
	public static $TABLE_PREFIX	= 'su';


	protected static $ENTITY_NAME = 'Entities_SeoUrl';
	protected static $CONV = array(
		'lang_id'	=> 'langId',
		'type'		=> 'typeId',
		'url'		=> 'url',
		'event_id'	=> 'objectId',
	);



	/**
	 * Returns all seoUrls in the system
	 * @return array array of the seoUrl structures
	 * @see Entities_SeoUrl
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}



	/**
	 * Insert new seoUrl.
	 * @param struct $event structure of the event
	 * @return integer event identifier of the created event
	 * @see Entities_SeoUrl
	 */
	public static function insert($seoUrl) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$db->insert(
				static::$TABLE,
				array(
					'url' 		=> $seoUrl['url'],
					'lang_id'	=> $seoUrl['langId'],
					'type'		=> static::$TYPE_ID,
					'event_id'	=> $seoUrl['objectId'],
				)
			);
		
			It6_GlobalCache_Invalidator::invalidateSportsbook();
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
			
			It6_DbTransaction::commit($db);
			
			return true;
		
		}
		
		catch(Exception $e) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update seoUrl. (Entity: '".get_called_class()."')", 0, $e);
		}
	}



	/**
	 * Update seoUrl.
	 * @param struct $seoUrl structure of the event
	 * @return true on success
	 * @see Entities_SeoUrl
	 */
	public static function update($seoUrl) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		
		try {
			static::delete($seoUrl);
			static::insert($seoUrl);
		
			It6_DbTransaction::commit($db);
		
			return true;
		}
		
		catch(Exception $e) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update seoUrl. (Entity: '".get_called_class()."')", 0, $e);
		}
	}



	/**
	 * Delete event.
	 * @param struct $seoUrl idenetifier of the event.
	 * @return true on success
	 * @see Entities_SeoUrl
	 */
	public static function delete($seoUrl) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		
		try {
			$db->delete(
				static::$TABLE,
				array(
					'lang_id = ?'	=> $seoUrl['langId'],
					'type = ?'		=> static::$TYPE_ID,
					'event_id = ?'	=> $seoUrl['objectId'],
				)
			);

			It6_DbTransaction::commit($db);
			It6_GlobalCache_Invalidator::invalidateSportsbook();
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
			
			return true;
		}
		
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update seoUrl.", 0, $e);
		}
	}
	
	/**
	 * Get (translated) seo url by type
	 * @param int $type use constants from Webservice_SeoUrl
	 * @param int $eventId
	 * @param int $langId
	 * @return string seo url (one entry without slashes)
	 */
	public static function getByPrimaryKey($type, $eventId, $langId) {
		$seoUrl = static::getDb()->select()
				->from('seo_url', array('url'))
				->where('event_id = ?', $eventId)
				->where('type = ?', $type)
				->where('lang_id = ?', $langId)
				->query()->fetch();
		return str_replace('/', '', $seoUrl['url']);
	}
}
