<?php

/**
 * @package    main
 */


class Limity{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * pole menu
 * @access private
 * @var array
 */
private  $menu = array();

/**
 * navratove pole
 * @access private
 * @var array
 */
public  $redirect = array();

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * pravo zmeny v sekci
 * @access private
 * @var int
 */
private  $update = 0;

/**
 * pravo vymazani v sekci
 * @access private
 * @var int
 */
private  $delete = 0;

private $messages = '';

  public function __construct($section=1,$dbGame=null){



  }

  /**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @param string $url chybna adresa
 * @param string $lang jazyk stranek
 * @param string $sekce v jake sekci se nachazime
 * @return void
 */
  public function runAction($url="",$lang="en"){


  	if(isset($_POST['submit_limit'])){

  	 if($this->update){

        $this->updateLimit();

     }else
        $this->vrat .= '<div class="errormsg">Nemáte právo provést tuto akci</div>';

  	}

    $this->limitFieald();



  }

   /**
 * Aktualizace limitu
 * @return string
 */
public function updateLimit() {

//var_dump($_POST);
	
	foreach($_POST['sport'] AS $sport_id=>$h) {
		if(!ctype_digit($h)) continue;
		
		Zend_Registry::get('zdb_game')->query('replace into limity (sport_id,limit_den) values(?,?)',array($sport_id,$h));
		
		if(isset($_POST['user'][$sport_id]) && ctype_digit($_POST['user'][$sport_id]) && isset($_POST['uid']) && ctype_digit($_POST['uid'])) {

			$select = Zend_Registry::get('zdb_game')
						->select()
						->from(array("a"=>'limity_user'),array('user_id'))
						->where('user_id=?',intval($_POST['user_id']))
						->where('sport_id=?',intval($sport_id));

			$stm  = $select->query();
			$rowD = $stm->fetchAll();
//			var_dump('xx');
//var_dump($rowD);
			if(count($rowD) == 0) {
				$data = array();
				$data['sport_id'] = intval($sport_id);
				$data['user_id'] = intval($_POST['uid']);
				$data['limit_den_individual'] = intval($_POST['user'][$sport_id]);

				try {
					Zend_Registry::get('zdb_game')->insert('limity_user',$data);
				} catch(Zend_Exception $e){
					It6_Log::err(
						"Error inserting user sport limit for sport id '%sport%', user id %user%.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('sport' => $sport_id, 'user' => $_POST['uid'])
					);
					throw new ExHandler($e->getMessage(),"admin_ex_db");
				}
			} else {
				$data = array();
				$data['limit_den_individual'] = intval($_POST['user'][$sport_id]);
				//$data['sport_id'] = intval($sport_id);
				try {

					Zend_Registry::get('zdb_game')->update('limity_user', $data, array('user_id = ?' => intval($_POST['user_id']),'sport_id = ?'=>intval($sport_id)));
					
				} catch(Zend_Exception $e) {
					It6_Log::err(
						"Error updating user sport limit for sport id '%sport%', user id %user%.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('sport' => $sport_id, 'user' => $_POST['user_id'])
					);
					throw new ExHandler($e->getMessage(),"admin_ex_db");

				}
			}
		}
	}
	$this->messages = UiUtil::printMessages('update-ok');
	It6_Log::info(
		"Daily limit for sport id '%sport%' was updated.",
		It6_Log::TAG_ADMIN_OPERATION,
		array('sport' => $_POST['sport'])
	);
}


 /**
 * Vraci pole limitu
 * @return string
 */
  public function limitFieald(){


  	  $select = Zend_Registry::get('zdb_game')->select()->from(array("a"=>'sport'),array('ns'=>'sport_id','t.limit_den','nazev'=>'translate(a.nazev,1)'))
                                           ->joinleft(array('t'=>'limity'),'a.sport_id=t.sport_id');

    $stm  = $select->query();
    $rowD = $stm->fetchAll();


    $sport = array();
    $user = array();

    foreach($rowD as $h){

    	$sport[$h['ns']]['limit'] = $h['limit_den'];
    	$sport[$h['ns']]['nazev'] = $h['nazev'];

    }


	if(isset($_POST['user_id']) && ctype_digit($_POST['user_id'])) {
		$select = Zend_Registry::get('zdb_game')
					->select()
					->from(array("a"=>'sport'),array('ns'=>'a.sport_id','t.vycerpal','t.limit_den_individual','nazev'=>'translate(a.nazev,1)'))
					->join(array('t'=>'limity_user'),'t.sport_id=a.sport_id')
					->where('t.user_id=?',$_POST['user_id']);

		$stm  = $select->query();
		$rowD = $stm->fetchAll();

		foreach($rowD as $h){
			$user[$h['ns']]['limit'] = $h['limit_den_individual'];
			$user[$h['ns']]['vycerpal'] = $h['vycerpal'];
		}
	}
	$this->vrat .= '<h3>'.I18n::tr('Bet limits per sport/day').'</h3>';

	$this->vrat .= $this->messages;

	$this->vrat .= '<form method="post">';

	/* FILTER */
	$this->vrat .= '
		<table class="table-filter">
			<tr>
				<th colspan="2">Filter</th>
			</tr>
			<tr>
				<td>User ID</td><td><input type="text" name="user_id" value="'. (isset($_POST['user_id'])?$_POST['user_id']:'') .'"/> <input type="submit" name="submit_user_id" value="Odeslat"/></td>
			</tr>
		</table>';

	$this->vrat .= '<table class="table-list">';
	$this->vrat .= '<tr><th>'.i18n::tr('Sport').'</th><th>'.i18n::tr('Limit').' ['.CENTRAL_CURRENCY_NAME.']</th>';
	if(isset($_POST['user_id']) && ctype_digit($_POST['user_id'])) $this->vrat .= '<th>Vyčerpal ['.CENTRAL_CURRENCY_NAME.']</th><th>Individuální limit ['.CENTRAL_CURRENCY_NAME.']</th>';
	$this->vrat .= '</tr>';
	
	foreach($sport as $k=>$h){
		$this->vrat .= '<tr><td>'.Help::html($h['nazev']).'</td><td><input type="text" value="'.$h['limit'].'" name="sport['. $k .']" /></td>';
		
		if(isset($_POST['user_id']) && ctype_digit($_POST['user_id'])) {
			$this->vrat .= '<td align="center">'. (!empty($user[$k]) ? $user[$k]['vycerpal'] : '-') .'</td><td><input type="text" value="'.(!empty($user[$k]) ? $user[$k]['limit'] : '-').'" name="user['. $k .']" /></td>';
		}
	}
	
	$this->vrat .= '</table>';
	$this->vrat .= '<div class="actions">
						<input type="submit" name="submit_limit" value="'.I18n::tr('Save').'"/>
					</div>
						<input type="hidden" name="uid" value="'.!isset($_POST['user_id']) ? '' : $_POST['user_id'].'"/>';
	$this->vrat .= '</form>';
	}

 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){

    return $this->vrat;

  }


    /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){

   $this->update = $update;
   $this->delete = $delete;

  }


  public function __destruct(){




  }


}


?>
