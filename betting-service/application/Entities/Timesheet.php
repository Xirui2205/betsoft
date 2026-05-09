<?php

/**
 * Timesheet dao.
 * @author Filip Vesely
 * 
 */
class Entities_Timesheet extends Entities_AbstractEntity {
	
	/**
	 * Identifier of the bookmaker.
	 * @var integer
	 */
	public $bookmakerId;
	
	/**
	 * Day.
	 * @var string
	 */
	public $day;

	/**
	 * Arrival time.
	 * @var string
	 */
	public $arrival;

	/**
	 * Departure time.
	 * @var string
	 */
	public $departure;
	
}
