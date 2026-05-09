<?php

/**
 * Branch dao.
 * @author Pavel Klinger
 * @see Webservice_Branch
 * 
 */
class Entities_Host extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the host.
	 * @var integer
	 */
	public $hostId;
	
	/**
	 * Identifier of the branch where the host is located
	 * @var integer
	 */
	public $branchId;
	
	/**
	 * Ip adress of the host
	 * @var string
	 */
	public $ip;
	
	/**
	 * Host name.
	 * @var string
	 */
	public $name;
	
	/**
	 * True if host is used, otherwise false.
	 * @var boolean
	 */
	public $allowed;
	
	/**
	 * Version of the instaled software
 	 * @var string
	 */
	public $version;
	
	
	/**
	 * True if host is online otherwise false
	 * @var boolean
	 */
	public $isOnline;
	
	/**
	 * Hardware of the host
 	 * @var string
	 */
	public $hardware;

	/**
	 * Display of the host
 	 * @var string
	 */
	public $display;

	/**
	 * Printer of the host
 	 * @var string
	 */
	public $printer;

	/**
	 * Windows serial number of the host
 	 * @var string
	 */
	public $winSn;

	/**
	 * Provider dns of the host
 	 * @var string
	 */
	public $providerDns;

	/**
	 * Provider gateway of the host
 	 * @var string
	 */
	public $providerGateway;

	/**
	 * Provider ip of the host
 	 * @var string
	 */
	public $providerIp;

	/**
	 * Provider username of the host
 	 * @var string
	 */
	public $providerUsername;

	/**
	 * Provider password of the host
 	 * @var string
	 */
	public $providerPassword;

	/**
	 * Email account of the host
 	 * @var string
	 */
	public $vicEmail;

	/**
	 * Password for the email account of the host
 	 * @var string
	 */
	public $vicEmailPassword;

	/**
	 * Admin password for the application of the host
 	 * @var string
	 */
	public $vicAdminPassword;

	/**
	 * Employee password for the application of the host
 	 * @var string
	 */
	public $vicEmployeePassword1;

	/**
	 * Employee password for the application of the host
 	 * @var string
	 */
	public $vicEmployeePassword2;
	
	/**
	 * Note.
 	 * @var string
	 */
	public $note;

	/**
	 * Fingerprint of machine (GUID not enclosed in curly braces; 36 chars)
	 * @var unknown_type
	 */
	public $fingerprint;
}
