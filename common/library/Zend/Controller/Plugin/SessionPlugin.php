<?php



class Zend_Controller_Plugin_SessionPLugin extends Zend_Controller_Plugin_Abstract 
{ 
    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
	public function routeShutdown(Zend_Controller_Request_Abstract $request){
	}

	/**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		Models_Session_SesClass::open();
	}

}
