<?php 

set_time_limit(0);
error_reporting(E_ALL);
$file = file_get_contents('test.xml');

echo mb_strlen($file) . '<br>';
$params = array('http' => array(
		'method' => 'POST',
		'header'=>"Content-Type: text/plain\r\n",
		'content' => urlencode($file)
));

$context = stream_context_create($params);

echo 'Stream vytvoren <br>';
$result = file_get_contents('http://admin.compbet.com/_xmlimport/', false, $context);

echo 'Výstup' . $result;
