<?php

class It6_Validate_FormTimer extends Zend_Validate_Abstract {
    
    public function isValid($value, $context = null) {
        
        $totaltime = time() - $value;
        if ($totaltime < Webservice_Parameter::getGlobalParameter('web.registration.FormTimer')) {
            return false;
        }
        
        return true;
    }
    
}
