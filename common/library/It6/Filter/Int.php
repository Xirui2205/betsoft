<?php

class It6_Filter_Int extends It6_Filter_Abstract {

	public function filter($value) {
		return preg_replace('/[^-+\\d]+/', '', $value);
	}

}
