<?php

class It6_Validate_HoneyPot extends Zend_Validate_Abstract {
    
    const MSG_SPAMBOT = 'spam';
        
    protected $_messageTemplates = array(
        self::MSG_SPAMBOT => 'Toto pole není pro člověka'
    );
    
    public function isValid($value, $context = null) {
        
        $value = (string)$value;
        $this->_setValue($value);
        
        if (is_string($value) and $value == '') {
            return true;
            
        }
        
        $this->_error(self::MSG_SPAMBOT);
        return false;
        
    }
    
}
