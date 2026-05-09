<?php

class It6_Models_DailySequence extends It6_Models_DbDependent {

protected static $_registryEntryDb = 'db';

protected static $_table = 'daily_sequence';

const SEQID_BANK_EXPORT = 1;

/**
 * Reserves number of next IDs in sequence (continuous)
 * @param integer $sequenceId Sequence ID (use SEQID_* constants)
 * @param integer $count of numbers to reserve
 * @param Zend_Db_Adapter $db [optional]
 * @return integer|boolean First of reserved numbers or FALSE on error
 */
public static function reserveNext($sequenceId, $count, &$db = null) {
	static::assureDbParam($db);
	$rows = $db->select()->from(self::$_table, array(
			'number' => 'seq_number',
			'today' => new Zend_Db_Expr('CURRENT_DATE()=export_date'),
		))
		->where('id=?', $sequenceId)
		->query()
		->fetchAll();
	if (empty($rows))
		return false;
	$row = $rows[0];
	$current = (empty($row['today']) ? 1 : $row['number']);
	$next = $current + $count;
	$db->update(
		self::$_table,
		array('seq_number' => $next, 'export_date' => It6_Date::dbNowAsDate()),
		array('id=?' => $sequenceId)
	);
	return $current;
}

}
