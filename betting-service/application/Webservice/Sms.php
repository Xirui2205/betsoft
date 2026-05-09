<?php
class Webservice_Sms extends Webservice_AbstractWebService  {

	public static $TABLE						= "sms";
	public static $TABLE_SMS_TYPE				= "sms_type";
	public static $IDENTITY						= "id";
    public static $TABLE_PREFIX                 = "s";
    public static $TABLE_SMS_TYPE_PREFIX        = "st";
    
    public static $ENTITY_NAME = "Entities_sms";
	protected static $CONV = array(
		's.id'                => 'smsId',
		's.message_text'      => 'smsMessageText',
		's.phone_number'      => 'smsPhoneNumber',
		's.send'              => 'smsSend',
        's.sms_type_id'       => 'smsTypeIdSms',
		'st.id'           => 'smsTypeId',
		'st.name'         => 'smsTypeName'
	);
    
    protected static function defaultJoins($query) {
        return parent::defaultJoins($query)
                ->join(array(self::$TABLE_SMS_TYPE_PREFIX => self::$TABLE_SMS_TYPE),
                        's.sms_type_id = st.id');
	}
    
    protected static function getDb() {
		return static::getAdminDb();
	}
    
	/**
	 * Returns all sms data in the system.
	 * @return struct sms data structure
	 * @see Entities_Sms
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}
    
	/**
	 * Find sms by given identifier.
	 * @param integer $smsId identifier of the sms
	 * @return struct sms structure
	 * @see Entities_Sms
	 */
	public static function getById($smsId, $extensions = null) {
		$retdata = parent::getById($smsId, $extensions);
		$retdata['smsSend']	= It6_Date::fromDb($retdata['smsSend']);
		return $retdata;
	}

	/**
	 * Insert new sms. Value of the sms identifier is ignored and new
	 * is generated.
     *  
	 * @param struct $sms structure of the sms
	 * @return integer|bool sms identifier of the created user or false on error
	 * @see Entities_Sms
	 */
	public static function insert($sms) {
		$sms['smsMessageText'] = trim($sms['smsMessageText']);
		$sms['smsPhoneNumber'] = trim($sms['smsPhoneNumber']);
		$sms['smsTypeIdSms'] = intval($sms['smsTypeIdSms']);
		$sms['smsSend'] = $sms['smsSend'] ? $sms['smsSend'] : null; // null ... dosud neodeslano
        
		return parent::insert($sms);
	}
}