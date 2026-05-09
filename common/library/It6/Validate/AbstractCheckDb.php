<?php

abstract class It6_Validate_AbstractCheckDb extends Zend_Validate_Abstract {


	/**
	 * name: checkDb
	 * @param $wsClass string name of ws class to call
	 * @param $wsId string name of a column to get data from. Needed to reduce the amount of data transfered.
	 * @param $checkFor struct defines which column values pairs we need to be unique: colname => value
	 * @param $exclude struct defines which column value pairs should be excluded from reslt. Typically used for update operations.
	 * @return number of rows | false
	 */
	protected function checkDb($wsClass, $wsId, array $checkFor, array $exclude = array(), $returnValue='count') {
		$filterData = array();

		foreach($checkFor as $colName => $value) {
			$filterDef[] = array('?'=> array($colName => $value), 'OP' => '=');
		}
		foreach($exclude as $colName => $value) {
			$filterDef[] = array('?'=> array($colName => $value), 'OP' => '!=');
		}

		if(!is_array($wsId))
			$wsId = array($wsId);
		

		$extensions[] = new It6_WsExtension_Client_Filter('filter', $filterDef);
		$extensions[] = new It6_WsExtension_Client_Columns('columns', $wsId);

		$rows = Zend_Registry::get('ws')->ext($extensions)->$wsClass->getAll();

		if(!empty($rows) && $returnValue == 'count')
			return count($rows);
		else if(!empty($rows) && $returnValue == 'array')
			return It6_ArrayWrapper::toNativeArray($rows);
		else
			return false;
	}
}
