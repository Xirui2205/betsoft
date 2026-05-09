<?php

class It6_Models_Form_Util {

	//TODO: this is duplicate code of admin/Models/Utils.php
	//DUPLICITY: admin/Models/Utils.php
	public static function getSelectOptions($array, $valueColumn = 'id', $labelColumn = 'name') {
		$retdata = array();

		foreach($array as $element) {
			$retdata[$element[$valueColumn]] = $element[$labelColumn];
		}

		return $retdata;
	}



	public static function getDateCmbDays() {
		$tr = Zend_Registry::get('translate');
		$dayListArr		= array('0' => $tr->trans('choose'));

		for($i=1; $i<=31; $i++)
			$dayListArr[$i] = $i;

		return $dayListArr;
	}



	public static function getDateCmbMonth() {
		$tr = Zend_Registry::get('translate');
		$monthListArr = array('0' => $tr->trans('choose'));
		$monthListArr = array_merge($monthListArr, It6_Date::getListOfMonth());

		return $monthListArr;
	}



	public static function getDateCmbYears($endYearMod) {
		$tr = Zend_Registry::get('translate');
		$endYear = date('Y', It6_Date::nowAsTimestamp());
		$yearListArr = array('0' => $tr->trans('choose'));
		if(!empty($endYearMod))
			$endYear = $endYear + $endYearMod;

		for($i=$endYear; $i>=1900; $i--)
			$yearListArr[$i] = $i;

		return $yearListArr;
	}
}
