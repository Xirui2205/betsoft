<?php

class It6_Betradar_Category extends It6_Betradar_Node {

public $outrights = array();
public $tournaments = array();

public function addOutright($outright) {
	$this->outrights[] = $outright;
}

public function addTournament($tournament) {
	$this->tournaments[] = $tournament;
}

public function getDataToPreload(&$preloadedData) {
	foreach ($this->tournaments as &$tournament) {
		$tournament->getDataToPreload($preloadedData);
	}
	foreach ($this->outrights as &$outright) {
		$outright->getDataToPreload($preloadedData);
	}
}

public function freeData() {
	$this->outrights = array();
	$this->tournaments = array();
}

public function hasAnythingToImport(&$import) {
	if ($import->canImportMatches()) {
		if (!empty($this->tournaments))
			return true;
	}
	if ($import->canImportOutrights()) {
		if (!empty($this->outrights))
			return true;
	}
	return false;
}

public function import(&$import, &$sport) {
	if ($import->canImportMatches()) {
		foreach ($this->tournaments as &$tournament) {
			$tournament->import($import, $sport, $this);
		}
	}
	if ($import->canImportOutrights()) {
		foreach ($this->outrights as &$outright) {
			$outright->import($import, $sport, $this);
		}
	}
}

} // class
