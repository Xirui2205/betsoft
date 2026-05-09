<?php

/**
 * ApprovalGroupThreshold type dao.
 * @author Filip Vesely
 * @see Webservice_ApprovalGroupThreshold
 */
class Entities_ApprovalGroupThreshold extends Entities_AbstractEntity {


	/**
	 * Id of the threshold
	 * @var integer
	 */
	public $thresholdId;

	/**
	 * Name of the group
	 * @var integer
	 */
	public $approvalGroupId;

	/**
	 * Name of the group
	 * @var float
	 */
	public $oddUpperThreshold;

	/**
	 * Table of thresholds, limits
	 * @var float
	 */
	public $stakeLowerThreshold;
}
