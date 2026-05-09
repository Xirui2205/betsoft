<?php
 
class Chat {
		
	private $_sectionId;
	
	private $_db;
	
	private $_data;
	
	public function __construct($sectionId = null){
		$this->_sectionId = $sectionId;
		$this->_db = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
		$this->_db->setFetchMode(DB_FETCHMODE_ASSOC);
   		$sql = "set names 'utf8'";
   		$this->_db->query($sql);
   		$this->init();
	}
	
	public function init(){
		
		if(!empty($_GET['pageId'])){
	  		$init = 'init' .  ucfirst(strtolower($_GET['pageId']));
	  		$this->$init();
	  	}
	  	else{
	  		$this->_getSazky();
	  	}
	}
	
	public  function initDetail(){
		$this->_getChat($_GET['sazkaId']);
	}
	
	protected function _getSazky(){
		
		$sql = "SELECT 
					CONCAT(a.`home_team`, ' - ', a.away_team) AS s_sazka,
				    a.event_id AS nl_id_sazka,
				    COUNT(b.live_event_id) AS nl_chat,
				    a.start_date AS dt_zacatek
				FROM live_event a 
				JOIN live_chat b ON (a.`event_id` = b.`live_event_id`)
				GROUP BY b.`live_event_id`
				ORDER BY a.`start_date` DESC
				LIMIT 50";
		$this->_data['sazky'] = $this->_db->query($sql);
		return $this->_data['sazky'];
	}
	
	protected function _getChat($sazkaId){
		
		$sazkaId = intval($sazkaId);
		
		$sql = "SELECT 
					a.ts AS dt_datum,
				    a.`msg` AS s_chat,
				    a.`nick` AS s_nick,
				    a.`user_id` AS nl_id_user,
                    c.iso AS s_jazyk_iso
				FROM live_chat a
				JOIN live_event b ON (a.`live_event_id` = b.`event_id`)
                JOIN `jazyky` c ON (c.`lang_id` = a.`lang_id`)
				WHERE b.`event_id` = $sazkaId
				ORDER BY a.ts DESC;";
		$this->_data['chat'] = $this->_db->query($sql);
		return $this->_data['chat'];
	}
	
	
	public function getContent(){
		extract($this->_data);
		ob_start();
			if(!empty($_GET['pageId']))
			{
				require 'Template/Chat/' . $_GET['pageId'] . '.phtml';
			}
			else
			{
				require 'Template/Chat/index.phtml';
			}
			$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	
}