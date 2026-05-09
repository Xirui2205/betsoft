<?php 
if(!defined('ROOT')) define('ROOT',dirname( dirname( dirname(__FILE__) ) ) . '/');

$fileName = "UT_VICTORKA_2_4_2013.pdf";
$filePath = ROOT . "web/www/pdf/newspapers/" . $fileName;

header('Cache-control: private');
header('Content-Type: application/pdf');
header('Content-Disposition:attachment; filename="' . $fileName . '"');
header("Content-Length: ".filesize($filePath));
header('Pragma: public');
readfile($filePath);