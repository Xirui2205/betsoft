<?php

/**
 * User note
 * @author Martin Bohal
 * @see Webservice_UserNote
 */
class Entities_UserAction extends Entities_AbstractEntity {

	/**
	 * id of the action
	 * @var int
	 */
	public $actionId;

	/**
	 * id of the user
	 * @var int
	 */
	public $userId;

	/**
	 * text of the note
	 * @var int
	 */
	public $actionType;

	/**
	 * date the action is valid from
	 * @var string
	 */
	public $validFrom;

	/**
	 * date the action is valid until
	 * @var string
	 */
	public $validUntil;

	/**
	 * Hash attached to the transaction
	 * @var string
	 */
	public $actionHash;

	/**
	 * date the action was created
	 * @var string
	 */
	public $dateCreated;

	/**
	 * date the action is executed
	 * @var string
	 */
	public $dateExecuted;
}
