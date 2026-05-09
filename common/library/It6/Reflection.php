<?php
class It6_Reflection {
public static function getClassConstants($className) {
		$oClass = new ReflectionClass($className);
		return $oClass->getConstants();
	}
}
