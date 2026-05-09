<?php

class It6_Filter_AccountingAccount extends It6_Filter_Abstract {

public function filter($value) {
	return str_pad($value, 3, '0', STR_PAD_LEFT);
}

} // class
