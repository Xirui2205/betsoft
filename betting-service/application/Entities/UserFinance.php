<?php

/**
 * Branch type dao.
 * @author Martin Bohal
 * @see Webservice_BranchType
 */
class Entities_UserFinance extends Entities_AbstractEntity {

	/**
	 * id of the user
	 * @var int
	 */
	public $id;

	/**
	 * balance of the user
	 * @var string
	 */
	public $balance;

	/**
	 * amount the user owns
	 * @var string
	 */
	public $debit;

	/**
	 * chips the user owns
	 * @var string
	 */
	public $chips;

	/**
	 * bonus the user enjoyes
	 * @var string
	 */
	public $bonus;
}
