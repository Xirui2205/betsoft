<?php



class Zend_Controller_Plugin_HtmlToXhtml extends Zend_Controller_Plugin_Abstract 
{ 
	
    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
  { 
  	
  	if($this->getRequest()->getControllerName() != 'ajax'){
     $resp = $this->getResponse(); 
     $tidy = new tidy(); 
     $tidy->parseString($resp, array('output-xhtml' => true, 'doctype' => 'strict'), 'utf8');
     $tidy->cleanRepair(); 
     $resp->setBody($tidy); 
  	}
  	
  } 
  

  

} 


?>