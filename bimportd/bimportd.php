<?php

set_time_limit(0);

define('ROOT', dirname(dirname(__FILE__)) . '/');

require_once(ROOT . 'bimportd/config_local.php');
do {
	system('./bimport.sh');
	sleep(SLEEP_TIME);
} while(true);
