<?php

class It6_Filter_NineDigitHandle extends It6_Filter_Abstract {

public function filter($value) {
	It6_NineDigitHandle::fixHandle($value);
	return $value;
}

} // class