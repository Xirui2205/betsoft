<?php

class MobileController extends Zend_Controller_Action {
    
        public function indexAction () {
                Models_BasicRender::render($this->view,$this->_request);
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
                
        }

    
}

