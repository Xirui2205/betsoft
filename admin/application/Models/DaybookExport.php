<?php

class Models_DaybookExport {

	public static function export($data) {
		$export = array();

	
		foreach ( $data as $i ) {
			
			$date = substr($i->date,0,10);
			$date = str_replace('-','',$date);
			
			$export[] = array(
				'Sbornik'           => $i->daybookCollection,
				'Ev.číslo'          => $i->id,
				'Dat. účto'         => $date,
				'Poř.'              => $i->order,
				'Účet'              => $i->account,
				'Obrat MD'          => 0 == $i->order ? $i->amount : '',
				'Obrat Dal'         => 0 == $i->order ? '' : $i->amount,
				'Popis'             => $i->noteDaybook,
				'Kurs'              => 1,
				'RČ/IČO'            => $i->branchHandle,
				'Partner'           => It6_Models_Host::ID_INTERNET == $i->hostId ? 'Internet('.$i->userId.')' : $i->hostName,
				'Kód měny'          => 'CZK',
				'Fáze'              => '2',
				'Stav'              => '0',
				'Druh Data'         => '3',
				'PZ'                => $i->branchHandle,
				'Obrat MD v měně'   => $i->order == 0 ? $i->amount : 0,
				'Obrat DAL v měně'  => $i->order == 1 ? $i->amount : 0
			);
		}

		return It6_Models_ExportHelper::assocArrayToCsv($export);
	}

}

