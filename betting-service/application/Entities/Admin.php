<?php

/**
 * Bookmaker dao.
 * @author Pavel Klinger
 * @see Webservice_Admin
 *
 */
class Entities_Admin extends Entities_AbstractEntity {

	/**
	 * Unique identifier of the bookmaker
	 * @var integer
	 */
	public $bookmakerId;

	/**
	 * Branch
	 * @var string
	 */
	public $branchId;

	/**
	 * Role ID
	 * TODO: what role? user has more roles
	 * @var string
	 */
	public $roleId;

	/**
	 * Bookmaker first name.
	 * @var string
	 */
	public $firstName;

	/**
	 * Bookmaker last name.
	 * @var string
	 */
	public $lastName;

	/**
	 * Bookmaker login name.
	 * @var string
	 */
	public $loginName;

	/**
	 * Bookmaker password.
	 * @var string
	 */
	public $password;

	/**
	 * Bookmaker phone.
	 * @var string
	 */
	public $phone;


	/**
	 * True for disabled bookmakers.
	 * @var boolean
	 */
	public $isBanned;

	/**
	 * Bookmaker email.
	 * @var string
	 */
	public $email;

}
