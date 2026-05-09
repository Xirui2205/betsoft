<?php

class It6_Models_Host {

const ID_INTERNET = 1;
const ID_INTERNET_LIVE = 7;

public static function isSystemId($hostId) {
	return (self::ID_INTERNET == $hostId || self::ID_INTERNET_LIVE == $hostId);
}

} // class It6_Models_Host
