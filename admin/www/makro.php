<?php
phpinfo();
$_html = file_get_contents("http://sortiment.makro.cz/cs/ardo-boruvky-1kg/115365p/");
//$html = iconv( "UTF-8", "UTF-8", $_html );
//$html = utf8_encode ( $_html );
$html = mb_convert_encoding($_html, 'HTML-ENTITIES', "UTF-8");

$classname = 'price';
$dom = new DOMDocument;
$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
//$results = $xpath->query("//*[@class='" . $classname . "']");
$results = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

if ($results->length > 0) {
    echo $review = $results->item(0)->nodeValue;
}
?>