<?php

/**
 * User dao.
 * @author Pavel Klinger
 * @see Webservice_User
 *
 */
class Entities_User extends Entities_AbstractEntity {

	/**
	 * Unique identifier of the user.
	 * @var integer
	 */
	public $userId;

	/**
	 * Unique handle of user
	 * @var string
	 */
	public $userHandle;

	/**
	 * Login name of the user.
	 * @var string
	 */
	public $username;

	/**
	 * First name of the user.
	 * @var string
	 */
	public $firstName;

	/**
	 * Last name of the user.
	 * @var string
	 */
	public $lastName;

	/**
	 * Password of the user.
	 * @var string
	 */
	public $password;

	/**
	 * Identifier of the user's sex: 0 male, 1 female.
	 * @var integer
	 */
	public $sex;
	/**
	 * Date of the user's birth.
	 * @var date
	 */
	public $birthDate;

	/**
	 * User's email.
	 * @var string
	 */
	public $email;

	/**
	 * Credit card number
	 * @var string
	 */
	public $creditCardNumber;

	/**
	 * Identifier of the country.
	 * @var integer
	 * @see Entities_Country
	 */
	public $countryId;

	/**
	 * User adress street.
	 * @var string
	 * @see Entities_User::town
	 * @see Entities_User::zip
	 */
	public $street;

	/**
	 * User adress town.
	 * @var string
	 * @see Entities_User::street
	 * @see Entities_User::zip
	 */
	public $town;

	/**
	 * User adress zip code.
	 * @var string
	 * @see Entities_User::street
	 * @see Entities_User::town
	 */
	public $zip;

	/**
	 * User's phone. ???
	 * @var string
	  @see Entities_User::$mobilePhone
	 */
	public $phone;

	/**
	 * Identifier of the currency.
	 * @var integer
	 * @see Entities_Currency
	 */
	public $currencyId;

	/**
	 * Identifier of the currency.
	 * @var integer
	 * @see Entities_Currency
	 */
	public $currencyName;

	/**
	 * Identifier of the currency.
	 * @var integer
	 * @see Entities_Currency
	 */
	public $countryName;

	/**
	 * Identifier of the user's language.
	 * @var integer
	 * @see Entities_Language
	 */
	public $languageId;

	/**
	 * Identifier of the user's language.
	 * @var integer
	 * @see Entities_Language
	 */
	public $languageName;

	/**
	 * Identifier of the user's home branch.
	 * @var integer
	 * @see Entities_Language
	 */
	public $branchId;

	/**
	 * Time of the user's last login.
	 * @var datetime
	 */
	public $lastLoginTime;

	/**
	 * Time when the session expires.
	 * @var datetime
	 */
	public $sessionTimeout;

	/**
	 * Time of the user's registration.
	 * @var datetime
	 */
	public $registrationTime;

	/**
	 * Time of the user activation.
	 * @var datetime
	 */
	public $activationTime;

	/**
	 * When true the newsletter is sended to the user.
	 * @var boolean
	 */
	public $sendNewsletter;

	/**
	 * True for disabled users.
	 * @var boolean
	 */
	public $banned;

	/**
	 * Identifies anonymous user
	 * @var boolean
	 */
	public $anonymous;


	/**
	 * User money
	 * @var float
	 */
	public $balance;

	/**
	 * Array of points balances
	 * @var array
	 */
	public $points;

	/* prosetrit prakticnost techto parametru ########################################################### */

	/**
	 * Identifier of the group
	 * @var integer
	 */
	public $groupId;

	/**
	 * Identifier of the bookmaker who has created user.
	 * @var int
	 */
	public $createdBy;

	/**
	 * Identifier of the yser who has affiliated user.
	 * @var int
	 */
	public $affiliatedBy;

	/**
	 * Boolean user can use internet betting
	 * @var boolean
	 */
	public $internetAllowed;

	/**
	 * ??????
	 * @var integer
	 */
	public $block;

