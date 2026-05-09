<?php

/**
 * Printed page statistic
 * overideble per host or user.
 * @author 
 */
class Webservice_PrintedPagesCounter extends Webservice_AbstractWebService {

	public static $TABLE = "host_printed_page";
	public static $TABLE_PREFIX = "hpp";
	public static $IDENTITY = "id";
	public static $ENTITY_NAME = "Entities_PrintedPagesCount";
	public static $CONV = array(
		'id'			    => 'id',
		'host_id'		    => 'host_id',
		'document_type'	    => 'document_type',
		'page_count'	    => 'page_count'
	);

	/**
	 * Printed page counter.
	 * @param integer $hostId identifier of the host
	 * @param integer $documentType value of the Document Type
	 * @param string $printedPageCount value of the printed page count
	 * @return boolean
	 */
	public static function newPagePrinted($hostId, $documentType, $printedPageCount) {
		$db = static::getMainDb();

		$data = array(
			'host_id' => $hostId,
			'document_type' => $documentType,
			'page_count' => $printedPageCount
		);

		$res = $db->insert(self::$TABLE, $data);
		return;
	}

	/**
	 * Get data.
	 * @param integer $hostId identifier of the host
	 * @param integer $dateStart
	 * @param integer $dateEnd
	 * @return array
	 */
	public static function getData($hostId = null, $dateStart = null, $dateEnd = null) {
		$result = static::getMainDb()->select()
		->from(self::$TABLE);
		
		if ( !empty($hostId) ) {
			$hostId = str_replace(";", ",", trim($hostId));
			$hostId = str_replace(" ", ",", $hostId);
			$result->where('host_id IN (?)', explode(",", $hostId));
		}

		if ( !empty($dateStart) ) {
			$result->where('datum >= ?', It6_Date::toDb($dateStart));
		}

		if ( !empty($dateEnd) ) {
			$result->where('datum <= ?', It6_Date::toDb($dateEnd));
		}

		return $result->query()->fetchAll();
	}
}