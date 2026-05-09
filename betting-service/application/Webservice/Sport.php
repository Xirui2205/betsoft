<?php

/**
 * Sport related static methods.
 * @author Pavel Klinger
 * @see Entities_Sport
 *
 */
class Webservice_Sport extends Webservice_AbstractWebService  {
	
	public static $TABLE = "sport";
	public static $TABLE_PREFIX = "sp";
	public static $IDENTITY = "sport_id";

	protected static $ENTITY_NAME = "Entities_Sport";
	protected static $CONV = array(
		'sp.sport_id' => 'sportId',
		'nazev' => 'name',
		'pozice' => 'navigationOrder',
		'zvyrazneni' => 'navigationHighlight',
		'zobrazeno' => 'navigationVisible',
		'betradar_sport_id' => 'betradarId',
		'live' => 'live',
		'approval_group_id' => 'approvalGroupId',
		'bet_alias_from' => 'betAliasFrom',
		'bet_alias_to' => 'betAliasTo',
		'bet_alias_from_new' => 'betAliasFromNew',
		'bet_alias_to_new' => 'betAliasToNew'
	);

	/**
	 * Returns all sports in the system.
	 * @return array array of the sport structures
	 * @see Entities_Sport
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Retrieves ID=>name pairs useful eg. for sport selections
	 * @see Webservice_AbstractWebService::getTableOptions()
	 * @return array (sport ID => name)
	 */
	public static function getAllOptions($langId = null, $where = null) {
		return parent::getTableOptions(static::$TABLE, static::$IDENTITY, 'nazev', $langId);
	}

	/**
	 * Find sport by given identifier.
	 * @param integer $sportId identifier of the sport
	 * @return struct sport structure	 
	 * @see Entities_Sport	 
	 */
	public static function getById($sportId, $extensions = null) {
		return parent::getById($sportId, $extensions);
	}
	
	/**
	 * Find sport by given handle
	 * @param string $sportHandle
	 * @return struct sport structure
	 * @see Entities_Sport
	 * @see Entities_Sport#handle
	 */
	public static function getByHandle($handle, $extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}
	
	/**
	 * Insert new sport. Value of the sport identifier is ignored and new
	 * is generated. 
	 * @param struct $sport structure of the sport
	 * @return integer sport identifier of the created sport
	 * @see Entities_Sport	 
	 */
	public static function insert($sport) {
		return parent::insert($sport);
	}
	
	/**
	 * Update sport.  
	 * @param struct $sport structure of the sport	 
	 * @return true on success
	 * @see Entities_Sport	 
	 */
	public static function update($sport) {
		return parent::update($sport);
	}
	
	/**
	 * Delete sport.  
	 * @param struct $sportId idenetifier of the sport.
	 * @return true on success	 
	 * @see Entities_Sport	 
	 */
	public static function delete($sportId) {
		return parent::delete($sportId);
	}
	
	/**
	 * Returns all sports where can be done bets now.
	 * @return array array of the sport structures
	 * @see Entities_Sport
	 */
	public static function getRelevant($extensions = null) {
		try {
			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);
			
			$select = static::defaultQuery( static::getDb()->select() )
				->join(
					array('ev' => Webservice_Event::$TABLE),
					'sp.sport_id = ev.sport_id AND ev.zobrazeno = 1',
					null)
				->joinLeft(
					array('tr' => 'preklady'),
					'tr.index_pole=sp.nazev AND tr.lang_id=1',
					array('name' => '(COALESCE(tr.text, sp.nazev))')
				);
			$select = Webservice_Event::getRelevantQuery($select)
				->where('sp.zobrazeno = 1') 
				->group('sp.sport_id')
				->having('COUNT(bt.sazka_id) > 0')
				->order('sp.pozice');
	
			static::preprocessExtensions($extensions, $input, $metadata, $select);

			$columns = array();
			if (!empty($extensions)) {
				foreach ($extensions as $ext) {
					if ($ext instanceof It6_WsExtension_Server_Columns) {
						$extCols = $ext->getParam(It6_WsExtension_Columns::PARAM_COLUMNS);
						if (!empty($extCols))
							$columns = array_merge($columns, $extCols);
					}
				}
			}
			if (empty($columns))
				$columns = null;

			$output = static::fetchAllEntities($select->query(), $columns);

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		catch ( Exception $e) {
			throw new It6_XmlRpc_Exception('getRelevantEvents', 0, $e);
		}
	}
		
	/**
	 * Returns all events related to the given sport.
	 * @param int $sportId identifier of the sport
	 * @return array array of event structures
	 * @see Entities_Event
	 */
	public static function getAllEvents($sportId, $extensions = null) {
		return Webservice_Event::getAllWhere(array(
			'sport_id = ?' => $sportId), $extensions);
	}
	
	/**
	 * Returns all events related to given sport 
	 * where can be done bets now.
	 * @param int $sportId identifier of the sport
	 * @return array array of the event structures
	 * @see Entities_Event
	 */
	public static function getRelevantEvents($sportId, $extensions = null) {
		
		try {
			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);
			
			$select = Webservice_Event::defaultQuery( Webservice_Event::getDb()->select() );
			$select = Webservice_Event::getRelevantQuery($select) 
				->where('sp.sport_id = ?', $sportId)
				->where('ev.zobrazeno = 1')
				->group('ev.udalost_id')
				->having('COUNT(bt.sazka_id) > 0');
	
			static::preprocessExtensions($extensions, $input, $metadata, $select);
				
			$columns = array();
			if (!empty($extensions)) {
				foreach ($extensions as $ext) {
					if ($ext instanceof It6_WsExtension_Server_Columns) {
						$extCols = $ext->getParam(It6_WsExtension_Columns::PARAM_COLUMNS);
						if (!empty($extCols))
							$columns = array_merge($columns, $extCols);
					}
				}
			}
			if (empty($columns))
				$columns = null;
			$output = static::fetchAllEntities($select->query(), $columns);

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		catch ( Exception $e) {
			throw new It6_XmlRpc_Exception('getRelevantEvents', 0, $e);
		}
}
	
