<?php
/**
 * Timesheet related static methods
 * Timesheet means attendance of a bookmaker
 * @author Filip Vesely
 * @see Entities_Timesheet
 *
 */
class Webservice_Timesheet extends Webservice_AbstractWebService {

	public static $TABLE = "admin_timesheet";

	public static $ENTITY_NAME = "Entities_Timesheet";

	protected static $CONV = array(
		'admin_id' => 'bookmakerId',
		'day' => 'day',
		'arrival' => 'arrival',
		'departure' => 'departure'
	);


}
