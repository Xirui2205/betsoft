<?php

class It6_Betradar_Tournament  extends It6_Betradar_Node {

public $matches = array();

public function addMatch($match) {
	$this->matches[] = $match;
	$match->setTournamentId($this->brId);
}

public function getDataToPreload(&$preloadedData) {
	$preloadedData->addTournament($this->brId);
	foreach ($this->matches as $match) {
		$match->getDataToPreload($preloadedData);
	}
}

public function import(&$import, &$sport, &$category) {
	foreach ($this->matches as $match) {
		$match->import($import, $sport, $category, $this);
	}
}

} // class
