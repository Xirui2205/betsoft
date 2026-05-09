<?php

class It6_Models_ApprovalGroup extends It6_Models_DbDependent {

private $id = null;
private $thresholds = null;

/**
 * @param array $data
 *    array(
 *       array('oddUpperThreshold' => oddThreshold, 'stakeLowerThreshold' => stakeThreshold),
 *       ...
 *    );
 */
public function __construct($id, array $data) {
	$this->id = $id;
	$this->thresholds = array();
	foreach ($data as $item) {
		$this->thresholds[$item['oddUpperThreshold']] = $item['stakeLowerThreshold'];
	}
	ksort($this->thresholds, SORT_NUMERIC);
}

public function getId() {
	return $this->id;
}

/**
 * @param $odd Odd
 * @returns Stake threshold for given odd (rate) or FALSE if no threshold found.
 */
public function findStakeThreshold($odd) {
	foreach ($this->thresholds as $oddThreshold => $stakeThreshold) {
		if ($odd < $oddThreshold)
			return $stakeThreshold;
	}
	return false;
}

/**
 * @param float $odd Odd
 * @param float $stake Stake (or risk amount) on bet
 * @returns TRUE if bet should be approved, FALSE otherwise
 */
public function toBeApproved($odd, $stake) {
	$stakeThreshold = $this->findStakeThreshold($odd);
	if (false === $stakeThreshold)
		return true;
	else if ($stake > $stakeThreshold)
		return true;
	else
		return false;
}

/**
 * @param array|int $id approval group ID(s) to be read
 * @param $db [optional] DB adapter
 * @returns array (or array of arrays with approval group IDs as keys) with approval group data
 *    array( array('oddUpperThreshold' => oddThreshold, 'stakeLowerThreshold' => stakeThreshold), ... )
 */
public static function readApprovalGroup($id, &$db = null) {
	static::assureDbParam($db);
	if (is_array($id))
		$more = true;
	else {
		$more = false;
		$id = array($id);
	}
	$rows = $db->select()
		->from('approval_group_threshold')
		->where('approval_group_id IN (?)', $id)
		->query()
		->fetchAll();
	if (empty($rows))
		return null;
	$ags = array();
	foreach ($rows as $row) {
		$agId = $row['approval_group_id'];
		if (!array_key_exists($agId, $ags))
			$ags[$agId] = array();
		$ags[$agId][] = array(
			'oddUpperThreshold' => $row['odd_upper_threshold'],
			'stakeLowerThreshold' => $row['stake_lower_threshold']
		);
	}
	if ($more)
		return $ags;
	else
		return $ags[$id];
}

} // class It6_Models_ApprovalGroup