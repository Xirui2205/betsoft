<?php

class Entities_UserTracking extends Entities_AbstractEntity {

/**
 * Unique record ID
 * @var integer
 */
public $trackingId;

/**
 * Datetime of record
 * @var string
 */
public $time;

/**
 * ID stored in user cookie
 * @var string
 */
public $persistentId;

/**
 * Session ID at tha moment of record
 * @var string
 */
public $sessionId;

/**
 * Remote client IP
 * @var string
 */
public $remoteIp;

/**
 * X-Forward HTTP header value
 * @var string
 */
public $xForward;

/**
 * User agent string of client
 * @var string
 */
public $userAgent;

/**
 * User ID
 * @var integer
 */
public $userId;

/**
 * Username
 * @var string
 */
public $username;

}