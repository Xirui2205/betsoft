<?php
/**
 * Affiliate banner related static methods.
 * @author Jiri Ulbrich
 * @see Entities_AffiliateBanner
 */
class Webservice_AffiliateBanner extends Webservice_AbstractWebService {

	public static $TABLE = "affiliate_banner";
	public static $TABLE_PREFIX = "ab";
	public static $IDENTITY = "id";
	protected static $ENTITY_NAME = "Entities_AffiliateBanner";

	protected static $CONV = array(
		'id'			=> 'affiliateBannerId',
		'file_name'		=> 'fileName',
		'image_title'	=> 'imageTitle',
		'valid_from'	=> 'validFrom',
		'valid_to'		=> 'validTo',
		'alt'			=> 'alt',
		'target'		=> 'target',
	);

	//protected static function defaultJoins($query) {}

	/**
	 * Returns all banners in the system
	 * @return array array of the banner structures
	 * @see Entities_AffiliateBanner
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find banner by given identifier.
	 * @param integer $bannerId identifier of the banner
	 * @return struct banner structure
	 * @see Entities_AffiliateBanner
	 */
	public static function getById($bannerId, $extensions = null) {
		return parent::getById($bannerId, $extensions);
	}

	/**
	 * Insert new region. Value of the region identifier is ignored and new
	 * is generated.
	 * @param struct $banner structure of the banner
	 * @return integer banner identifier of the created banner
	 * @see Entities_AffiliateBanner
	 */
	public static function insert($banner) {
		/*$adapter = new Zend_File_Transfer_Adapter_Http();
		$adapter->setDestination(ROOT . 'tmp');

		if (!$adapter->receive()) {
			$messages = $adapter->getMessages();
			return false;
		} else {
			$fullPath = $adapter->getFileName();
			$fileName = $adapter->getFileName(null, false);
			$ftp = new It6_FtpSync_Client();
			$res = $ftp->upload(array($fullPath => "/affiliate/$fileName"), false);
		}*/
		return parent::insert($banner);
	}

	/**
	 * Update region.
	 * @param struct $banner structure of the region
	 * @return true on success
	 * @see Entities_AffiliateBanner
	 */
	public static function update($banner) {
		return parent::update($banner);
	}

	/**
	 * Delete banner.
	 * @param struct $bannerId idenetifier of the banner.
	 * @return true on success
	 * @see Entities_AffiliateBanner
	 */
	public static function delete($bannerId) {
		return parent::delete($bannerId);
	}
}