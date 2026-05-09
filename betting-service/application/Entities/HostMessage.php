<?php

/**
 * HostMessage dao.
 * @author Pavel Klinger
 * @see Webservice_HostMessage
 */
class Entities_HostMessage extends Entities_AbstractEntity {
	
	/**
	 * id of the region where the branch is
	 * @var int
	 */
	public $id;
	
	
	
	/**
	 * Recepie
	 * @var integer
	 */
	public $hostId;
	
	
	/**
	 * Message body
	 * @var string
	 */
	public $message;
	
	
	/**
	 * Creation time
	 * @var date
	 */
	public $time;
	
	
	/**
	 * read ... not new.
	 * @var integer
	 */
	public $read;
	
	
	/**
	 * Time of reading the message
	 * @var date
	 */
	public $readTime;
}
