<?php 

class Zend_View_Helper_UrlSet extends Zend_View_Helper_Abstract {  
     
     public $view;

     
	public function urlSet($uid,$paramsUrl='',$params='', $withHost = false) {  
      
	    $select = Zend_Registry::get('db')->select()->from(array('a'=>'controller_convert'),array('a.req_controller','a.req_action'))
	                                      ->join(array('b'=>'jazyky'),'a.lang_id=b.lang_id')
	                                      ->where('a.c_id=?',$uid)
	                                      ->where('b.iso=?',$_SESSION['lang']);
        $stm  = $select->query();
  	    $row = $stm->fetchAll();
      
  	     	    
  	    foreach($row as $h){
  	 	    
  	 	  $cnt = '';
  
  	 	  if($h['req_controller'] != 'index')$cnt  = $h['req_controller'].'/';
  	 	  $act = ($h['req_action'] != 'index'?$h['req_action'] .'/':'');
  	 	
			$urlStart = ($withHost ? PROTOCOL . WEBHOST : '');
			return $urlStart .'/'. $_SESSION['lang'] .'/'. $cnt  . $act . $paramsUrl .(mb_strlen($params)>0?'?':'') .$params;
  	    	
  	    }
     	 
     }
       
      
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    
}