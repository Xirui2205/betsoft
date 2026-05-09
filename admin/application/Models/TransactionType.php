<?php

class Models_TransactionType {


	public static function getEditableTypes() {
		$extensions[] = new It6_WsExtension_Client_Filter('filter',
		array(
			array('?'=> array('hasFee' => array('always', 'sometimes','never')), 'OP' => 'IN (?)'),
			array(
				'OP' => 'OR',
				array('?'=> array('isFeeEditable' => '1'), 'OP' => '='),
				array('?'=> array('isLimitEditable' => '1'), 'OP' => '=')
			)));
		$types = Zend_Registry::get('ws')->ext($extensions)->TransactionType->getAll();

		$outData = array();
		foreach($types as $type) {
			if($type['isFeeEditable'] == '1' && in_array($type['hasFee'],array('always', 'sometimes')))
				$outData['feeEditable'][] = $type;
			if($type['isLimitEditable'] == '1')
				$outData['limitEditable'][] = $type;
		}

		return $outData;
	}



	public static function getFeesById($typeId) {
		$extensions[] = new It6_WsExtension_Client_Columns('columns', array('transactionTypeId', 'feeFix', 'feeRel'));
		$types = Zend_Registry::get('ws')->ext($extensions)->TransactionType->getById($typeId);

		return $types;
	}



	public static function updateLimits($values) {
		if(!empty($values['lowLimitCheckbox'])) {
			unset($values['lowLimitCheckbox']);
			$values['lowLimit'] = new Zend_Db_Expr('NULL');
		}
		if(!empty($values['highLimitCheckbox'])) {
			unset($values['highLimitCheckbox']);
			$values['highLimit'] = new Zend_Db_Expr('NULL');
		}

		unset($values['save']);

		return Zend_Registry::get('ws')->TransactionType->update($values);
	}



	public static function updateFees($values) {
		if(empty($values['feeFix']))
			$values['feeFix'] = new Zend_Db_Expr('NULL');
		if(empty($values['feeRel']))
			$values['feeRel'] = new Zend_Db_Expr('NULL');

		unset($values['save']);

		return Zend_Registry::get('ws')->TransactionType->update($values);
	}
}
