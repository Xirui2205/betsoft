<?php

set_time_limit(0);

define('ROOT', dirname(dirname(__FILE__)) . '/');

require_once(ROOT . 'confirmd/config_local.php');
do {
	system('../confirm');
	usleep(SLEEP_TIME);
} while(true);
