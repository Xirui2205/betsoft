<?php
class TicketUtil {

	public static function isPaidOut($ticket, $db = null) {
		if (is_numeric($ticket)) {
			if (!isset($db))
				$db = Zend_Registry::get('db');
			$sql = "SELECT vyplacen FROM ticket WHERE ticket_id = ".intval($ticket);
			$rows = $db->query($sql)->fetchAll();
			if (empty($rows))
				throw new Exception('Nepodarilo se provest dotaz: vyber z tabulky ticket');
			$ticket = $rows[0];
		}
		if ($ticket['vyplacen'] == 1)
			return true;
		else 
			return false;
	}

	public static function getTicketAction(Array $h, $k, $nomenu, $isSuperAdmin = false) {
		$ret = array();

		$paidOut = (0 != $h['vyplacen']);
		$canceled = (1 == $h['zruseno']);
		if ($nomenu == false && (!$paidOut || $isSuperAdmin)) {
			$superMark = ($paidOut ? '!' : '');
			if ($canceled) {
				if (!$paidOut)
					$ret[] = '<input type="submit" onclick="if(!confirm(\''.I18n::tr('question_want_renew_ticket').'\'))return false;" name="renew['.$k.']"  value="'.I18n::tr('renew_ticket').$superMark.'" />';
			}
			else {
				if ($paidOut) {
					$reasonClass = '';
					$reasonJs = "if(\$('#duvod_zruseni_$k').val().length<5){alert('".I18n::tr('too_short_description')."');return false;}"; // must end to allow JS code to be appended
				}
				else {
					$reasonClass = 'hide';
					$reasonJs = '';
				} 
				$ret[] = '<input type="submit" name="delete['.$k.']" class="delete_ticket" onclick="'.$reasonJs.'if(!confirm(\''
					.I18n::tr('question_want_cancel_ticket').'\'))return false;"  value="'.I18n::tr('cancel_ticket').$superMark.
					'" />&nbsp;<input id="duvod_zruseni_'.$k.'" type="text" class="'.$reasonClass.'" name="duvod_zruseni['.$k.']" size="20" />';
			}
		}
		if (!$paidOut && $nomenu == false ) {
			if (!$canceled)
				$ret[] = '<input type="submit" name="delete_all_bets['.$k.']" class="delete_ticket" onclick="if(!confirm(\''.I18n::tr('question_want_rate_ticket_1').'\'))return false;"  value="'.I18n::tr('ticket_rate_1').'" />&nbsp;<input type="text" class="hide" name="duvod_zruseni['.$k.']" />';
		}

		$ws = Zend_Registry::get('ws');

		
		if (!$paidOut && $h['cash'] == 1) {
			$ret[] = '<button onclick="toggleTicketActions('.$k.');return false;">>></button>';
			$ret[] = '<div id="ticketAction_'.$k.'" style="display:none;">';
			
			if (empty($h['cancel_allowed'])) {
				$ret[] = '<input type="submit" onclick="if(!confirm(\''.I18n::tr('Do you really want to allow to cancel the ticket?').'\'))return false;" name="allow_cancelation['.$k.']"  value="'.I18n::tr('allow-branch-cancelation').'" />'; 
			}
			if (empty($h['collection_time'])) {
				$tmp = It6_LocalCache::get('a_tu_gta_hosts_html');
				if (empty($tmp)) {
					$hosts = $ws->Host->getAll();
	
					
					$tmp = "<select style=\"margin: 5px;\" onchange=\"this.form.submit(); return true;\" name=\"indcollection[{k}]\" id=\"indcollection_{k}\">";
					$tmp .= '<option value="">-- '.I18n::tr('Set individual collection').' --</option>';		
					$tmp .= '<option value="' . It6_Models_Host::ID_INTERNET . '">'.I18n::tr('Collect from this admin').'</option>';
					
					foreach ( $hosts as $host ) {
						if ( $host->hostId == It6_Models_Host::ID_INTERNET ) continue;
						$tmp .= '<option value="' . $host->hostId . '">' . I18n::tr('Collect in') . ': ' . $host->name . '</option>';
					}
					$tmp .= "</select>";
					It6_LocalCache::set('a_tu_gta_hosts_html', $tmp, 5*60);
				}
				$tmp = str_replace('{k}', $k, $tmp);
				$id = (empty($h['forced_collection_host_id']) ? '' : $h['forced_collection_host_id']);
				$tmp .= '<script type="text/javascript">$(document).ready(function() {$("#indcollection_' . $k . ' option[value=\'' . $id . '\']").attr("selected","selected");});</script>';
				$ret[] = $tmp;
				
				if ( $h['vyplacen'] != 0 && $h['forced_collection_host_id'] == It6_Models_Host::ID_INTERNET ) {
					$ret[] = '<input type="submit" onclick="if(!confirm(\''.I18n::tr('Do you really want to collect the ticket?').'\'))return false;" name="collect['.$k.']"  value="'.I18n::tr('Collect ticket').'" />';
				}
			}
			
			$ret[] = '</div>';
		}
		
		if (!empty($h['collection_time']) && $isSuperAdmin) {
			$ret[] = '<input type="submit" onclick="if(!confirm(\''.I18n::tr('Do you really want to cancel collect?').'\'))return false;" name="cancelCollect['.$k.']"  value="'.I18n::tr('Cancel_ticket_collect').'" />';
		}
		
		
		return empty($ret) ? '&nbsp;' : implode("\n", $ret);
	}

