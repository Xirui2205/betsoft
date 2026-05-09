<?php

class Models_ConfirmationsExport {

public static function getExportUrl($fileName) {
	return PROTOCOL . ADMINHOST . "/?section="
		. FinanceController::CONFIRMATIONS_EXPORT_ID
		. "&file=" . urlencode($fileName);
}

public static function getExports() {
	//TODO: prepare links and some other stuff
	$exports = It6_Bank_Batch_Export::getExports();
	foreach ($exports as &$e) {
		$file = $e;
		$e = array(
			'title' => $file,
			'url' => self::getExportUrl($file),
		);
	}
	return $exports;
}
	
} // class