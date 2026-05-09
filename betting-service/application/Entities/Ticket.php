<?php
/**
 * Ticket dao.
 * @author Pavel Klinger
 * @see Webservice_Sport
 * 
 */
class Entities_Ticket extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the ticket
	 * @var integer
	 */
	public $ticketId;

	/**
	 * Ticket handle
	 * @var integer
	 */
	public $ticketHandle;

	/**
	 * True means that this ticket is just for template purpose.
	 * @var boolean
	 */
	public $isTemplate;
	
	/**
	 * Identifier of ticket's owner
	 * @var integer
	 * @see Entities_User
	 */
	public $userId;
	
	/**
	 * Identifier of host where the ticked was created
	 * @var integer
	 * @see Entities_User
	 */
	public $hostId;
	
	/**
	 * Sum of money in the ticket.
	 * @var float
	 * @see Entities_Ticket::winAmount
	 * @see Entities_Ticket::totalOdds
	 */
	public $amount;

	/**
	 * Sum of money in the ticket rounded to smallest cash unit.
	 * @var float
	 */
	public $cashAmount;
	
   /**
	* Points stake in points tickets
	* @var float
	*/
	public $pointsAmount;

	/**
	 * Name of ticket currency (or points)
	 * @var string
	 */
	public $currencyName;

	/**
	 * Creation time of the ticket.
	 * @var datetime
	 */
	public $createdTime;
	
	/**
	 * True when the ticked was payd off.
	 * @var boolean
	 * @see Entities_Ticket::$paidOutTime
	 * @see Entities_Ticket::$paidOutBookmakerId
	 */
	public $paidOut;
	
	
	/**
	 * True when the ticket was canceled.
	 * @var boolean
	 * @see Entities_Ticket::$canceledByBookmakerId
	 * @see Entities_Ticket::$reasonOfCancelation
	 */
	public $canceled;
	
	/**
	 * Identifier of the bookmaker who canceled the ticket.
	 * @var integer
	 * @see Entities_Bookmaker
	 */
	public $canceledByBookmakerId;
	
	/**
	 * Nick of the bookmaker who canceled the ticket.
	 * @var integer
	 * @see Entities_Bookmaker
	 */
	public $canceledByBookmakerNick;
	
	/**
	 * Reason of the ticket cancelation.
	 * @var string
	 * @see Entities_Ticket::$canceledByBookmakerId
	 * @see Entities_Ticket::$canceled
	 */
	public $reasonOfCancelation;
	
	/**
	 * ??????
	 * @var boolean
	 */
	
	public $freeBetBonus;
		
	
	
	/**
	 * Identifier of the ticket type
	 * @var string ticked type enumeration of (simple, kombi, system, maxikombi)
	 * @see Entities_TicketType
	 */
	public $type;
	
	
	/**
	 * Possible win sum of money (totalOdds * Amount)
	 * @var float
	 * @see Entities_Ticket::$amount
	 * @see Entities_Ticket::$totalOdds
	 */
	public $winAmount;
	
	/**
	 * Total odds rate of the ticket.
	 * @var float
	 * @see Entities_Ticket::$amount
	 * @see Entities_Ticket::$winAmount
	 */
	public $totalOdds;
	
	/**
	 * ????
	 * @var float
	 * @see Entities_Ticket::$winAmmunt
	 * @see Entities_Ticket::$paidOut
	 * @see Entities_Ticket::$realTotalOdds
	 */
	public $realWinAmount;
	
	/**
	 * ????
	 * @var float
	 * @see Entities_Ticket::$totalOdds
	 * @see Entities_Ticket::$paidOut
	 * @see Entities_Ticket::$realWinAmount
	 */
	public $realTotalOdds;
	
	/**
	 * ?????
	 * @var unknown_type
	 */
	public $stats;
	
	/**
	 * ?????
	 * @var unknown_type
	 */
	public $userStats;

	
	/**
	 * Time when the ticket was payed off.
	 * @var datetime
	 * @see Entities_Ticket::$paidOut
	 * @see Entities_Ticket::$paidOutBookmakerId 
	 */
	public $paidOutTime;
	
	/**
	 * Identifier of the bookmaker who payed off the ticket.
	 * @var integer
	 * @see Entities_Bookmaker
	 * @see Entities_Ticket::$paidOutTime
	 * @see Entities_Ticket::$paidOut
	 */
	public $paidOutBookmakerId;
	
	
	/**
	 * ?????
	 * @var boolean
	 */
	public $mail;		
		
	
	/**
	 * Send ticket results to user via text message
	 * @var boolean
	 */
	public $sms;
	
	/**
	 * Array of all ticket tips groups
	 * @var array
	 * @see Entities_TicketTipGroup
	 */
	public $groups;
	
	/**
	 * Array of all beted combinations for system and maxicombi bet.
	 * @var array
	 * @see Entities_TicketCombination
	 */
	public $combinations;

	/**
	 * Ticket hash
	 * @var string
	 */
	public $ticketHash;

	/**
	 * @var boolean
	 */
	public $cash;

	/**
	 * Type of points for payments in points. Null for cash payments.
	 * @var integer
	 */
	public $pointTypeId;

	/**
	 * @var float
	 */
	public $rateAdvance;
	
	/**
	 * Admin ID of coupon creator (zero for web user created tickets)
	 * @var integer
	 */
	public $adminId;

	/**
	 * Coupon ID from which was ticket created
	 * @var integer
	 */
	public $couponId;
	
	/** 
	 * Manipulation fee, calculated after ticket creation, real number (ratio)
	 * @var unknown_type
	 */
	public $mp;
	
	/** 
	 * Wining manipulation fee (just ratio)
	 * @var unknown_type
	 */
	public $mpWin;
	
	/** 
	 * Wining manipulation fee, calulated after payout
	 * @var unknown_type
	 */
	public $mpWinAmount;
	
	/**
	 * True on allowed cancelation of old ticket
	 * @var boolean
	 */
	public $cancelAllowed;

	/**
	 * ID of admin who created coupon in
	 * @var integer
	public $couponBookmakerId;

	/**
	 * Datetime of confirmation
	 * @var integer
	 */
	public $confirmDate;

	/**
	 * ID of admin who confirmed coupon in confirmation process
	 * @var integer
	 */
	public $confirmBookmakerId;

}