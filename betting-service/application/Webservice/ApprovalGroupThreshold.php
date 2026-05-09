<?php

/**
 * Entities_ApprovalGroupThreshold type related static methods.
 * @author Filip Vesely
 * @see Entities_ApprovalGroupThreshold
 */
class Webservice_ApprovalGroupThreshold extends Webservice_AbstractWebService  {

	public static $TABLE = "approval_group_threshold";
	public static $IDENTITY = "id";

	public static $ENTITY_NAME = "Entities_ApprovalGroupThreshold";
	protected static $CONV = array(
		'id' => 'thresholdId',
		'approval_group_id'	=> 'approvalGroupId',
		'odd_upper_threshold'	=> 'oddUpperThreshold',
		'stake_lower_threshold'	=> 'stakeLowerThreshold'
	);	

	/**
	 * Returns thershold settings
	 * @param integer $groupId id of the group
	 * @return array structure of the threshold settings
	 */
	public static function getByGroupId($groupId) {

		//FIXME this should return entity (use toEntity or getAllWhereOrder
		$db = static::getDb();
		$types = $db->select()
			->from(self::$TABLE)
			->where('approval_group_id = ?',$groupId)
			->order('odd_upper_threshold ASC')
			->query()->fetchAll();

	    return $types;

	}


	/**
	 * Insert new thershold. Value of the threshold identifier is ignored and new
	 * is generated. 
	 * @param struct $threshold structure of the host
	 * @return struct identifier of the created host or false on error
	 * @see Entities_ApprovalGroupThreshold
	 */
	public static function insert($threshold) {
		return parent::insert($threshold);				
	}

	/**
	 * Update threshold.  
	 * @param struct $threshold structure of the threshold
	 * @return true on success
	 * @see Entities_ApprovalGroupThreshold 
	 */
	public static function update($threshold) {
		return parent::update($threshold);
	}

	/**
	 * Delete threshold.  
	 * @param integer $thresholdId structure of the threshold
	 * @return true on success
	 * @see Entities_ApprovalGroupThreshold 
	 */
	public static function delete($thresholdId) {
		return parent::delete($thresholdId);
	}
}
