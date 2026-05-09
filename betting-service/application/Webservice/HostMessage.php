<?php

/**
 * Host message related static methods.
 * @author Pavel Klinger
 * @see Entities_HostMessage
 */
class Webservice_HostMessage extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_HostMessage";
	public static $TABLE		= "host_message";
	//public static $TABLE_PREFIX	= "bm";
	public static $IDENTITY		= "id";

	public static $CONV = array(
		'id'       => 'id',
		'host_id'  => 'hostId',
		'message'  => 'message',
		'time'     => 'time',
		'read'      => 'read',
		'read_time' => 'readTime',
	);

	protected static function getDb() {
		return static::getAdminDb();
	}


	/**
	 * Returns all message
	 * @return struct object of the branch structures
	 * @see Entities_HostMessage
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * @param integer $hostId
	 * @reeturn string|boolean Cached JSON of messages or FALSE if there is nothing in cache
	 */
	public static function getNewByHostFromCache($hostId) {
		$key = static::getMessageCacheKey($hostId);
		$json = It6_GlobalCache::getKey($key, $fetched);
		if ($fetched)
			$messages = $json;
		else
			$messages = false;
		return $messages;
	}

	/**
	 * Returns all new (not read) messages
	 * NOTE: this method is implemented as low-level as possible and with minimal dependencies
	 *       because it is called from bypass scripts (optimized for frequent calls)
	 * @param integer $hostId identifier of the host
	 * @return array array of struct
	 * @see Entities_HostMessage
	 */
	public static function getNewByHost($hostId, $tryCache = true) {
		$messages = array();
		if ($tryCache)
			$messages = static::getNewByHostFromCache($hostId);
		if (empty($messages)) {
			//$messages = parent::getAllWhere(array( 'hostId = ?' => $hostId, '`read` = ?' => 0));
			$db = static::getDb();
			$messages = $db->select()
				->from(static::$TABLE, array_flip(static::$CONV))
				->where('host_id=?', $hostId)
				->where($db->quoteIdentifier('read') . '=0')
				->query()
				->fetchAll();
			static::writeMessageCache($hostId, $messages);
		}
		return new It6_ArrayWrapper($messages);
	}


	/**
	 * Returns location with given Id
	 * @param array $locationId id of the location we need info about
	 * @return struc structure of the location with the given id
	 */
	public static function getById($id, $extensions = null){

		return parent::getById($id, $extensions);
	}

	/** 
	 * Mark message as read (not neaw)
	 * @param integer $id identifier of the message
	 * @return boolean true on success 
	 */
	public static function setRead($id) {
		try {
			$message = static::getById($id);
			if (!empty($message) && 0 == $message['read']) {
				$db = static::getDb();
				
				$db->update(
					static::$TABLE,
					array(
						'read' => 1,
						'read_time' => It6_Date::dbNow()),
					array( static::$IDENTITY . '= ?' => $id,'`read` <> 1'	));
				static::deleteMessageCache($message['hostId']);
			}
		} catch (Extension $e) {
			throw new It6_XmlRpc_Exception('HostMessage::setRead',0,$e);
		}
			
		return true;
	}

	/**
	 * Insert new message. Value of the message identifier is ignored and new
	 * is generated.
	 * @param struct $message structure of the message
	 * @return integer message identifier of the created message
	 * @see Entities_HostMessage
	 */
	public static function insert($message) {
		$message['time'] = It6_Date::dbNow();
		$result = parent::insert($message);
		static::deleteMessageCache($message['hostId']);
		return $result;
	}

	/**
	 * @param ineteger $hostId
	 * @return string Cache key
	 */
	public static function getMessageCacheKey($hostId) {
		return It6_GlobalCache::KEY_PREFIX_HOST_MESSAGES . intval($hostId);
	}

	/**
	 * Clears message cache for given host
	 * @param integer $hostId
	 */
	public static function deleteMessageCache($hostId) {
		$key = static::getMessageCacheKey($hostId);
		return It6_GlobalCache::deleteKey($key);
	}
	
	/**
	 * Writes cache with new messages for host
	 * @param integer $hostId
	 * @param array $messages array of message structures
	 */
	public static function writeMessageCache($hostId, $messages = null) {
		if (empty($hostId))
			return;
		if (!isset($messages))
			$messages = static::getNewByHost($hostId);
		if (empty($messages))
			$messages = array();
		$messages = It6_ArrayWrapper::toNativeArray($messages);
		$key  = static::getMessageCacheKey($hostId);
		It6_GlobalCache::setKey($key, Zend_Json::encode($messages));
	}

}
