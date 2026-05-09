<?php
/**
 * Maps placeholder names for JS files to file names.
 * Used for dynamic inclusion if JS scripts into (X))HTML.
 */

class It6_JsFiles extends It6_MemberArray {

protected $_files = array();

public function __construct() {
	$this->registerFile('eventAjax', 'event.ajax.js');
	$this->registerFile('voucherAjax', 'voucher.ajax.js');
	$this->registerFile('adminAjax', 'admin.ajax.js');
	$this->registerFile('articleAjax', 'article.ajax.js');
	$this->registerFile('branchAjax', 'branch.ajax.js');
	$this->registerFile('commonAjax', 'common.ajax.js');
	$this->registerFile('transactionTypeAjax', 'transactionType.ajax.js');
	$this->registerFile('userAjax', 'user.ajax.js');
	$this->registerFile('parameterAjax', 'parameter.ajax.js');
	$this->registerFile('wsForm', 'wsForm.js');
	$this->registerFile('charCounter', 'charCounter.js');
	$this->registerFile('promoAdministration', 'promoAdministration.js');
	$this->registerFile('popup', 'popup.js');
	$this->registerFile('fundTakeinsAjax', 'fund-takeins.ajax.js');
	$this->registerFile('typeAjax', 'type.ajax.js');
	$this->registerFile('smsAjax', 'sms.ajax.js');
	$this->registerFile('clientCardAjax', 'clientCard.ajax.js');
	$this->registerFile('affiliatePartnerAjax', 'affiliatePartner.ajax.js');
	$this->registerFile('affiliateBannerAjax', 'affiliateBanner.ajax.js');
	$this->registerFile('pageMetadataAjax', 'pageMetadata.ajax.js');
	//$this->registerFile('jqueryUI', 'jquery-ui-1.8.21.custom.min.js');
	$this->registerFile('jqueryUI', 'jquery-ui-1.10.1.custom.min.js');
	$this->registerFile('bettingStatisticsAjax', 'bettingStatistics.ajax.js');
	$this->registerFile('highCharts', 'highcharts/highcharts.js');
	$this->registerFile('highChartsExporting', 'highcharts/exporting.js');
	// $this->registerFile('highChartsGray', 'highcharts/gray.js');
	// add additional JS files if needed
}

/**
 * @param $placeholder File name placeholder (used in controllers for activation).
 * @param $fileName JS file name for given placeholder.
 * @param $activated TRUE|FALSE value assigned to placeholder item
 */
public function registerFile($placeholder, $fileName, $activated = null) {
	if (isset($activated))
		$this->$placeholder = $activated;
	$this->_files[$placeholder] = $fileName;
}

public function getActiveFiles() {
	$files = array();
	foreach ($this->_files as $placeholder => $file) {
		if ($this->has($placeholder))
			$files[] = $file;
	}
    
	return $files;
}

} // class It6_JsFiles