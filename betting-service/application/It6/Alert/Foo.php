<?php
class It6_Alert_Foo extends It6_Alert {
	public static function check($params, $userId = null, $branchId = null) {
		return isset($params['param']) && $params['param'] == 'foo';
	}
	
	protected static function run($params, $userId = null, $branchId = null) {
		echo "runnig alarm.";
	}
}