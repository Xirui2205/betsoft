<?php
/**
 * Coupon DAO (coupon is ticket before acceptation).
 * @author Petr Šťastný
 * 
 */
class Entities_Coupon extends Entities_AbstractEntity {

/**
 * Owner of the ticket (from vic_main.uzivatel)
 * @var integer
 */
public $userId;

/**
 * Creator of ticket (from vic_admin.admin)
 * @var integer
 */
public $adminId;

/**
 * Host where was ticket created (from vic_admin.host)
 * @var integer
 */
public $hostId;

/**
 * Unique ID of coupon
 * @var integer
 */
public $couponId;

/**
 * JSON encoded data of ticket (specific entries)
 * @var string
 */
public $data;

/**
 * Acceptation status
 *  0 = new (in acceptation)
 *  2 = accepted
 *  3 = rejected
 *  4 = other amount
 *  5 = acceptation prolonged
 * @var integer
 */
public $status;

/**
 * Time of insert/update
 * @var datetime
 */
public $date;

/**
 * JSON with changes for preapproved coupon
 * @var string
 */
public $modified;

/**
 * ?
 * @var integer
 */
public $live;

/**
 * ?
 * @var boolean
 */
public $liveConfirm;

/**
 * Optional status message
 * @var string
 */
public $statusMessage;

/**
 * Time of confirmation action
 * @var datetime
 */
public $confirmDate;

/**
 * ID of bookmaker who updated status in confirmation process
 * @var integer
 */
public $bookmakerId;

/**
 * Boolean value indicating that coupon was prolonged during confirmation process
 * @var integer
 */
public $prolonged;

} // class Entities_Coupon
