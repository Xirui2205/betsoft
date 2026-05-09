<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

$d = getdate();
$offset = date('Z');
echo "{\"year\":{$d['year']},\"month\":{$d['mon']},\"day\":{$d['mday']},\"hour\":{$d['hours']},\"minute\":{$d['minutes']},\"second\":{$d['seconds']},\"offset\":$offset}";