	public static function getTicketNotes(Array $h) {
			$t = '';

			if ($h['zruseno'] == 1) { 
				$t .= I18n::tr('ticket_canceled_by_book_id').' '.$h['zrusil_bookmaker_id'].' ('.I18n::tr('ticket_cancel_reason').' '.Help::Html($h['duvod_zruseni']).')';
			}
			if ($h['cancel_allowed'] == 1) {
				$t .= I18n::tr('ticket_cancellation_allowed');
			}
		/*	if ($h['system'] != 0) {
				$this->vrat .= '<tr><td colspan="3">'.($h['banker_num']>0?$h['banker_num'].' Banker +':'').' System '.$h['system'].'/'.(count($h['sazky'])-$h['banker_num']).' ('.($systemAr[(count($h['sazky'])-$h['banker_num'])][$h['system']]).' sázek)</td></tr>';
			}*/

			if ($h['free_bet_bonus'] == 1) {
				$t .= 'Free bet bonus';
			}

			return $t;
	}

	public static function getTicketStatus(Array $h) {
		if ($h['type']=='simple' || $h['type']=='kombi') {
			
			foreach ($h['bet'] as $k2 => $h2) {
					$status_ticket = $h2['status'];
					$result = explode(';',$h2['vysledek']);
					//TODO: refactor this shit...
					if ($h2['ticket_sazka_zrusena'] == 0 &&
							$h2['status'] != 1 &&
								in_array($h2['sloupec_id'],$result) &&
									$h['vyplacen'] == 1 &&
										$h2['status'] != 2) {
						$status_ticket = 1;  //spravny tip
						
					} else if ($h2['ticket_sazka_zrusena'] == 0 &&
								$h2['status'] != 1 && !in_array($h2['sloupec_id'],$result) &&
									$h['vyplacen'] == 1) {
										
						$status_ticket = 2;  //spatny tip
						
					} else if($h['vyplacen'] == 1 && $h2['status'] != 1 && $h2['status'] != 2) {
						$status_ticket = 3;  //vyplacen v kurzu 1
					}
			}

			/*if($h['kurz_celkem'] == 0) { 
				$h['kurz_celkem'] = 1;
			}*/

			/* rozhodnuti o jmenu stavu */
			if ($status_ticket == 0) {
				$status_ticket_name = I18n::tr('Not Paid out');
			//} else if ($h['system'] != 0 ) { //TODO: Ask somebody?
			} else if ( !empty($h['collection_time']) ) {
				$status_ticket_name = I18n::tr('Collected');
			} else if ($h['vyplacen'] != 0 ) {
				$status_ticket_name = I18n::tr('Paid out');
			} else if ($status_ticket == 1 ) {
				$status_ticket_name = I18n::tr('winning');
			} else if ($status_ticket == 2 ) {
				$status_ticket_name = I18n::tr('loss');
			} else {
				$status_ticket_name = I18n::tr('--STATUS UNKNOWN--');
			}

			if ($h['zruseno'] == 1) $status_ticket_name = I18n::tr('Rated 1');

			return $status_ticket_name;

		} else {
//			return $h['type'].'--STATUS NOT IMPLEMENTED--';
			switch ($h['helper']->result) {
				case -1 : return I18n::tr('loss'); break;
				case 0 : return I18n::tr('unknown'); break;
				case 1 : return I18n::tr('winning'); break;
				default : return I18n::tr('--STATUS UNKNOWN--');
			}
		}
	}


}
