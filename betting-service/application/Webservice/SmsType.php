<?php
class Webservice_SmsType extends Webservice_AbstractWebService  {
    public static $ENTITY_NAME	= "Entities_SmsType";
	public static $TABLE		= "sms_type";
	public static $TABLE_PREFIX	= "st";
	public static $IDENTITY		= "id";

	public static $CONV = array(
		'id'	=> 'smsTypeId',
		'name'	=> 'smsTypeName',
	);
    
	protected static function getDb() {
		return static::getAdminDb();
	}

	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	public static function getById($typeId, $extensions = null){
		return parent::getById($typeId, $extensions);
	}
}