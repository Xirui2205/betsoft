<?php

/**
 * ApprovalGroup type related static methods.
 * @author Filip Vesely
 * @see Entities_ApprovalGroup
 */
class Webservice_ApprovalGroup extends Webservice_AbstractWebService  {


	public static $TABLE = "approval_group";
	public static $IDENTITY = "id";
	public static $ENTITY_NAME = "Entities_ApprovalGroup";
	protected static $CONV = array(
		'id'	=> 'approvalGroupId',
		'name'	=> 'name'
	);

	/**
	 * Returns all approval groups in the system
	 * @return array array of the approval group structures
	 * @see Entities_Region
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}	
	/**
	 * Returns approval group with threshold settings
	 * @param array $typeId id of the type we need info about
	 * @return struc structure of the type with the given id
	 */
	public static function getById($groupId, $extensions = null){
		$a = parent::getById($groupId, $extensions);
		$a->thresholdTable = Webservice_ApprovalGroupThreshold::getByGroupId($groupId);
		return $a;
	}

	/**
	 * Returns all relevant (with at least one interval in approval_group_threshold) approval groups
	 * @return struct  structure of the type with the given id
	 */
	public static function getAllRelevant() {
		$db = static::getDb();;
		//$res = true;
		$res = $db->select()->distinct()
			->from(array('t'=> Webservice_ApprovalGroupThreshold::$TABLE),array('id'=>'approval_group_id'))
			->join(array('g'=>static::$TABLE),'t.approval_group_id = g.id',array('name'))
			->query()->fetchAll();
		return static::toEntities($res);
	}

	/**
	 * Insert new host. Value of the host identifier is ignored and new
	 * is generated. 
	 * @param struct $group structure of the host
	 * @return host|bool identifier of the created host or false on error
	 * @see Entities_ApprovalGroup
	 */
	public static function insert($group) {		
		return parent::insert($group);				
	}

	/**
	 * Update host.  
	 * @param struct $host structure of the host	 
	 * @return true on success
	 * @see Entities_Host	 
	 */
	public static function update($group) {
		return parent::update($group);
	}
}
