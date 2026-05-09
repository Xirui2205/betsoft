<?php
require_once 'phpQuery.php';

class Models_Ticket_OldTicket {

public static function findOldTicket($handle) {
	
	$str = static::_getTicket($handle);



	if ( strlen($str['tiket'].'') <= 225 ) {
		return false;
	}
	$str = $str['tiket'].$str['vysledky'];
 
	$str = str_replace('.//Images//','/images/old-ticket/',$str);
 
	return $str;
}


protected static function _getTicket($tid) {
	$data = static::_prepareData($tid);
	$outputBrutto = static::_fetchResponse($data);
	$outputNetto = static::_stripCrap($outputBrutto);
	
	return $outputNetto;
}

protected static function _stripCrap($html) {
	phpQuery::newDocument($html);
    $content['tiket'] = pq(".ticket");
    $content['vysledky'] = pq(".tiket");
	return $content;
}

protected static function _prepareData($tid) {
	$data = array();
	$data['__VIEWSTATE'] = "dDwxOTg3MDUwODc1OztsPFJpZ2h0SW5kZXgxOnZhbGlkYXRlVGlrZXRfYnV0dG9uOz4+FnOPbcn51yMk+IcNIESJMkQXFmU=";
	$data['RightIndex1:tiketNumber_tb'] = $tid;
	$data['RightIndex1:validateTiket_button.x'] = 5;
	$data['RightIndex1:validateTiket_button.y'] = 5;
	return $data;
}

protected static function _fetchResponse($data) {

	$tuCurl = curl_init();
	$cookie_file_path = tempnam('/tmp', 'VT');
	curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($tuCurl, CURLOPT_HEADER, 0);
	curl_setopt($tuCurl, CURLOPT_POST, 0);
	curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($tuCurl, CURLOPT_AUTOREFERER, 0);
	curl_setopt($tuCurl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($tuCurl, CURLOPT_COOKIEFILE, $cookie_file_path);
	curl_setopt($tuCurl, CURLOPT_COOKIEJAR, $cookie_file_path);
	curl_setopt($tuCurl, CURLOPT_COOKIESESSION, TRUE);
	curl_setopt($tuCurl, CURLOPT_COOKIE, session_name() . '=' . session_id());
	curl_setopt($tuCurl, CURLOPT_URL, "http://old.compbet.com/youwin.aspx");
	$tuData = curl_exec($tuCurl);

	curl_setopt($tuCurl, CURLOPT_COOKIEFILE, $cookie_file_path);
	curl_setopt($tuCurl, CURLOPT_COOKIEJAR, $cookie_file_path);
	curl_setopt($tuCurl, CURLOPT_URL, "http://old.compbet.com/youwin.aspx");
	curl_setopt($tuCurl, CURLOPT_POST, 1);
	$postarr = array();
	foreach($data as $k=>$n) {
		$postArr[] = $k . '=' . urlencode($n);
	}
	curl_setopt($tuCurl, CURLOPT_POSTFIELDS, implode('&',$postArr));

	$tuData = curl_exec($tuCurl);
	if(!curl_errno($tuCurl)){
		$info = curl_getinfo($tuCurl);
		//echo 'Took ' . $info['total_time'] . ' (' . strlen($tuData) . 'B) seconds to send a request to ' . $info['url'];
	} else {
		//echo 'Curl error: ' . curl_error($tuCurl);
	}

	curl_close($tuCurl);

	unlink($cookie_file_path);
	return $tuData;
}


} //Models_Ticket_OldTicket
