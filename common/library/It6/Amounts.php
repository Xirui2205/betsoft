<?php

class It6_Amounts extends It6_Array {

const ITEM_CLASS = 'It6_Amount';

const OP_EQ = 1;
const OP_NOTEQ = 2;

/**
 * @note This method must be public because in PHP 5.3.6 there is no class context support in closures and workaround needs public access.
 * @param It6_Amount $amount Instance to be tested
 * @param array $members [optional] array( member_name => array(value [, operator]), ...)
 *                       all members with given name(s) are tested and must give positive result to not be filtered out
 *                       value is compared using operator
 *                       known operators (It6_Amounts::OP_* constants):
 *                           OP_EQ (default) ... equal
 *                           OP_NOTEQ ... not equal
 *                       for unknown operators member isn't tested
 * @returns boolean TRUE if item should be filtered out
 */
public static function filterByMembers(It6_Amount $amount, array $members) {
	foreach ($members as $name => $comp) {
		if (is_array($comp)) {
			$op = (!empty($comp[1]) ? $comp[1] : static::OP_EQ);
			$comp = $comp[0];
		}
		else
			$op = static::OP_EQ;
		if (static::OP_EQ == $op) {
			if ($amount->$name != $comp)
				return true;
		}
		else if (static::OP_NOTEQ == $op) {
			if ($amount->$name == $comp)
				return true;
		}
	}
	return false;
}

/**
 * Reduce filtered items
 * @param function $fnReduce Function that takes two parameters, 1st accumulator, 2nd array element and returns new accumulator value
 * @param array $fnFilter Filter callback (takes item reference as the only one parameter)
 * @param mixed $initial [optional] Initial value of accumulator (default null)
 * @returns mixed reduced value
 */
protected function _reduce($fnReduce, $fnFilter = null, $initial = null) {
	return array_reduce(
		$this->_items,
		function($accu, It6_Amount $amount) use ($fnReduce, $fnFilter) {
			return ( isset($fnFilter) && $fnFilter($amount) ? $accu : $fnReduce($accu, $amount) );
		},
		$initial
	);
}

/**
 * Walk on filtered items
 * @param function $function Function that takes item reference as the only one parameter
 * @param array $fnFilter Filter callback (takes item reference as the only one parameter)
 * @param float $initial [optional] Initial value of accumulator (default null)
 * @returns It6_Amounts Reference to this instance
 */
protected function _walk($function, $fnFilter = null) {
	array_walk(
		$this->_items,
		function(It6_Amount $amount) use ($function, $fnFilter) {
			if ( !isset($fnFilter) || !$fnFilter($amount) )
				$function($amount);
		}
	);
	return $this;
}

/**
 * Computes sum of amounts
 * @param bool $total TRUE if sum "totalized" amounts
 * @param array $filters [optional] @see It6_Amounts::filterByMembers() $members parameter description
 * @param float $initial [optional] Initial value of sum (default zero)
 */
public function sum($total, array $filters = array(), $initial = 0.0) {
	$_this = $this;
	return $this->_reduce(
		function($accu, It6_Amount $amount) use ($total) {
			return $accu + ($total ? $amount->total() : $amount->amount);
		},
		(empty($filters) ? null : 
			function(It6_Amount $item) use ($_this, $filters) { return $_this->filterByMembers($item, $filters); }
		),
		$initial
	);
}

/**
 * Computes sum of counts of amounts
 * @param array $filters [optional] @see It6_Amounts::filterByMembers() $members parameter description
 * @param float $initial [optional] Initial value of sum (default zero)
 */
public function sumCounts(array $filters = array(), $initial = 0) {
	$_this = $this;
	return $this->_reduce(
		function($accu, It6_Amount $amount) { return $accu + $amount->count; },
		(empty($filters) ? null : 
			function(It6_Amount $item) use ($_this, $filters) { return $_this->filterByMembers($item, $filters); }
		),
		$initial
	);
}

/**
 * Finds maximal amount
 * @param bool $total TRUE if total amount are compared
 * @param array $filters [optional] @see It6_Amounts::filterByMembers() $members parameter description
 * @param float $initial [optional] Initial value of maximum (default FALSE)
 */
public function max($total, array $filters = array(), $initial = false) {
	$_this = $this;
	return $this->_reduce(
		function($accu, It6_Amount $amount) { $v = ($total ? $amount->total() : $amount->amount); return ($v > $accu ? $v : $accu); },
		(empty($filters) ? null : 
			function(It6_Amount $item) use ($_this, $filters) { return $_this->filterByMembers($item, $filters); }
		),
		$initial
	);
}

/**
 * Sets value of all amounts
 * @param float $amount Given value
 * @param array $filters [optional] @see It6_Amounts::filterByMembers() $members parameter description
 * @returns It6_Amounts reference to this instance
 */
public function setAll($amount, array $filters = array()) {
	$_this = $this;
	$this->_walk(
		function(It6_Amount $item) use ($amount) { $item->amount = $amount; },
		(empty($filters) ? null : 
			function(It6_Amount $item) use ($_this, $filters) { return $_this->filterByMembers($item, $filters); }
		)
	);
	return $this;
}

/**
 * Add $value to all amounts
 * @param float $value
 * @param bool $total TRUE of $value is total, FALSE if total value should be computed from $value
 * @param array $filters [optional] @see It6_Amounts::filterByMembers() $members parameter description
 * @returns It6_Amounts reference to this instance
 */
public function addToAll($value, $total, array $filters = array()) {
	$_this = $this;
	$this->_walk(
		function(It6_Amount $amount) use ($value, $total) { $amount->add($value, $total); },
		(empty($filters) ? null : 
			function(It6_Amount $item) use ($_this, $filters) { return $_this->filterByMembers($item, $filters); }
		)
	);
	return $this;
}

/**
 * Change amounts so that their total sum makes (if possible) given value.
 * Changes are distributed evenly (as evenly as it is possible).
 * This function fixes minimal and maximal constraints anyway, amounts are rounded.
 * @param float $targetTotal Given target total sum
 * @param float $minAmount Any amount mustn't be below this value
 * @param float $maxAmount Any amount mustn't be above this value
 * @param float $unit [optional] Smallest amount unit determinig precision to which must be rounded result amounts (default 1; 100 implies precision of two decimal digits)
 * @returns float New total sum (may differ from target value)
 */
public function setTotal($targetTotal, $minAmount, $maxAmount, $unit = 1) {
	$round = function($amount) use ($unit) {
		return It6_Models_Currency::round($amount, It6_Models_Currency::ROUND_MATH, $unit, It6_Models_Currency::ROUND_PARAM_UNIT, null);
	};
	$targetTotal = $round($targetTotal);
	$n = 0;
	$nTotal = 0;
	$total = 0.0;
	$max = false;
	foreach ($this->_items as &$amount) {
		++$n;
		$nTotal += $amount->count;
		$total += $amount->total();
		$amount->ignored = false;
		if (false === $max || $max < $amount->amount)
			$max = $amount->amount;
	}
	if (0 == $n)
		return 0;
	$minTotal = $nTotal * $minAmount;
	$maxTotal = $nTotal * $maxAmount;
	if ($targetTotal <= $minTotal) {
		$this->setAll($minAmount);
		return $minTotal;
	}
	else if ($targetTotal >= $maxTotal) {
		$this->setAll($maxAmount);
		return $maxTotal;
	}
	if ($minAmount == $maxAmount) {
		if ($minTotal != $targetTotal)
			return 0; // cannot satisfy
		$this->setAll($minAmount);
		return $minTotal;
	}
	// recalculate amounts
	$sumKN = 0;
	foreach ($this->_items as &$amount) {
		$sumKN += ($amount->amount / $max) * $amount->count;
		$amount->k = $amount->amount / $max;
	}
	$range = $maxAmount - $minAmount;
	$c = $targetTotal - $nTotal * $minAmount;
	$c /= $range * $sumKN;
	$newTotal = 0.0; // amounts before rounding
	$newTotalR = 0.0; // rounded amounts
	$quantum = 1 / $unit; // the least positive amount in given precision
	foreach ($this->_items as &$amount) {
		$value = $minAmount + $amount->k * $c * $range;
		$amount->amount = $value;
		$newTotal += $amount->total();
		$value = $round($value);
		if ($value < $minAmount)
			$value += $quantum;
		else if ($value > $maxAmount)
			$value -= $quantum;
		$amount->amount = $value;
		$newTotalR += $amount->total();
	}
	if (abs($newTotal - $targetTotal) > $quantum / 100)
		throw new Exception('World is such a hostile place to live when you have only ' . $newTotal . ' in your pocket and you need ' . $targetTotal . ' to get into Disneyland');
	// check that if rounding changed total sum
	$done = false;
	if ($newTotalR != $targetTotal) {
		$diff = $targetTotal - $newTotalR;
		//echo "D=$diff\n"; //TODO: get rid of this line
		// check all combinations of quantum sums (and with all combinations of sign changes), find minimum difference (zero is optimal)
		$bestDiff = false; // best found diff
		$best = false; // combination giving best quantum sum
		$indices = range(0, $n - 1);
		$signCombsCache = array();
		for ($k = 1; $k <= $n; ++$k) {
			$combs = It6_Array::getCombinations($n, $k, $indices);
			foreach ($combs as $comb) {
				for ($a = 1; $a <= $k; ++$a) {
					if (empty($signCombsCache[$k]) || empty($signCombsCache[$k][$a])) {
						$signCombs = It6_Array::getCombinations($k, $a, range(0, $k - 1));
						array_unshift($signCombs, array());
						$signCombsCache[$k][$a] = $signCombs;
					}
					else
						$signCombs = $signCombsCache[$k][$a];
					foreach ($signCombs as $signs) {
						// use negative sign for right indices in $comb
						$combS = $comb; // combination with signs applied
						$ni = count($combS);
						for ($i = 0; $i < $ni; ++$i) {
							if (in_array($i, $signs))
								$combS[$i] *= -1;
						}
						$qs = 0;
						$outOfLimit = false;
						foreach ($combS as $i) {
							$s = ($i < 0 ? -1 : 1);
							$i *= $s;
							$amount = &$this->_items[$i];
							$_a = $amount->amount + $s * $quantum;
							if ($_a < $minAmount || $_a > $maxAmount) {
								$outOfLimit = true;
								break;
							}
							$qs += $s * $this->_items[$i]->count * $quantum;
						}
						if ( false === $outOfLimit) {
							$newDiff = abs($diff - $qs);
							if ($newDiff < abs($diff) && (false === $bestDiff || $newDiff < $bestDiff) ) {
								$bestDiff = $newDiff;
								$best = $combS;
								if (0 == $bestDiff)
									$done = true; // we found optimal case
							}
						}
						if ($done) break;
					}
					if ($done) break;
				}
				if ($done) break;
			}
			if ($done) break;
		}
		// now might have best correction and we apply it
		if (false !== $bestDiff) {
			foreach ($best as $i) {
				$s = ($i < 0 ? -1 : 1);
				$i *= $s;
				$this->_items[$i]->amount += $s * $quantum;
			}
		}
	}
	return $this->sum(true);
}

} // class It6_Amounts
