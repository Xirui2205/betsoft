<?php
/**
 * @version    1.0
 * @link       help
 
 */

/**
 * Trida pro praci s newslettery
 *
 *
 * <code>
 * 
 * </code>
 *
 * @package    Main
 */
 
class Calendar extends Template{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $ud_vrat = "";

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

/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */              
private  $dbGame;

/**
 * aktualni sekce
 * @access private
 * @var int
 */ 
private  $section;                

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $db objekt spojeni s databazi
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0,$db=null){
   
   $this->section =  $section;
  

    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

		
  }
  
/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){
  
	  #odelsani newsletteru#
      if(isset($_POST['send_n'])){
	   
	   if($this->update)
	    $this->SendNews();
	   else
	      $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editování v této sekci</div>\n";
		  
		$this->ShowZpravy();
		
	  }
      else
	     $this->ShowZpravy();
	   
	   $this->dbGame->disconnect();
  }
  
  
 /**
 * odelslani newsltter
 * @return void
 */
  public function SendNews(){
     
	 $user = $jazyk = $iso = $u = $d = $section = $menu = $udalost = array();
	 $preklad = new Preklady();
	 
	
	 
	 if(isset($_POST['predmet']) && is_array($_POST['predmet'])){
	 
	 $this->dbGame->autoCommit(false);
		   
	 foreach($_POST['predmet'] as $k=>$h){

	 	  if(isset($_POST['active'][$k])){
		    
			$jazyk[] = $k;
		    $sql = "select iso from jazyky where lang_id=".$k;
            $res =& $this->dbGame->query($sql);
            if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber jazyku',"admin_ex_db");
	        
			if ($row =& $res->fetchRow()) $iso[$k] = $row['iso'];else{$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: max z novinek',"admin_ex_db");}
			
		  }	  
		
	 }
	 
	 $sql = "select udalost_id,sport_id,nazev from udalost";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatelu',"admin_ex_db");
     $sport_udalost = array();
     while ($row =& $res->fetchRow()){
     	
     	$p = $preklad->FindPreklad($row['nazev']);
     	
     	$udalost[$row['udalost_id']] = $p;
     	$sport_udalost[$row['udalost_id']] = $row['sport_id'];
     }
     
	 $sql = "select email,lang_id from uzivatel where newsletter=1";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatelu',"admin_ex_db");
	 
	  $p = 0;  
	 while ($row =& $res->fetchRow()){
	   
	    if(Help::valideMail($row['email']) && mb_strlen($row['email']) > 0) {$user[$row['lang_id']][] = $row['email'];$p++;}
	   
	 }
	 
   
	 
	 include('shared/htmlMimeMail/htmlMimeMail.php');
	 
	 $yy = 1;
	 $mail_id = 0;
	 
	 
	 foreach($jazyk as $lang_id){
	    echo $lang_id;
	   if(!isset($_POST['active'][$lang_id])) continue;
	   if(!isset($_POST['predmet'][$lang_id]) || mb_strlen($_POST['predmet'][$lang_id]) < 1 || mb_strlen($_POST['predmet'][$lang_id]) > 180){$this->dbGame->rollback();$this->dbGame->autoCommit(true);$this->vrat .= "<div class=\"errormsg\">Předmět (".$lang_id.") musí mít 1-180 znaků</div><br />";continue;}

	   if($yy == 1){
	   
	     $sql = "replace into mail_stat(type,datum) values (2,'".It6_Date::dbNow()."')";
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: zalozeni newsletteru',"admin_ex_db");
	     
         $sql = "select max(mail_id) AS maxi from  mail_stat";
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: zalozeni newsletteru',"admin_ex_db");
         
         
         if ($row2 =& $res->fetchRow()) $mail_id = $row2['maxi'];
         	
         
	   }
	   
	   $yy++; 
	   
	   $sql = "select * from sekce_herna where lang_id=".$lang_id;
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: update tiketu',"admin_ex_db");}
       
	   while ($row2 =& $res2->fetchRow()){
		  $section[$lang_id][$row2['sekce_herna_id']] = $row2['url'];
	   }
		
	   $sql = "select * from preklady_menu where (menu_id=10 or menu_id=35 or menu_id=36 or menu_id=38) and lang_id=".$lang_id;
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: update tiketu',"admin_ex_db");}
       
	   while ($row2 =& $res2->fetchRow()){
		  $menu[$lang_id][$row2['menu_id']] = $row2['uri'];
	   }
	   
	   
	   $stpl = new TemplatePower("_tpl/calendar.tpl");
	   $stpl->prepare();
	   
	   
	   $u[$lang_id][1] = $preklad->FindPreklad('calendar',$lang_id); $u[$lang_id][1] = $u[$lang_id][1][$lang_id]; //kalendar


       $u[$lang_id][9]='';//$u[$lang_id][9] = $preklad->FindPreklad('g_motto',$lang_id); $u[$lang_id][9] = $u[$lang_id][9][$lang_id]; //moto
	   $u[$lang_id][10] = $preklad->FindPreklad('tit_more_info',$lang_id); $u[$lang_id][10] = $u[$lang_id][10][$lang_id]; //vice informaci
       $u[$lang_id][11] = $preklad->FindPreklad('mail_unsubscribe',$lang_id); $u[$lang_id][11] = $u[$lang_id][11][$lang_id]; //text pod carou

       $d[$lang_id][4] = $preklad->FindPreklad('day1',$lang_id); $d[$lang_id][4] = $d[$lang_id][4][$lang_id]; //Pondeli
	   $d[$lang_id][5] = $preklad->FindPreklad('day2',$lang_id); $d[$lang_id][5] = $d[$lang_id][5][$lang_id]; //Pondeli
	   $d[$lang_id][6] = $preklad->FindPreklad('day3',$lang_id); $d[$lang_id][6] = $d[$lang_id][6][$lang_id]; //Pondeli
	   $d[$lang_id][7] = $preklad->FindPreklad('day4',$lang_id); $d[$lang_id][7] = $d[$lang_id][7][$lang_id]; //Pondeli
	   $d[$lang_id][8] = $preklad->FindPreklad('day5',$lang_id); $d[$lang_id][8] = $d[$lang_id][8][$lang_id]; //Pondeli
	   $d[$lang_id][9] = $preklad->FindPreklad('day6',$lang_id); $d[$lang_id][9] = $d[$lang_id][9][$lang_id]; //Pondeli
	   $d[$lang_id][10] = $preklad->FindPreklad('day7',$lang_id); $d[$lang_id][10] = $d[$lang_id][10][$lang_id]; //Pondeli


//	   WEBHOST.$iso[$lang_id].'/'.$section[$lang_id][1].$menu[$lang_id][10]
	   
	   $stpl->assign("CALENDAR",Help::Html(mb_strtoupper($u[$lang_id][1],'UTF-8')));
       
	   $stpl->assign("DAY1",Help::Html($d[$lang_id][4]));
       $stpl->assign("DAY2",Help::Html($d[$lang_id][5]));
       $stpl->assign("DAY3",Help::Html($d[$lang_id][6]));
       $stpl->assign("DAY4",Help::Html($d[$lang_id][7]));
       $stpl->assign("DAY5",Help::Html($d[$lang_id][8]));
       $stpl->assign("DAY6",Help::Html($d[$lang_id][9]));
       $stpl->assign("DAY7",Help::Html($d[$lang_id][10]));
       
	   $stpl->assign("MOTTO",Help::Html(mb_strtoupper($u[$lang_id][9],'UTF-8')));
	   
	   $stpl->assign("NOTE",Help::Html(mb_strtoupper($u[$lang_id][11],'UTF-8')));
	   
	   $image = array(1001=>"football.gif",1003=>"tennis.gif",1006=>"basketb.gif",1011=>"hockey.gif",1012=>"fun.gif",1013=>"voleyball.gif",1014=>"dart.gif",1015=>"rugby.gif",1016=>"ping_pong.gif",1017=>"motosport.gif",1018=>"ski.gif",1019=>"pool.gif",1020=>"handball.gif",1021=>"globus.gif",1022=>"florball.gif",1023=>"horse.gif",1024=>"box.gif",1025=>"baseball.gif",1026=>"amfootball.gif",1027=>"cyklo.gif",1028=>"golf2.gif",1029=>"formule.gif",1030=>"motobike.gif",1031=>"special.gif",1032=>"waterpolo.gif",1033=>"skijump.gif",1034=>"biatlon.gif");
	   
	   $sp_vrat = "";

	   
	   $mail = new htmlMimeMail();
	  // echo "<pre>";print_r($_FILES);

	   #TOP NEWS#
	   if((isset($_POST['cal'][$lang_id][1]['head']) && mb_strlen($_POST['cal'][$lang_id][1]['head']) > 2) || (isset($_POST['cal'][$lang_id][2]['head']) && mb_strlen($_POST['cal'][$lang_id][2]['head']) > 2) || (isset($_POST['cal'][$lang_id][3]['head']) && mb_strlen($_POST['cal'][$lang_id][3]['head']) > 2) ){

	   	
	   	for($x=1;$x<4;$x++){
	   	  
	   	if(mb_strlen($_POST['cal'][$lang_id][$x]['head']) < 3) continue;
	   	 
	   	
	   	 $obr = '';
	   	 if(isset($_FILES['cal'.$lang_id.$x.'file']['tmp_name'])) {$mail->addHTMLImage($mail->getFile($_FILES['cal'.$lang_id.$x.'file']['tmp_name']),$_FILES['cal'.$lang_id.$x.'file']['name'],'image/jpeg'); $obr = '<img src="'.$_FILES['cal'.$lang_id.$x.'file']['name'].'"  style=" margin:0px 7px 10px 7px;border:0px;" alt="" />';}
	   	
	   	 $sp_vrat .='   <div style="width:221px;height:294px;background:white;float:left;margin:20px '.($x==3?'0':'18').'px 20px 0px;border-bottom:1px solid #DAE0E8;">
          <div style="width:197px;_width:207px;_w\idth:197px;padding:10px 2px 10px 8px;margin:7px;background:#B70700;color:white;font-weight:bold;">'.$_POST['cal'][$lang_id][$x]['head'].'</div>
          '.$obr.'
          <div style="background:white;width:207px;color:black;margin:0px 7px 15px 7px;">
           
          <span style="color:#383838;font-weight:bold;font-size:14px">'.$_POST['cal'][$lang_id][$x]['datum'].'</span>'.(mb_strlen($_POST['cal'][$lang_id][$x]['datum'])>1?'<br /><br />':'').'
          '.nl2br($_POST['cal'][$lang_id][$x]['text']).'
           '.(isset($_POST['nudalost'][$lang_id][$x]) && $_POST['nudalost'][$lang_id][$x] != 0?'<a href="'.WEBHOST.$iso[$lang_id].'/'.$section[$lang_id][1].$menu[$lang_id][10].'?mail='.$mail_id.'&e_add='.intval($_POST['nudalost'][$lang_id][$x]).'" style="color:#C73D38;float:right;">'.$u[$lang_id][10].'</a>  ':"" ).'
          </div>
           </div>';
	   	}
	   	
	   }
	   
	   $ma_vrat = '';$help = 0;
	   for($x=4;$x<11;$x++){
	   	
	   	$help++;
	   	
	   	if(isset($_POST['cal'][$lang_id][$x]['datum']) && mb_strlen($_POST['cal'][$lang_id][$x]['datum']) > 2){
	   	
	   	$ma_vrat .= ' <!--Block--><a name="n'.$x.'"></a><div style="float:left;width:349px;border-bottom:2px solid #DAE0E8;">
          
          <div style="float:left;width:100px;padding:8px 2px 8px 10px;background:#B70700;color:white;border-bottom:2px solid #880600;">'.mb_strtoupper($d[$lang_id][$x]).'</div>
          <div style="float:left;width:60px;padding:8px 0px 8px 0px;background:#880600;color:white;border-bottom:2px solid #880600;text-align:center">'.$_POST['cal'][$lang_id][$x]['datum'].'</div>
          ';
	   	 
	   	 for($y=1;$y<4;$y++){
	   	 	if(!isset($_POST['udalost'][$lang_id][$x][$y]) || $_POST['udalost'][$lang_id][$x][$y] == 0) continue;
         $ma_vrat .= '<div style="float:left;clear:both;width:329px;_width:349px;_w\idth:329px;height:48px;padding:10px;border-bottom:1px dotted #DAE0E8;border-right:1px solid #DAE0E8;background:white;">
             
             <span style="color:#383838;font-weight:bold;font-size:14px;">'.$_POST['cal'][$lang_id][$x]['od'][$y].'</span><img src="'.$image[$sport_udalost[intval($_POST['udalost'][$lang_id][$x][$y])]].'" style="border:0px;float:left;padding:1px 4px 4px 0px;" /><br />
              <a href="'.WEBHOST.$iso[$lang_id].'/'.$section[$lang_id][1].$menu[$lang_id][10].'?mail='.$mail_id.'&e_add='.intval($_POST['udalost'][$lang_id][$x][$y]).'" style="color:#C73D38;float:right;">'.$u[$lang_id][10].'</a>  
              '.$udalost[intval($_POST['udalost'][$lang_id][$x][$y])][$lang_id].'
                
          </div>';
	   	 }
          
         $ma_vrat .= '</div><!--END Block-->';
       
	   	}
	   	
	   	 if($help == 2) {$help=0;$ma_vrat .= '<div style="float:left;clear:both;height:20px;width:100%"></div>';}
	   	
	   }
	   
	   $stpl->assign("NEWS",$sp_vrat);
	   $stpl->assign("MATCH",$ma_vrat);
	   
	   foreach($image as $h8){
	   	$mail->addHTMLImage($mail->getFile($_SERVER["DOCUMENT_ROOT"].'/_clip_mail/'.$h8),$h8,'image/gif');
	   }
	   
	   $mail->addHTMLImage($mail->getFile($_SERVER["DOCUMENT_ROOT"].'/_clip_mail/logo.jpg'),'logo.jpg','image/jpeg');
	   //$mail->addHTMLImage($mail->getFile($_SERVER["DOCUMENT_ROOT"].'/_clip_mail/stats_bet.gif'),'stats_bet.gif','image/gif');
	   
       $mail->setTextCharset("UTF-8");
	   $mail->setHeadCharset("UTF-8");
	   $mail->setHTMLCharset("UTF-8");  $mail->html_charset = "UTF-8";$mail->text_encoding = "UTF-8";
	   $mail->setHTMLEncoding("base64");
	   $mail->setFrom(NOREPLY);
	   $mail->setReturnPath(NOREPLY);
       $mail->setHtml( $stpl->getOutputContent());
	   $mail->setSubject($_POST['predmet'][$lang_id]);
	   $mail->setBcc(implode(";",$user[$lang_id]));

	   $result = $mail->send(INFOMAIL);
       if(!$result){
	 
	     $this->dbGame->rollback();
	     $this->dbGame->autoCommit(true);
	     throw new ExHandler('s'.var_dump($mail->headers).'Nepodarilo se poslat maily',"game_ex_db");
	 
	   }
	   
	      
	 }
	 
	 $this->vrat .= "<div class=\"okmsg\">Newsletter byl odeslan (".$p.")</div><br />";
	It6_Log::info(
		"Newsletter sent.",
		It6_Log::TAG_ADMIN_OPERATION); 
     $this->dbGame->commit();
	   
	}
	 
	
  }
  

  
/**
 * formular pro novy a odesleny newsletter
 * @param array $data pole obsahu pro vsechny jazyky
 * @param int $news_id id novinky
 * @return void
 */
  private function ShowForm(array $data=NULL,$news_id=NULL){
    
	
	 $jazyky = $pjazyky = $js = array();
    
	 if($news_id==NULL) $news_id = 0;
	 
   	 $sql = "select lang_id,iso,alt_text from jazyky order by lang_id";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");
	 
	 while ($row =& $res->fetchRow()){
	 
	    $jazyky[$row['lang_id']] = $row['alt_text'].': '.$row['iso'];
	    $pjazyky[] = $row['lang_id'];
		$js[] = '\'html['.$news_id.']['.$row['lang_id'].']\'';
	
	 }
	
	
	 $this->vrat .= "<form method=\"post\"  enctype=\"multipart/form-data\" action\"?create_news=1&section=".$this->section."\">";
	 
	 	 $x = 0;
	 foreach($jazyky as $k=>$h){
	 
	  $this->vrat .= '<input type="button" id="data_b_'.$k.'" name="data_b_'.$k.'" onclick="NewsLang(new Array('.implode(',',$pjazyky).'),'.$k.')" '.($x==0?'style="color:black;font-weight:bold;border:2px solid black"':'').' class="sinput3" value="'.Help::Html($h).'" />&nbsp;&nbsp;';
	 
	  $x++;
	 
	 }
	 
	  $x = 0;
	 foreach($jazyky as $k=>$h){
	  
	 	
	  $this->vrat .= '<div style="'.($x==0?'display:block':'display:none').'" id="data_'.$k.'" name="data_'.$k.'">';
	  $this->vrat .= '<br />Aktivovat: <input type="checkbox" class="no" name="active['.$k.']" value="" />';
	  $this->vrat .= '<br />Předmět: <input type="text" maxlength="100" name="predmet['.$k.']" value="'.(isset($_POST['predmet'][$k])?Help::Html($_POST['predmet'][$k]):"").'" /> <hr />';
      
	  $this->vrat .= '<table>';
	  
	  $this->vrat .= '<tr><th colspan="4" align="left"><h3>Hlavička</h3></th></tr>';
	  
	  $this->vrat .= '<tr><td colspan="4" align="left"><strong>Text 1:</strong></td></tr>';
	  $this->vrat .= '<tr><td>Nadpis: </td><td><input type="text" name="cal['.$k.'][1][head]"  value="'.(isset($_POST['cal'][$k][1]['head'])?Help::Html($_POST['cal'][$k][1]['head']):"").'" /></td><td>Datum: </td><td><input type="text"  name="cal['.$k.'][1][datum]" value="'.(isset($_POST['cal'][$k][1]['datum'])?Help::Html($_POST['cal'][$k][1]['datum']):"").'" class="mandatory sinput3"  /><a href="javascript:ok1 = window.open(\'calendar.php?cas=cal['.$k.'][1][datum]\',\'\',\'width=140,height=247\');void(0);"><img src="'.HOST.'_clip/calendar.gif" alt="Týmy" class="img" /></a></td></tr>';
	  $this->vrat .= '<tr><td valign="top">Text: </td><td colspan="3"><textarea name="cal['.$k.'][1][text]" cols="35" rows="6">'.(isset($_POST['cal'][$k][1]['text'])?Help::Html($_POST['cal'][$k][1]['text']):"").'</textarea></td></tr>';
	  $this->vrat .= '<tr><td valign="top">Obrázek: </td><td colspan="3"><input type="file" style="width:500px"  name="cal'.$k.'1file" /></td></tr>';
	  $this->vrat .= '<tr><th colspan="2" align="left">Vyberte událost: </th><th colspan="2" align="left">'.$this->RetUdalost($k,1).'</th></tr>';
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
      
	  $this->vrat .= '<tr><td colspan="4" align="left"><strong>Text 2:</strong></td></tr>';
	  $this->vrat .= '<tr><td>Nadpis: </td><td><input type="text" name="cal['.$k.'][2][head]"  value="'.(isset($_POST['cal'][$k][2]['head'])?Help::Html($_POST['cal'][$k][2]['head']):"").'" /></td><td>Datum: </td><td><input type="text"  name="cal['.$k.'][2][datum]" value="'.(isset($_POST['cal'][$k][2]['datum'])?Help::Html($_POST['cal'][$k][2]['datum']):"").'" class="mandatory sinput3"  /><a href="javascript:ok1 = window.open(\'calendar.php?cas=cal['.$k.'][2][datum]\',\'\',\'width=140,height=247\');void(0);"><img src="'.HOST.'_clip/calendar.gif" alt="Týmy" class="img" /></a></td></tr>';
	  $this->vrat .= '<tr><td valign="top">Text: </td><td colspan="3"><textarea name="cal['.$k.'][2][text]" cols="35" rows="6">'.(isset($_POST['cal'][$k][2]['text'])?Help::Html($_POST['cal'][$k][2]['text']):"").'</textarea></td></tr>';
	  $this->vrat .= '<tr><td valign="top">Obrázek: </td><td colspan="3"><input type="file" style="width:500px"  name="cal'.$k.'2file" /></td></tr>';
	  $this->vrat .= '<tr><th colspan="2" align="left">Vyberte událost: </th><th colspan="2" align="left">'.$this->RetUdalost($k,2).'</th></tr>';
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
	  $this->vrat .= '<tr><td colspan="4" align="left"><strong>Text 3:</strong></td></tr>';
	  $this->vrat .= '<tr><td>Nadpis: </td><td><input type="text" name="cal['.$k.'][3][head]"  value="'.(isset($_POST['cal'][$k][3]['head'])?Help::Html($_POST['cal'][$k][3]['head']):"").'" /></td><td>Datum: </td><td><input type="text"  name="cal['.$k.'][3][datum]" value="'.(isset($_POST['cal'][$k][3]['datum'])?Help::Html($_POST['cal'][$k][3]['datum']):"").'" class="mandatory sinput3"  /><a href="javascript:ok1 = window.open(\'calendar.php?cas=cal['.$k.'][3][datum]\',\'\',\'width=140,height=247\');void(0);"><img src="'.HOST.'_clip/calendar.gif" alt="Týmy" class="img" /></a></td></tr>';
	  $this->vrat .= '<tr><td valign="top">Text: </td><td colspan="3"><textarea name="cal['.$k.'][3][text]" cols="35" rows="6">'.(isset($_POST['cal'][$k][3]['text'])?Help::Html($_POST['cal'][$k][3]['text']):"").'</textarea></td></td></tr>';
	  $this->vrat .= '<tr><td valign="top">Obrázek: </td><td colspan="3"><input type="file" style="width:500px"  name="cal'.$k.'3file" /></td></tr>';
	  $this->vrat .= '<tr><th colspan="2" align="left">Vyberte událost: </th><th colspan="2" align="left">'.$this->RetUdalost($k,3).'</th></tr>';
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
      
      $this->vrat .= '<tr><th colspan="4" align="left"><hr /></th></tr>';
        
      $this->vrat .= '<tr><td colspan="2"><em>Pondělí</em></td><th>Datum</th><th><input type="text" name="cal['.$k.'][4][datum]" value="'.(isset($_POST['cal'][$k][4]['datum'])?Help::Html($_POST['cal'][$k][4]['datum']):"").'" /></th></tr>';
      
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,4,1).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][4][od][1]" value="'.(isset($_POST['cal'][$k][4]['od'][1])?Help::Html($_POST['cal'][$k][4]['od'][1]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,4,2).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][4][od][2]" value="'.(isset($_POST['cal'][$k][4]['od'][2])?Help::Html($_POST['cal'][$k][4]['od'][2]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,4,3).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][4][od][3]" value="'.(isset($_POST['cal'][$k][4]['od'][3])?Help::Html($_POST['cal'][$k][4]['od'][3]):"").'" /></td></tr>';
       
      $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
	  $this->vrat .= '<tr><td colspan="2"><em>Úterý</em></td><th>Datum</th><th><input type="text" name="cal['.$k.'][5][datum]" value="'.(isset($_POST['cal'][$k][5]['datum'])?Help::Html($_POST['cal'][$k][5]['datum']):"").'" /></th></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,5,1).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][5][od][1]" value="'.(isset($_POST['cal'][$k][5]['od'][1])?Help::Html($_POST['cal'][$k][5]['od'][1]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,5,2).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][5][od][2]" value="'.(isset($_POST['cal'][$k][5]['od'][2])?Help::Html($_POST['cal'][$k][5]['od'][2]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,5,3).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][5][od][3]" value="'.(isset($_POST['cal'][$k][5]['od'][3])?Help::Html($_POST['cal'][$k][5]['od'][3]):"").'" /></td></tr>';
      
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
	  $this->vrat .= '<tr><td colspan="2"><em>Středa</em></td><th>Datum</th><th><input type="text" name="cal['.$k.'][6][datum]" value="'.(isset($_POST['cal'][$k][6]['datum'])?Help::Html($_POST['cal'][$k][6]['datum']):"").'" /></th></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,6,1).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][6][od][1]" value="'.(isset($_POST['cal'][$k][6]['od'][1])?Help::Html($_POST['cal'][$k][6]['od'][1]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,6,2).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][6][od][2]" value="'.(isset($_POST['cal'][$k][6]['od'][2])?Help::Html($_POST['cal'][$k][6]['od'][2]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,6,3).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][6][od][3]" value="'.(isset($_POST['cal'][$k][6]['od'][3])?Help::Html($_POST['cal'][$k][6]['od'][3]):"").'" /></td></tr>';
      
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
	  $this->vrat .= '<tr><td colspan="2"><em>Čtvrtek</em></td><th>Datum</th><th><input type="text" name="cal['.$k.'][7][datum]" value="'.(isset($_POST['cal'][$k][7]['datum'])?Help::Html($_POST['cal'][$k][7]['datum']):"").'" /></th></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,7,1).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][7][od][1]" value="'.(isset($_POST['cal'][$k][7]['od'][1])?Help::Html($_POST['cal'][$k][7]['od'][1]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,7,2).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][7][od][2]" value="'.(isset($_POST['cal'][$k][7]['od'][2])?Help::Html($_POST['cal'][$k][7]['od'][2]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,7,3).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][7][od][3]" value="'.(isset($_POST['cal'][$k][7]['od'][3])?Help::Html($_POST['cal'][$k][7]['od'][3]):"").'" /></td></tr>';
      
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
	  $this->vrat .= '<tr><td colspan="2"><em>Pátek</em></td><th>Datum</th><th><input type="text" name="cal['.$k.'][8][datum]" value="'.(isset($_POST['cal'][$k][8]['datum'])?Help::Html($_POST['cal'][$k][8]['datum']):"").'" /></th></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,8,1).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][8][od][1]" value="'.(isset($_POST['cal'][$k][8]['od'][1])?Help::Html($_POST['cal'][$k][8]['od'][1]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,8,2).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][8][od][2]" value="'.(isset($_POST['cal'][$k][8]['od'][2])?Help::Html($_POST['cal'][$k][8]['od'][2]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,8,3).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][8][od][3]" value="'.(isset($_POST['cal'][$k][8]['od'][3])?Help::Html($_POST['cal'][$k][8]['od'][3]):"").'" /></td></tr>';
      
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
	  $this->vrat .= '<tr><td colspan="2"><em>Sobota</em></td><th>Datum</th><th><input type="text" name="cal['.$k.'][9][datum]" value="'.(isset($_POST['cal'][$k][9]['datum'])?Help::Html($_POST['cal'][$k][9]['datum']):"").'" /></th></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,9,1).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][9][od][1]" value="'.(isset($_POST['cal'][$k][9]['od'][1])?Help::Html($_POST['cal'][$k][9]['od'][1]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,9,2).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][9][od][2]" value="'.(isset($_POST['cal'][$k][9]['od'][2])?Help::Html($_POST['cal'][$k][9]['od'][2]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,9,3).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][9][od][3]" value="'.(isset($_POST['cal'][$k][9]['od'][3])?Help::Html($_POST['cal'][$k][9]['od'][3]):"").'" /></td></tr>';
      
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
	  $this->vrat .= '<tr><td colspan="2"><em>Neděle</em></td><th>Datum</th><th><input type="text" name="cal['.$k.'][10][datum]" value="'.(isset($_POST['cal'][$k][10]['datum'])?Help::Html($_POST['cal'][$k][10]['datum']):"").'" /></th></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,10,1).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][10][od][1]" value="'.(isset($_POST['cal'][$k][10]['od'][1])?Help::Html($_POST['cal'][$k][10]['od'][1]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,10,2).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][10][od][2]" value="'.(isset($_POST['cal'][$k][10]['od'][2])?Help::Html($_POST['cal'][$k][10]['od'][2]):"").'" /></td></tr>';
      $this->vrat .= '<tr><th colspan="1" align="left">Vyberte událost: </th><th colspan="1" align="left">'.$this->RetUdalost($k,10,3).'</th><td>Od - Do: </td><td><input type="text" name="cal['.$k.'][10][od][3]" value="'.(isset($_POST['cal'][$k][10]['od'][3])?Help::Html($_POST['cal'][$k][10]['od'][3]):"").'" /></td></tr>';
      
	  $this->vrat .= '<tr><td colspan="4">&nbsp;</td></tr>';
	  
      $this->vrat .= '</table>';
	  
	  $this->vrat .= '<br /></div>';
	 
	  $x++;
	 
	 }

	  $this->vrat .= '<br /><br /><input type="submit" value="Poslat" name="send_n" />';
     if($news_id != NULL) $this->vrat .= '<input type="hidden" name="nid" value="'.$news_id.'" />';
	 
	 $this->vrat .= '</form>';
	 
	}

  
	/**
 * Vyber udalosti
 * @param int $cislo index
 * @param int $cislo2 index
 * @return sting
 */
  private function RetUdalost($lang_id,$cislo,$cislo2=NULL){
  	 
  	static $check = false;
  	static $sport; 
  	
  	 $vrat = '';
  	 
  	 if($check == false){
  	 	
  	 
  	 $preklad = new Preklady();
  	
  	 $sql = "select s.nazev as snazev,u.nazev as unazev,u.udalost_id,s.sport_id from sport s inner join udalost u on s.sport_id=u.sport_id";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");
	 
     $sport = array();
	 
     while ($row =& $res->fetchRow()){
  	    
     	if(!isset($sport[$row['sport_id']])){

     	  $s = $preklad->FindPreklad($row['snazev'],1);
     	  $sport[$row['sport_id']]['nazev'] = $s[1]; 
     	  
     	}
     	
     	$s = $preklad->FindPreklad($row['unazev'],1);
     	$sport[$row['sport_id']]['udalost'][$row['udalost_id']]['nazev'] = $s[1]; 
     	 
     }
  	
  	 }
  	 
     $vrat .= '<select name="'.($cislo2!=NULL?'udalost':'nudalost').'['.$lang_id.']['.$cislo.']'.($cislo2!=NULL?'['.$cislo2.']':'').'"><option value="0">Vyber událost</option>'; 
  	 
     foreach($sport as $s_id=>$h){
     	 $vrat .= '<optgroup label='.$h['nazev'].'>';
     	 foreach($h['udalost'] as $u_id=>$h2){
     	 	$vrat .= '<option '.(isset($_POST['udalost_'.$lang_id]) && $_POST['udalost_'.$lang_id] == $u_id?'selected="selected"':'').' value="'.$u_id.'">'.Help::Html($h2['nazev']).'</option>';
     	 }
     	
     	 $vrat .= '</optgroup>';
     }
     
     $vrat .= '</select>';
     

    $check = true;
    
  	return $vrat;
  	
  }
  
/**
 * Zobrazeni formulare
 * @return sting
 */
  private function ShowZpravy(){


	if(isset($_REQUEST['act']) && $_REQUEST['act'] == "send")
	  $this->ShowOdeslane();
	else
	  $this->ShowForm();
	  
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
  
 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){
   
    return $this->vrat;
	
  }
    
   
  public function __destruct(){
    
	
	 
	 
  }
  
}

?>
