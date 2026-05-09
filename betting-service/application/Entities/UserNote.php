<?php

/**
 * User note
 * @author Martin Bohal
 * @see Webservice_UserNote
 */
class Entities_UserNote extends Entities_AbstractEntity {

	/**
	 * id of the user
	 * @var int
	 */
	public $userId;

	/**
	 * text of the note
	 * @var string
	 */
	public $text;

	/**
	 * name of the admin who entered the note
	 * @var integer
	 */
	public $adminId;

	/**
	 * date the note was entered
	 * @var string
	 */
	public $date;

	/**
	 * Id of the note
	 * @var string
	 */
	public $noteId;
}
