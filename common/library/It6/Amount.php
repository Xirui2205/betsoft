<?php
/**
 * Class for amount that can state that it exists more than once
 */
class It6_Amount {

/**
 * Amount that exists COUNT-times in total
 * @var float
 */
public $amount = 0;

/**
 * Amount's multiplicity
 * @var interger
 */
public $count = 0;

/**
 * Amount's identifier for unambiquous
 * @var mixed
 */
public $id = null;

/**
 * Halper variable for some koeficient
 * @var floatval
 */
public $k = 0;

/**
 * @param float $amount The amount
 * @param int $count Multiplicity of the amount
 */
public function __construct($amount, $count, $id = null) {
	$this->amount = $amount;
	$this->count = $count;
	$this->id = $id;
}

/**
 * @returns float Total amount sum (count times amount)
 */
public function total() {
	return $this->amount * $this->count;
}

/**
 * @returns string Default text representation is like "(amount, count -> total)"
 */
public function __toString() {
	return '(' . $this->amount . ', ' . $this->count . ' -> ' . $this->total() . ')';
}

/**
 * Add $value to all amounts
 * @param float $value
 * @param bool $total TRUE of $value is total, FALSE if total value should be computed from $value
 */
public function add($value, $total) {
	if ($total)
		$value /= $this->count;
	$this->amount += $value;
	return $this;
}

} // class It6_Amount
