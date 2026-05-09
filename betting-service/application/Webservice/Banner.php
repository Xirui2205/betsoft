<?php

/**
 * Banner related static methods.
 * @author Martin Bohal
 * @see Entities_Banner
 *
 */
class Webservice_Banner extends Webservice_AbstractWebService  {

	public static $TABLE					= "banner";
	public static $TABLE_PREFIX				= "bn";
	public static $TABLE_CONTROLLER			= "controller_convert";
	public static $TABLE_CONTROLLER_PREFIX	= "cc";
	public static $IDENTITY					= "banner_id";

	protected static $ENTITY_NAME = "Entities_Contract";

	protected static $CONV = array(
		'banner_id'				=> 'bannerId',
		'valid_from'			=> 'validFrom',
		'valid_to'				=> 'validTo',
		'controller_id'			=> 'controllerId',
		'location_id'			=> 'locationId',
		'lang_id'				=> 'languageId',
		'title'					=> 'title',
		'image_name'			=> 'imageName',
		'url'					=> 'url',
		'text'					=> 'text',
		'target'				=> 'target',
		'order'					=> 'order',
		'cc.real_controller'	=> 'controllerName'
	);



	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->join(
				array(self::$TABLE_CONTROLLER_PREFIX => self::$TABLE_CONTROLLER),
				self::$TABLE_CONTROLLER_PREFIX.'.c_id = '.self::$TABLE_PREFIX.'.controller_id
				AND '.self::$TABLE_CONTROLLER_PREFIX.'.lang_id = '.self::$TABLE_PREFIX.'.lang_id',
				null
			);
	}



	/**
	 * Find banners by given controller and language
	 * @param int $branchId identifier of the contract
	 * @param int $langId identifier of the language
	 * @return struct array of banner structures
	 * @see Entities_Banner
	 */
	public static function getByControllerAndLanguage($controllerId, $langId, $onlyActive = false, $extensions = null) {
		$where = array (
			'controller_id = ?' => $controllerId,
			self::$TABLE_PREFIX.'.lang_id = ?' => $langId,
		);
		if($onlyActive === true) {
			$where['valid_to > ?']		= It6_Date::dbNow();
			$where['valid_from <= ?']	= It6_Date::dbNow();
		}

		$retdata = static::getAllWhereOrder($where, array('order'),$extensions);

		foreach($retdata as $key => $banner) {
			$retdata[$key] = (self::convertDates($banner, 'fromDb'));
		}
		return $retdata;
	}



	/**
	 * Find banners by given controller and language
	 * @param int $branchId identifier of the contract
	 * @return struct array of banner structures
	 * @see Entities_Banner
	 */
	public static function getByController($controllerId, $extensions = null) {
		$retdata = static::getAllWhere(
			array('controller_id = ?' => $controllerId),
			$extensions
		);

		foreach($retdata as $key => $banner) {
			$retdata[$key] = (self::convertDates($banner, 'fromDb'));
		}

		return $retdata;
	}



	/**
	 * Find Banner by given identifier.
	 * @param array $bannerId identifier of the banner
	 * @return array banner structures
	 * @see Entities_Banner
	 */
	public static function getById($bannerId, $extensions = null) {
		$retdata = parent::getById($bannerId, $extensions);
		return(self::convertDates($retdata, 'fromDb'));
	}



	/**
	 * Update banner
	 * @param struct $values for the line to be updated
	 * @boolean
	 * @see Entities_Banner
	 */
	public static function update($values) {
		$result = parent::update(self::convertDates($values, 'toDb'));
		if($result)
			It6_GlobalCache_Invalidator::Banner_update();

		return $result;
	}



	/**
	 * Insert banner
	 * @param struct $values for the line to be inserted
	 * @boolean
	 * @see Entities_Banner
	 */
	public static function insert($values) {
		$result = parent::insert(self::convertDates($values, 'toDb'));
		if($result)
			It6_GlobalCache_Invalidator::Banner_update();

		return $result;
	}



	private static function convertDates($values, $direction) {
		if($direction == 'toDb') {
			if(isset($values['validFrom']))
				$values['validFrom'] = It6_Date::toDb($values['validFrom']);
			if(isset($values['validTo']))
				$values['validTo'] = It6_Date::toDb($values['validTo']);
		}
		else if($direction == 'fromDb') {
			$values['validFrom']	= It6_Date::fromDb($values['validFrom']);
			$values['validTo']		= It6_Date::fromDb($values['validTo']);
		}

		return $values;
	}
}
