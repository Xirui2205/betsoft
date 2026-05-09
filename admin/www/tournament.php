<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');

set_time_limit(0);
error_reporting(E_ALL);



$fp = fopen("tid.xml","r");

$xml = "";
do{$xml .= fgets($fp,1024);}while(!feof($fp));

fclose($fp);

$xml = trim($xml);
$xml = preg_replace("/\n*/","",$xml);
//echo htmlspecialchars($xml);exit;

$doc = new DOMDocument();
$doc->loadXML($xml);

$cid_ob = $doc->getElementsByTagName('Category');
$cid_ar = array();

for ($ii = 0; $ii < $cid_ob->length; $ii++) {

   $cid = $cid_ob->item($ii)->getElementsByTagName('CID')->item(0)->nodeValue;
   $sid = $cid_ob->item($ii)->getElementsByTagName('SID')->item(0)->nodeValue;
   $name = $cid_ob->item($ii)->getElementsByTagName('CName')->item(0)->nodeValue;

   $cid_ar[$cid]['sid'] = $sid;
   $cid_ar[$cid]['name'] = $name;
   
}


$sport_ob = $doc->getElementsByTagName('Sport');
$sport_ar = array();

for ($ii = 0; $ii < $sport_ob->length; $ii++) {

   $name = $sport_ob->item($ii)->getElementsByTagName('SName')->item(0)->nodeValue;
   $sid = $sport_ob->item($ii)->getElementsByTagName('SID')->item(0)->nodeValue;

   $sport_ar[$sid] = $name;
   
}


$items = $doc->getElementsByTagName('Tournaments');

$tournament = $items->item(0)->getElementsByTagName('Tournament');
echo '<link rel="stylesheet" href="css/blueprint/screen.css?r=' . RELEASE_REV . '" type="text/css" media="screen, projection">
 <link rel="stylesheet" href="css/adm.css?r=' . RELEASE_REV . '" media="screen" type="text/css" />

';
echo "<table class=\"unitable2\"><thead>";echo "<tr><th>BET RADAR League ID</th><th>Liga</th><th>Sport</th><th>Oblast</th></tr></thead>";
#Tournament#
	   for ($ii = 0; $ii < $tournament->length; $ii++) {
       
       $cid = $tournament->item($ii)->getElementsByTagName('CID')->item(0)->nodeValue;
	     echo "<tr><td>"; echo $tournament->item($ii)->getElementsByTagName('TID')->item(0)->nodeValue;echo "</td><td>";
		   echo $tournament->item($ii)->getElementsByTagName('TName')->item(0)->nodeValue;echo "</td><td>";
		   echo $sport_ar[$cid_ar[$cid]['sid']];echo "</td><td>";
       echo $cid_ar[$cid]['name'];echo "</td></tr>";
		 
	   }
	
	echo "</table>";   
?>
