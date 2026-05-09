<?php
define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');
//var_dump(ROOT);
include(ROOT . 'common/includes.inc.php');
require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();

$host = 'http://localhost:14445';
$date = 'All';
$dateFrom = NULL;
$dateTo = NULL;
$category = 1;

$categoryNames = array(1 => '-hlavni',2 => '-hlavni-podpurky',3 => '-kompletni');
$dateNames = array('Today' => 'dnešní','All' => 'celkova');

$type = 'All';

if ( !empty($_GET['DateFilter']) ) {
	if (in_array($_GET['DateFilter'], array_keys($dateNames)) )
		$date = $_GET['DateFilter'];
	else if ($_GET['DateFilter']=='FromTo') {
		$date = $_GET['DateFilter'];
		$dateFrom = $_GET['DateFrom'];
		$dateTo = $_GET['DateTo'];
	}
	
	$type = $dateNames[$date];
}
if ( in_array($_GET['Category'], array_keys($categoryNames)) ) {
	$category = $_GET['Category'];
	$type .= $categoryNames[$_GET['Category']];
}



$clientCfg = array(
	'maxredirects' => 0,
	'timeout'      => 120
);
$getParams = array(
	'DocumentType' => 'Offer',
	'PageFormat' => 'Portrait',
	'Category' => $category,
	'DateFilter' => $date, // 'All', 'Today'
	'DateFrom' => $dateFrom,
	'DateTo' => $dateTo,
);


$client = new Zend_Http_Client("$host/docrq", $clientCfg);
$client->setParameterGet($getParams);
$response = $client->request();
//var_dump($response);

// Example of response headers on success:
// X-Document-Id: 2
// X-Document-Pages: 141
// X-Document-URL: http://localhost/offergen/2.pdf
// X-Preview-URL: http://localhost/offergen/2.txt

// Example of response headers on error:
// X-Error-Message: NoOutputError: No pages in output.
// X-Localized-Message: Zadny vystup

//$docPath = $response->getHeader('X-document-url');
$docId = $response->getHeader('X-document-id');
//$docPath = ROOT . "offergen/output/$docId.pdf";

if (empty($docId)) { // || !is_readable($docPath)) {
	header('Content-Type: text/plain; charset=utf8');
	$content = 'Nabídka je nedostupná!';
	$err = $response->getHeader('X-Error-Message');
	//$err = $response->getHeader('X-Localized-Message');
	if (!empty($err))
		$content .= "\n$err";
	echo $content;
}
else {
	$client->resetParameters(true);
	$client->setUri("$host/pdf/$docId");
	$response = $client->request();
	if ($response->isSuccessful()) {
		try {
			$temp = tmpfile();
			$bytes = fwrite($temp, $response->getRawBody());
			header("Content-Type: application/pdf");
			header("Content-Length: $bytes");
			header("Content-Disposition: attachment; filename=nabidka-$type-" . strftime('%Y-%m-%d') . ".pdf");
			fseek($temp, 0);
			fpassthru($temp);
			exit;
		}
		catch (Exception $e) {
		}
	}
	header('Content-Type: text/plain; charset=utf8');
	die('Error: Dokument nabídky nenalezen');
}
