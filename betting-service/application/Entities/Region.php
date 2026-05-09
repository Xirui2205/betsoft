<?php

/**
 * Region dao.
 * @author Pavel Klinger
 * @see Webservice_Region
 * 
 */
class Entities_Region extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the region.
	 * @var integer
	 */
	public $regionId;
	
	/**
	 * Human readable identifier of the region 
	 * @var string
	 */
	public $handle;
	
	/**
	 * Name of the region.
	 * @var string
	 */
	public $name;
	
	/**
	 * Region betradar id.
	 * @var integer
	 */
	public $betradarId;
	
	/**
	 * Path to the region flag.
	 * @var string
	 */
	public $flagPath;
	
	/**
	 * By this variable are entities sorted in the navigation.
	 * @var integer
	 */
	public $navigationOrder;			
	
}