<?php

/**
 * Branch dao.
 * @author Pavel Klinger
 * @see Webservice_Branch
 *
 */
class Entities_Branch extends Entities_AbstractEntity {

	/**
	 * Unique identifier of the branch.
	 * @var integer
	 */
	public $branchId;

	/**
	 * Identifier of the branch where the bookmaker (employee, technician, sales mgr).
	 * @var string
	 * @see Entities_Branch
	 */
	public $name;

	/**
	 * Branch current status : online/offline.
	 * @var string
	 */
	public $statusId;

	/**
	 * Identifier of the branch where the bookmaker (employee, technician, sales mgr).
	 * @var string
	 * @see Entities_Branch
	 */
	public $ticketHeader;

	/**
	 * Branch address street.
	 * @var string
	 */

	public $street;

	/**
	 * Branch address zip code.
	 */
	public $zip;

	/**
	 * Branch address town.
	 */
	public $town;

	/**
	 * Branch address region
	 */
	public $branchLocationId;


	/**
	 * Type of the branch: casino, ..
	 */
	public $typeId;

	/**
	 * Branch contact email.
	 */
	public $email;

	/**
	 * Branch phone prefix.
	 * @var string
	 */
	public $handle;

	/**
	 * Branch phone.
	 * @var string
	 */
	public $phone;

	/**
	 * Branch provider name.
	 * @var string
	 */
	public $providerName;

	/**
	 * Branch provider address.
	 * @var string
	 */
	public $providerAddress;

	/**
	 * Branch provider IC.
	 * @var string
	 */
	public $providerIc;

	/**
	 * Branch provider DIC.
	 * @var string
	 */
	public $providerDic;

	/**
	 * Branch provider email.
	 * @var string
	 */
	public $providerEmail;

	/**
	 * Identifier of the branch currency
	 * @var integer
	 */
	public $currencyId;

	/**
	 * @var float
	 */
	public $balance;

	/**
	 * Name of the branch location
	 * @var integer
	 */
	public $locationName;

	/**
	 * Name fo the branch type
	 * @var integer
	 */
	public $typeName;

	/**
	 * name of the branch status
	 * @var integer
	 */
	public $statusName;


	/**
	 * True on banned branch	 
	 * @var boolean
	 */
	
	public $banned;


	/**
	 * Stem of the branch
	 * @var integer
	 */
	public $stemId;
	
  	/**
	 * Branch open date.
	 * @var date
	 */
	/*public $validFromDate;


	public $bankAccountPrefix;

	public $bankAccountNumber;

	public $bankNumber;

	public $bankName;

	public $bankBranchName;

	public $bankBranchNumber;

	public $bankCurrency;

	public $bankCurrencyName;*/

}
