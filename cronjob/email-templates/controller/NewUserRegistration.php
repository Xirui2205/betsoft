<?php

class NewUserRegistration extends It6_Cron_Job_Email_UserEmail {
	
	private $BRANCH_CONTROLLER_ID	= 23;
	private $CLOSEST_BRANCH_COUNT	= 5;

	private static function file_get_contents_curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public function prepareEmail() {
		parent::prepareEmail();
		$ws = Zend_Registry::get('ws');
		$dbAdmin = Zend_Registry::get('admindb');

		$extensions = array();
		$cols = array('username','street','town','countryName','registrationTime','languageId');
		$extensions[] = new It6_WsExtension_Client_Columns('columns', $cols);
		$filterDef = array(array('?'=> array('userId' => $this->params[static::PARAM_USER_ID]), 'OP' => '='));
		$extensions[] = new It6_WsExtension_Client_Filter('def-filter', $filterDef);

		$this->user = $ws->ext($extensions)->User->getAll();
		$this->user = It6_ArrayWrapper::toNativeArray($this->user);
		$this->user = $this->user[0];

		$this->branchController = It6_Models_ControllerConvert::getByIdAndLang($this->BRANCH_CONTROLLER_ID, $this->user['languageId']);
		$this->topBranchesController = $this->branchController;
		$templateVariables = array(
			'{{top_branches_url}}' => PROTOCOL . WEBHOST
				. "/{$this->topBranchesController['langIso']}/{$this->topBranchesController['reqController']}/"
				. ($this->topBranchesController['reqAction'] != 'index' ? "{$this->topBranchesController['reqAction']}/" : '' ),
			'{{nearest_branch_url}}' => PROTOCOL . WEBHOST
				. "/{$this->branchController['langIso']}/{$this->branchController['reqController']}/"
				. ($this->branchController['reqAction'] != 'index' ? "{$this->branchController['reqAction']}/" : '' )
				. '?addr=' . urlencode("{$this->user['street']} {$this->user['town']}")
				. '&zoom=city&markerType=home',
			'{{username}}' => $this->user['username'],
		);
		$this->detailTemplatePlaceholders = array_keys($templateVariables);
		$this->detailTemplateValues = array_map(function($v) {
				return htmlspecialchars($v);
			},
			array_values($templateVariables)
		);

		$clBranches = array();

		$address = urlencode($this->user['street'].",".$this->user['town'].",Czech Republic");
		$region = "CZE";
		$json = self::file_get_contents_curl("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
		$decoded_adr = json_decode($json);

		if (count($decoded_adr->{'results'}) != 0) {
			$latitude = $decoded_adr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$longitude = $decoded_adr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

			$sql = "SELECT branch.*, 
					( 3959 * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance 
					FROM branch WHERE is_active = 1 HAVING distance < 25 ORDER BY distance LIMIT 0,5";
			$branches = $dbAdmin->query($sql)->fetchAll();

			if ( count($branches) != 0 ) {
				foreach ($branches as $key => $branch) {
					$branch['openingHours'] = Webservice_Branch::getOpeningHoursByBranchId($branch['id']);
					$clBranches[] = $branch;
				}
			}
		}

		$this->clBranches = $clBranches;
	}
}