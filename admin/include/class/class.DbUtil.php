<?php

class DbUtil {

	public static function connectWebDb() {
		return self::connectDb(GDATABASE, GMY_HOST, GMY_USER, GMY_PASS, GMY_DB);
	}

	public static function connectAdminDb() {
		return self::connectDb(DATABASE, MY_HOST, MY_USER, MY_PASS, MY_DB);
	}

	public static function connectWarehouseDb() {
		return self::connectDb(WDATABASE, WMY_HOST, WMY_USER, WMY_PASS, WMY_DB);
	}

	private static function connectDb($type, $host, $user, $passwd, $dbName) {
		$db = DB::connect("$type://$user:$passwd@$host/$dbName");
		self::testResult($db);

		$db->setFetchMode(DB_FETCHMODE_ASSOC);
		$res = $db->query('SET NAMES \'utf8\'');
		self::testResult($res, I18n::tr('Nepodařilo se nastavit UTF-8 kódování pro komunikaci s DB'));
		$res = $db->query('SET time_zone=\'GMT\'');
		self::testResult($res, I18n::tr('Nepodařilo se nastavit časovou zónu GMT'));
		return $db;
	}

	public static function testResult($result, $msg = null, $translate = true) {
		if(DB::isError($result)) {
			if (empty($msg))
				$_msg = $result->getMessage();
			else
				$_msg = ($translate ? I18n::tr($msg) : $msg) . ' (' . $result->getMessage() . ')';
			throw new ExHandler($_msg, 'admin_ex_db');
		}
	}

	public static function testEmptyResult($row, $_msg = null) {
		if(!$row) {
			throw new ExHandler($_msg, 'admin_ex_db');
		}
	}

}
