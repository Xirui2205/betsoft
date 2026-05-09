<?php
/**
 * UserAction related static methods
 * @author Martin Bohal
 * @see Entities_UserAction
 */
class Webservice_UserAction extends Webservice_AbstractWebService  {

	public static $TABLE 				= "user_action";
	public static $TABLE_ARCHIVE		= "archive_user_action";
	public static $TABLE_PREFIX			= "ua";
	public static $ENTITY_NAME			= "Entities_UserAction";
	public static $IDENTITY				= "action_id";

	public static $CONV = array(
		'action_id'			=> 'actionId',
		'user_id'			=> 'userId',
		'action_type'		=> 'actionType',
		'valid_from'		=> 'validFrom',
		'valid_until'		=> 'validUntil',
		'action_hash'		=> 'actionHash',
		'date_created'		=> 'dateCreated',
		'date_executed'		=> 'dateExecuted'
	);



	/**
	 * Creates the user action defiend by $entity
	 * @param $entity struct definition of the user action to be created
	 * $return integer actionId of the  user actionn just created
	 * @see Entities_UserAction
	 */
	public static function insert($entity) {
		return parent::insert($entity);
	}



	/**
	 * Creates the user action defiend by $entity
	 * @param $entity struct definition of the user action to be created
	 * $return integer actionId of the  user actionn just created
	 * @see Entities_UserAction
	 */
	public static function resetPassword($actionHash=null, $userId=null) {
		$db = Zend_Registry::get('db');

		try {
			It6_DbTransaction::begin($db);

			if($userId == null) {
				$action = static::getByHash($actionHash);
				$userId = $action->userId;
			}

			$newPass = It6_Models_User::generatePassword(false);
			Webservice_User::setPassword(true, $userId, $newPass, true);

/*
			$newPassCrypt = It6_Models_User::cryptPassword(Help::DecodeUnicodeUrl($newPass));
			Webservice_User::update(array(
				'password'	=> $newPassCrypt,
				'userId'	=> $userId
			));

			$params = array(
				It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'PasswordChanged',
				'user_id'		=> $userId,
				'new_password'	=> $newPass
			);
			$email = array(
				'type' 		=> 1,
				'date'		=> It6_Date::dbNow(),
				'params'	=> $params
			);

			Webservice_CronJob::insert($email);
*/
			if($actionHash != null)
				self::archiveAction($actionHash);

			It6_DbTransaction::commit($db);
			return true;
		}

		catch(Exception $e) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not perform user action (actionHash: '".$actionHash."')", 0, $e);
		}
	}



	/**
	 * Creates the user action defiend by $entity
	 * @param $entity struct definition of the user action to be created
	 * $return integer actionId of the  user actionn just created
	 * @see Entities_UserAction
	 */
	private static function archiveAction($actionHash) {
		$action = self::getByHash($actionHash, false);

		$entity = new It6_ArrayWrapper($action);
		$data = static::removeTableNames(static::fromEntity($entity));
		Zend_Registry::get('db')->insert(static::$TABLE_ARCHIVE, $data);

		parent::delete($action['actionId']);
	}



	/**
	 * Creates the user action defiend by $entity
	 * @param $entity struct definition of the user action to be created
	 * $return integer actionId of the  user actionn just created
	 * @see Entities_UserAction
	 */
	public static function getByHash($actionHash, $onlyValid=true) {
		$db = Zend_Registry::get('db');
		if($onlyValid === true) {
			$where[] = $db->quoteInto('validFrom <= ?', It6_Date::dbNow());
			$where[] = $db->quoteInto('validUntil >= ?', It6_Date::dbNow());
		}
		$where[] = $db->quoteInto('actionHash = ?', $actionHash);

		$action = static::getAllWhere($where);
		return reset($action);
	}
}