	/**
	 * Returns all events related to the given sport and region.	 
	 * @param int $sportId identifier of the sport
	 * @param int $regionId identifier of the region
	 * @return array array of event structures
	 * @see Entities_Event
	 */
	public static function getEventsInRegion($sportId, $regionId, $extensions = null) {
		return Webservice_Event::getAllWhere(array(
			'sport_id = ?' => $sportId,
			'oblast_id = ?' => $regionId), $extensions);
	}
	
	/**
	 * Returns all events related to the given sport and region
	 * where can be done bets now.	 
	 * @param int $sportId identifier of the sport
	 * @param int $regionId identifier of the region
	 * @return array array of event structures
	 * @see Entities_Event
	 */
	public static function getRelevantEventsInRegion($sportId, $regionId, $extensions = null) {
		
		try {
			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);
			
			$select = Webservice_Event::defaultQuery( Webservice_Event::getDb()->select() );
			$select = Webservice_Event::getRelevantQuery($select) 
				->where('sp.sport_id = ?', $sportId)
				->where('ev.oblast_id = ?', $regionId)
				->where('ev.zobrazeno = 1')
				->group('ev.udalost_id')
				->having('COUNT(bt.sazka_id) > 0');

			static::preprocessExtensions($extensions, $input, $metadata, $select);
				
			$columns = array();
			if (!empty($extensions)) {
				foreach ($extensions as $ext) {
					if ($ext instanceof It6_WsExtension_Server_Columns) {
						$extCols = $ext->getParam(It6_WsExtension_Columns::PARAM_COLUMNS);
						if (!empty($extCols))
							$columns = array_merge($columns, $extCols);
					}
				}
			}
			if (empty($columns))
				$columns = null;
			$output = static::fetchAllEntities($select->query(), $columns);

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		catch ( Exception $e) {
			throw new It6_XmlRpc_Exception('getRelevantEventsInRegion', 0, $e);
		}
	}

	/**
	 * Returns all regions related to the given sport.
	 * @param int $sportId identifier of the sport
	 * @return array array of region structures
	 * @see Entities_Region
	 */
	public static function getAllRegions($sportId, $extensions = null) {
		try {
			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);
			
			$select = Webservice_Region::defaultQuery( Webservice_Region::getDb()->select() )
				->join(
					array('ev' => Webservice_Event::$TABLE),
					'rg.oblast_id = ev.oblast_id',
					null)
				->where('ev.sport_id = ?', $sportId)
				->group('rg.oblast_id');
	
			static::preprocessExtensions($extensions, $input, $metadata, $select);
				
			$columns = array();
			if (!empty($extensions)) {
				foreach ($extensions as $ext) {
					if ($ext instanceof It6_WsExtension_Server_Columns) {
						$extCols = $ext->getParam(It6_WsExtension_Columns::PARAM_COLUMNS);
						if (!empty($extCols))
							$columns = array_merge($columns, $extCols);
					}
				}
			}
			if (empty($columns))
				$columns = null;
			$output = static::fetchAllEntities($select->query(), $columns);

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		catch ( Exception $e) {
			throw new It6_XmlRpc_Exception('getAllRegions', 0, $e);
		}
	}

	/**
	 * All regions where are some opened bets
	 * @param int $sportId identifier of the sport
	 * @return Entities_Region
	 */
	public static function getAllRelevantRegions($sportId, $extensions = null) {
		try {
			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);
			
			$select = Webservice_Region::defaultQuery( Webservice_Region::getDb()->select() )
				->join(
					array('ev' => Webservice_Event::$TABLE),
					'rg.oblast_id = ev.oblast_id AND ev.zobrazeno = 1',
					null);
			$select = Webservice_Event::getRelevantQuery($select)
				->where('ev.sport_id = ?', $sportId)
				->group('rg.oblast_id')
				->having('COUNT(bt.sazka_id) > 0');

			static::preprocessExtensions($extensions, $input, $metadata, $select);

			$columns = array();
			if (!empty($extensions)) {
				foreach ($extensions as $ext) {
					if ($ext instanceof It6_WsExtension_Server_Columns) {
						$extCols = $ext->getParam(It6_WsExtension_Columns::PARAM_COLUMNS);
						if (!empty($extCols))
							$columns = array_merge($columns, $extCols);
					}
				}
			}
			if (empty($columns))
				$columns = null;
			$output = static::fetchAllEntities($select->query(), $columns);

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		catch ( Exception $e) {
			throw new It6_XmlRpc_Exception('getAllRelevantRegions', 0, $e);
		}
	}

	/**
	 * All sports by approval group Ids
	 * @param int $groupId identifier of the approval group
	 * @return Entities_ApprovalGroup
	 */
	public static function getByApprovalGroupId($groupId, $extensions = null) {
		$where = array('approval_group_id=?',$groupId);
		return parent::getAllWhere($where, $extensions);
	}

	/**
	 * Set approval group Id for
	 * @param array $sportIds identifier of the sports
	 * @param int $groupId identifier of the approval group
	 * @return Entities_ApprovalGroup
	 */
	public static function setApprovalGroupId($sportIds, $groupId) {
		try {
			$data = array('approval_group_id' => $groupId);
			$where = array();
			$where[static::$IDENTITY.' IN (?)'] = $sportIds;
			$res = static::getDb()->update(self::$TABLE, $data, $where);
			return $res;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("toEntity.", 0, $e);
		}
	}

}