	/**
	 * ?????
	 * @var integer
	 */
	public $block_ip;
	/**
	 * ?????
	 * @var integer
	 */
	public $black_play;

		/**
	 * ?????
	 * @var integer
	 */
	public $selfExcludedUntil;



	/**
	 * ?????
	 * @var string
	 */
	public $info;

	/**
	 * User's mobile phone. ????
	 * @var string
	 * @see Entities_User::$phone
	 */
	public $mobilePhone;




	/**
	 * ??????
	 * @var unknown_type
	 */
	public $withdrawelStatus;





	/**
	 * ????
	 */
	public $inidividualni_max_vklad;




	/**
	 * Identifier of the user status. ???
	 * @var integer
	 */
	public $status;

	/**
	 * ?????
	 * @var float
	 */
	public $winRatio;



	/**
	 * ?????
	 * @var integer
	 */
	public $vyhernost_game;

	/**
	 * ?????
	 * @var float
	 */
	public $bet_stats_win;

	/**
	 * ?????
	 * @var float
	 */
	public $bet_stats_lose_acc;

	/**
	 * ?????
	 * @var float
	 */
	public $bet_stats_lose_book;

	/**
	 * ?????
	 * @var float
	 */
	public $bet_total;

	/**
	 * ?????
	 * @var integer
	 */
	public $win_ticket;

	/**
	 * ?????
	 * @var integer
	 */
	public $lose_ticket;

	/**
	 * ?????
	 * @var integer
	 */
	public $delete_ticket;

	/**
	 * ?????
	 * @var integer
	 */
	public $num_bet_ticket;

	/**
	 * ?????
	 * @var integer
	 */
	public $bet_total2;


	/**
	 * ?????
	 * @var integer
	 */
	public $ticket_num;

	/**
	 * ?????
	 * @var integer
	 */
	public $financeRating;

	/**
	 * ?????
	 * @var integer
	 */
	public $maxBet;

	/**
	 * ?????
	 * @var string
	 */
	public $book_info;



	/**
	 * ?????
	 * @var string
	 */
	public $osloveni;

	/**
	 * ?????
	 * @var integer
	 */
	public $eTestovaci;


	/**
	 * Current cache value
	 * @var float
	 */
	public $currentCache;


	/**
	 * ?????
	 * @var float
	 */
	public $castka_m;

	/**
	 * ?????
	 * @var float
	 */
	public $castka_w;

	/**
	 * ?????
	 * @var float
	 */
	public $forbiden;

	/**
	 * ?????
	 * @var float
	 */
	public $accountStatus;

	/**
	 * @var integer
	 * @see Webservice_User::WATCHED_* constants
	 */
	public $watched;

	/**
	 * @var string
	 */
	public $clientCardNumber;
	
	/**
	* @var string
	*/
	public $agreementNumber;

	/**
	 * Identitider of the admin who has created user
	 * @var integer
	 */
	public $createdByAdminId;

	/**
	 * Identitider of the admin who has alolowed user
	 * @var integer
	 */
	public $allowedByAdminId;

	/**
	 * Identitider of user as shown on national id
	 * @var string
	 */
	public $citizenId;

	/**
	 * Base for bonus amount (first stake in version 1)
	 * @var float
	 */
	public $ebBase;

	/**
	 * Bonus amount
	 * @var float
	 */
	public $ebAmount;

	/**
	 * Datetime for start of entry bonus time period (first stake time for version 1)
	 * @var string
	 */
	public $ebFrom;

	/**
	 * Current balance for entry bonus (total money spent on tickets in version 1)
	 * @var float
	 */
	public $ebBalance;

	/**
	 * Amount of money user received (NULL not recevied yet, 0 for time period over wuthout requirements met, >0 amount user got)
	 * @var float
	 */
	public $ebApplied;

	/**
	 * Datetime when user recived bnous
	 * @var string
	 */
	public $ebAppliedAt;

	/**
	 * Version of entry bonus (each version can have own parameter set, currently only version 1 is supported)
	 * @var integer
	 */
	public $ebVersion;

}
