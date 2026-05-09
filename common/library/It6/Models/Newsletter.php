<?php

class It6_Models_Newsletter {
	
	/**
	 * Get hash for authentication during unsubscribe action
	 * @param integer $userId
	 * @param string $email
	 * @return string
	 */
	public static function getHash($userId, $email) {
		return sha1(($userId + 310) . md5($email . "b/*I7"));
	}
}