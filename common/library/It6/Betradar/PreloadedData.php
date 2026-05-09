<?php

class It6_Betradar_PreloadedData {

public $sportId = null;

public $brSports = array();
public $dbSports = array();

public $brTournaments = array();
public $dbTournaments = array();

public $brCompetitors = array();
public $dbCompetitors = array();

public $columnSetsBetTypeIds = array();
public $betTypeColumnSets = array(); // betTypeId => betSubtypeId => columnId => (keys: bewaId, name)

public $betTypeIds = array();
public $dbBetTypes = array();
public $dbBetTypeTextNotes = array(); // sportId => betTypeId => textNote

public $betTypeSettingsIds = array();
public $dbBetTypeSettings = array();

public $brBets = array(); // brBetType => matchId => oddsType => effSpecialValue => true
public $dbBets = array(); // brBetType => matchId => oddsType => effSpecialValue => bewaBetId => bewaData

public $matchesWithResults = array();

public function __construct($sportId) {
	$this->sportId = $sportId;
}

public function addSport($brId) {
	$this->brSports[$brId] = $brId;
}

public function addTournament($brId) {
	$this->brTournaments[$brId] = $brId;
}

private function addCompetitors($competitors) {
	foreach ($competitors as $c) {
		$brId = $c['brTeamId'];
		if (!isset($this->brCompetitors[$brId]))
			$this->brCompetitors[$brId] = $c;
	}
}

private function addBetTypesForColumnSets($betTypeIds) {
	foreach($betTypeIds as $typeId)
		$this->columnSetsBetTypeIds[$typeId] = $typeId;
}

/**
 * Store bet types and bet type settings for preload
 * @param array $betTypeIds (tournamentId => typeId => TRUE|subtypeIds)
 */
private function addBetTypes($betTypeIds) {
	foreach($betTypeIds as $tournamentId => $typeIds) {
		foreach ($typeIds as $typeId => $subtypeIds) {
			$this->betTypeIds[$typeId] = $typeId;
			if (true === $subtypeIds || !isset($this->betTypeSettingsIds[$tournamentId][$typeId]))
				$this->betTypeSettingsIds[$tournamentId][$typeId] = $subtypeIds;
			else if (true !== $this->betTypeSettingsIds[$tournamentId][$typeId])
				$this->betTypeSettingsIds[$tournamentId][$typeId] = array_merge($this->betTypeSettingsIds[$tournamentId][$typeId], $subtypeIds);
		}
	}
}

private function addBets($brBetType, $bets) {
	foreach ($bets as $matchId => $oddTypes) {
		if (true === $oddTypes)
			$this->brBets[$brBetType][$matchId] = true;
		else {
			foreach ($oddTypes as $oddType => $effSpecialValues) {
				foreach ($effSpecialValues as $effSpecialValue)
					$this->brBets[$brBetType][$matchId][$oddType][$effSpecialValue] = $effSpecialValue;
			}
		}
	}
}

public function addMatch($match) {
	if ($match->isOutright)
		$this->addTournament($match->tournamentId);
	$this->addCompetitors($match->competitors);
	$this->addBetTypesForColumnSets($match->getBetTypesNeedingAllColumnSets());
	$this->addBetTypes($match->getBetTypesToPreload());
	//$this->addBets( $match->getBetradarBetType(), array($match->brId => $match->getBetsToPreload()) );
	$this->addBets( $match->getBetradarBetType(), array($match->brId => true));
}

/**
 * Add new data among preloaded data (rewrites old data if already exist)
 * @param integer $betTypeId
 * @param integer $betSubtypeId
 * @param array $columns Array(bewaColumnId => (keys: bewaId, name))
 */
public function addBetTypeColumnSet($betTypeId, $betSubtypeId, $columns) {
	$this->betTypeColumnSets[$betTypeId][$betSubtypeId] = $columns;
}

/**
 * Add BBAS bet structure(s)
 * @param array|struct $dbBet One or more BBAS bet struct @see It6_Betradar_Import::getBetRadarBets()
 */
public function addDbBet($dbBet) {
	$this->dbBets[ $dbBet['brBetType'] ][ $dbBet['brId'] ][ $dbBet['brOddsType'] ][ $dbBet['brSpecialValue'] ][ $dbBet['bewaId'] ] = $dbBet;
}

public function freeCategoryData() {
	$this->brTournaments = array();
}

public function updateDbBetParentId($brBetType, $brMatchId, $parentId) {
	if (empty($this->dbBets[$brBetType][$brMatchId]))
		return;
	foreach ($this->dbBets[$brBetType][$brMatchId] as $oddsType => &$specialValues) {
		foreach ($specialValues as $specialValue => &$dbBets) {
			foreach ($dbBets as $bbasId => &$dbBet) {
				$dbBet['parentId'] = ($bbasId == $parentId ? null : $parentId);
			}
		}
	}
}

public function load($import) {
	if ( false === ($this->dbSports = $import->getSports($this->brSports)) )
		return false;
	$bbasSportId = $this->dbSports[$this->sportId]['bewaId'];
	$this->dbTournaments = $import->getTournaments($this->brTournaments);
	$this->dbCompetitors = $import->getTeams($this->brCompetitors, $bbasSportId);
	if (!empty($this->columnSetsBetTypeIds))
		$this->betTypeColumnSets = $import->getColumnSetsForBetTypes($this->columnSetsBetTypeIds);
	$this->dbBetTypes = $import->getBetType($this->betTypeIds);
	$this->dbBetTypeTextNotes[$bbasSportId] = $import->getBetTypeTextNote($this->betTypeIds, $bbasSportId);
	$this->dbBetTypeSettings = $import->getBetTypeSettings($this->betTypeSettingsIds, $this->dbTournaments);
	$this->dbBets = $import->getBetradarBets($this->brBets);
	// preload import cache
	$betSubtypeIds = It6_Betradar_Match::getBetSubtypesFromConfig();
	$betSubtypeIds = array_flip($betSubtypeIds);
	foreach ($this->dbBets as $brBetType => $matchIds) { 
		foreach ($matchIds as $matchId => $oddsTypes) {
			foreach ($oddsTypes as $oddType => $specValues) {
				foreach ($specValues as $specValue => $bets) {
					foreach ($bets as $betId => $dbBet) {
						$betSubtypeIds[$dbBet['subtypeId']] = true;
					}
				}
			}
		}
	}
	$import->getBetSubtype(array_keys($betSubtypeIds));
}

public function getSport($brId) {
	return (isset($this->dbSports[$brId]) ? $this->dbSports[$brId] : null);
}

public function getTournament($brId) {
	return (isset($this->dbTournaments[$brId]) ? $this->dbTournaments[$brId] : null);
}

public function getCompetitor($brId) {
	return (isset($this->dbCompetitors[$brId]) ? $this->dbCompetitors[$brId] : null);
}

public function getBetType($typeId) {
	return (isset($this->dbBetTypes[$typeId]) ? $this->dbBetTypes[$typeId] : null);
}

public function getBetTypeTextNote($typeId, $sportId) {
	return (isset($this->dbBetTypeTextNotes[$sportId][$typeId]) ? $this->dbBetTypeTextNotes[$sportId][$typeId] : '');
}

public function getBetTypeSettings($tournamentId, $typeId, $subtypeId = null) {
	if (!empty($subtypeId)) {
		if (empty($this->dbBetTypeSettings[$tournamentId][$typeId][$subtypeId]))
			return null;
		else
			return $this->dbBetTypeSettings[$tournamentId][$typeId][$subtypeId];
	}
	else {
		if (empty($this->dbBetTypeSettings[$tournamentId][$typeId]))
			return null;
		else
			return $this->dbBetTypeSettings[$tournamentId][$typeId];
	}
}

public function getBetTypeColumnSets($betTypeId) {
	return (isset($this->betTypeColumnSets[$betTypeId]) ? $this->betTypeColumnSets[$betTypeId] : null);
}

/**
 * Current implementation returns just one bet structure
 * @param integer $brBetType
 * @param integer $brId
 * @param string $oddsType
 * @param string $specialValue
 * @param boolean $outright TRUE if it is outright and so should return more bets, FALSE if it is match and just one bet is expected
 * @return array|NULL
 */
public function getBetByBetradar($brBetType, $brId, $oddsType, $specialValue, $outright) {
	if (empty($this->dbBets[$brBetType][$brId][$oddsType][$specialValue]))
		return null;
	else {
// 		$bets = $this->dbBets[$brId][$oddsType][$specialValue];
// 		if (1 != count($bets)) {
// 			It6_Log::warn(
// 				"More bets found for BR_ID/BR_ODDS_TYPE/BR_SPEC_VAL: brId=$brId oddsType=$oddsType brSpecialValue=$specialValue bewaBetIds=["
// 				. implode(',', array_keys($bets)) . ']'
// 			);
// 		}
// 		return array_shift($bets);
		return $this->dbBets[$brBetType][$brId][$oddsType][$specialValue];
	}
}

/**
 * Returns all preloaded bets with given BR match ID
 * @param integer $brBetType
 * @param integer $brMatchId
 * @return array|NULL (brOddsType => brSpecialValue => BBAS_data)
 */
public function getBetsByMatchId($brBetType, $brMatchId) {
	if (empty($this->dbBets[$brBetType][$brMatchId]))
		return null;
	else
		return $this->dbBets[$brBetType][$brMatchId];
}

} // class
