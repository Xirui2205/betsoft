<?php

/**
 * Stem dao.
 * @author Vladimir Holicka
 * @see Webservice_Stem
 *
 */
class Entities_Stem extends Entities_AbstractEntity {

	/**
	 * Stem ID
	 * $var integer
	 */
	public $stemId;
	
	/**
	 * Stam name
	 * @var string
	 */
	public $name;
	
	/**
	 * Stam's admin ID
	 * @var integer
	 */
	public $adminId;
	
	/**
	 * Stam description
	 * @var string
	 */
	public $desc;

}