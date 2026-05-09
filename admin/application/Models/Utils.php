<?php

class Models_Utils {

	public static function init(){

	}

	public static function getOrderButtons($orderColumns, $section, $active) {
		$orderButton  = array();
		foreach ($orderColumns as $k => $v) {
			$o = (!empty($v['order']) && $v['order']=='asc') ? 'desc' : 'asc';
			$i = '<img src="_img/adm_'.$o.'.png" alt="'.$o.'"/>';
			$act = ($active == $k) ? 'class="active"' : '';
			$orderButton[$k] = '<button '.$act.' onClick="loadGeneric('.$section.', \'content\', { ajax: true, column : \''.$k.'\', order : \''.$o.'\' })">'.$i.'</button>';
		}
		return $orderButton;
	}

	public static function getSportsComboOptions($elm, $s) {
		$elm->addMultiOption('', i18n::tr('Choose sport ..'));
		foreach ($s as $k => $v) {
			$elm->addMultiOption($v['sportId'], $v['name']);
		}
		return  $elm;
	}

	public static function createComboOptions($elm, $s, Array $cols) {
//		$elm->addMultiOption('', i18n::tr('Choose sport ..'));
		foreach ($s as $k => $v) {
			$elm->addMultiOption($v[$cols['id']], $v[$cols['name']]);
		}
		return  $elm;
	}

	public static function getNameById($wsClass, $col, $id) {
		$ws = Zend_Registry::get('ws');
		$res = $ws->$wsClass->getById($id);
		return $res[$col];
	}

	public static function getCollection($arr, $col_id = 'id', $col_name = 'name', $start = array()) {
		$out =  $start;
		foreach ($arr as $k => $v)
			$out[$v[$col_id]] = $v[$col_name];

		return $out;
	}

	public static function getSelectOptions($array, $valueColumn = 'id', $labelColumn = 'name') {
		$retdata = array();

		foreach($array as $element) {
			$retdata[$element[$valueColumn]] = $element[$labelColumn];
		}

		return $retdata;
	}

	public static function parseParameters($parameters, $idCol='parameterId'){
		$outdata = array();
		foreach($parameters as $param){
			$outdata['param'.$param[$idCol]] = $param['value'];

		}

		return $outdata;
	}
}
