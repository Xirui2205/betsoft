<?php

class It6_Betradar_Sport extends It6_Betradar_Node {

const BRID_SOCCER = '1';
const BRID_BASKETBALL = '2';
const BRID_BASEBALL = '3';
const BRID_ICE_HOCKEY = '4';
const BRID_HANDBALL = '6';
const BRID_GOLF = '9';
const BRID_MOTOSPORT = '11';
const BRID_RUGBY = '12';
const BRID_SKIING = '14';
const BRID_BANDY = '15';
const BRID_AMERICAN_FOOTBALL = '16';
const BRID_CYCLING = '17';
const BRID_SNOOKER = '19';
const BRID_TABLE_TENNIS = '20';
const BRID_DARTS = '22';
const BRID_VOLEYBALL = '23';
const BRID_FIELD_HOCKEY = '24';
const BRID_FUTSAL = '29';
const BRID_BADMINTON = '31';
const BRID_PESAPALLO = '61';

public $categories = array();

public static function isPredefinedBrId($brId) {
	static $cache = false;
	if (false === $cache)
		$cache = It6_Php::getFilteredClassConstants(get_called_class(), 'BRID_', false, 'values-set');
	return isset($cache[$brId]);
}

public function addCategory(&$category) {
	$this->categories[] = &$category;
}

public function removeCategory($brId = null) {
	return $this->_removeNode($this->categories, $brId);
}

public function getDataToPreload(&$preloadedData) {
	$preloadedData->addSport($this->brId);
	foreach ($this->categories as $category) {
		$category->getDataToPreload($preloadedData);
	}
}

} // class
