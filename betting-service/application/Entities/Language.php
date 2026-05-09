<?php

/**
 * Branch type dao.
 * @author Martin Bohal
 * @see Webservice_Language
 */
class Entities_Language extends Entities_AbstractEntity {

	/**
	 * id of the language
	 * @var int
	 */
	public $languageId;

	//FIXME:
	//Commented out by Martin as I do not know what they are and what they should be called.When someone needs them they can uncomment them and come up with some sense making name.
	//Same thing has to be done in Webservice_Langauge
	//public $pozice;


	/**
	 * iso code of the language
	 * @var string
	 */
	public $iso;

	/**
	 * name of the language
	 * @var string
	 */
	public $name;

	/**
	 * indicates shown / not shown
	 * @var boolean
	 */
	public $isShown;

	/**
	 * Locale name of the language
	 * @var string
	 */
	public $lcLocal;

	/**
	 * Iso for the language to be used on HP
	 * @var string
	 */
	public $homepageIso;

	/**
	 * Contact for the language
	 * @var string
	 */
	public $contact;

	/**
	 * Flag id for the language
	 * @var integer
	 */
	public $flagId;
}
