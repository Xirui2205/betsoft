<?php

file_put_contents('/tmp/php-live-input.log', strftime('%Y-%m-%d %H:%M:%S ') . file_get_contents('php://input'), FILE_APPEND);

define('ROOT', dirname(dirname(dirname(dirname(__FILE__)))) . '/');

define('LIVE_CLIENT', 1);

header("Content-type:text/xml; charset=UTF-8");

require_once ROOT . 'betting-service/application/bootstrap.php';

file_put_contents('/tmp/php-live-input.log', strftime('%Y-%m-%d %H:%M:%S ') . "PROCESSED.\n", FILE_APPEND);
