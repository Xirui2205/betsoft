<?php

/**
 * ApprovalGroup dao.
 * @author Filip Vesely1
 * @see Webservice_ApprovalGroup
 */
class Entities_ApprovalGroup extends Entities_AbstractEntity {
	
	/**
	 * id of the type of the approval group
	 * @var int
	 */
	public $approvalGroupId;
	
	
	
	/**
	 * Name of the group
	 * @var string
	 */
	public $name;

	/**
	 * Table of thresholds, limits
	 * @var array
	 */
	//public $thresholdTable;
}
