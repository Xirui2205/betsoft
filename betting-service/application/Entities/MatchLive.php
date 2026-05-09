<?php

/**
 * LiveMatch dao
 * @author Pavel Klinger
 * @see Webservice_Livebetting
 */
class Entities_MatchLive extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the live match
	 * @var integer
	 */
	public $id;

	/** 
	 * date in utc 'yyyy-mm-dd HH:MM:SS' 
	 * @var string
	 */
	public $start;

	/** 
	 * Valid values are:
	 * 'NOT_STARTED', 'BEGIN','END','1_HALF','2_HALF','1_THIRD', '2_THIRD',
	 * '3_THIRD','1_Q','2_Q','3_Q','4_Q','OVERTIME','PAUSE','STOP',
	 * '1_SET','2_SET','3_SET','4_SET','5_SET','LIVE_WARMUP',
	 * 'LIVE_UNFINISHED','LIVE_PENALTY','LIVE_CANCELED
	 * @var string
	 */
	public $status;

	/**
	 * @var string
	 */
	public $sport;
	
	/**
	 * @var string
	 */
	public $league;
	
	/**
	 * @var string
	 */
	public $homeTeam;
	
	/**
	 * @var string
	 */
	public $awayTeam;

	/**
	 * @var integer
	 */
	public $minute;

	/**
	 * @var integer
	 */
	public $specialId;
	
	
	/**
	 * if true (!empty) match is deleted
	 * @var boolean
	 */
	public $delete;


}
