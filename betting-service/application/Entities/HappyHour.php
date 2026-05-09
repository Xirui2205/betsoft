<?php

/**
 * HappyHour dao.
 * @author Pavel Klinger
 * @see Webservice_HappyHour
 * 
 */
class Entities_HappyHour extends Entities_AbstractEntity {
	
/**
 * @var integer
 */
public $happyHourId;

/**
 * Type of the happy hour for now only 'visit'
 * @var string
 */
public $type;

/**
 * time of the begining of the happy hour.
 * @var string
 */
public $timeFrom;

/**
 * time of the end of the happy hour if null end of day is taken
 * @var string
 */
public $timeTo;

/**	 
 * Parameter of the happy our, typically given money
 * @var float
 */
public $amount;

/**
 * Number of the users that run the happy hour campaign
 * @var integer
 */
public $usersCount;

/**
 * Users limit of the happy our campaign.
 * @var integer
 */
public $usersInitCount;

} // Entities_HappyHour
