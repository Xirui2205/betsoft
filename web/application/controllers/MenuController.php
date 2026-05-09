<?php

class MenuController extends Zend_Controller_Action {

	public function indexAction() {

		set_time_limit(0);
		$this->_helper->layout->disableLayout();

		Zend_Registry::get('db')->query('SET NAMES utf8');


		//Zend_Registry::get('db')->delete('controller_preklady');
		$select = Zend_Registry::get('db')
			->select()
			->from( array( 'a' => 'controller_convert' ), array('a.c_id') )
			->where('lang_id=?',1);

		/*   $select = Zend_Registry::get('db')->select()->from(array('a'=>'controller_convert'),array('a.c_id'))
		 ->where('lang_id=?',1)->where('a.c_id in(14,3,13)');
		 $stm  = $select->query();
		 $row = $stm->fetchAll();

		 foreach($row as $h){

		 $select = Zend_Registry::get('db')->select()->from(array('a'=>'preklady'),array('a.preklad_id'))->where('lang_id=?',1)
		 ->where('preklad_id<3432');
		 $stm  = $select->query();
		 $row2 = $stm->fetchAll();

		 foreach($row2 as $h2){

		 $data = array();
		 $data['preklad_id'] = $h2['preklad_id'];
		 $data['c_id'] = $h['c_id'];
		 Zend_Registry::get('db')->delete('controller_preklady','c_id='.$h['c_id'].' and preklad_id='.$h2['preklad_id']);
		 Zend_Registry::get('db')->insert('controller_preklady',$data);

		 }

		 }*/

		$select = Zend_Registry::get('db')
			->select()
			->from(array( 'a' => 'controller_convert'),
				array( "CONCAT_WS('_',a.real_controller, a.real_action, c.lang_id) AS n",
						'c.index_pole','c.text') )
			->join(array( 'd' => 'controller_preklady' ), 'd.c_id=a.c_id' )
			->join(array( 'c' => 'preklady'), 'c.preklad_id=d.preklad_id' );

		$stm  = $select->query();
		/*
		 $row = $stm->fetchAll();


		 $preklady = array();
		 foreach($row as $h){

		 $preklady[$h['real_controller']][$h['real_action']][$h['lang_id']][$h['index_pole']] = $h['text'];

		 }

		 foreach($preklady as $controller=>$h){

		 foreach($h as $action=>$h2){

		 foreach($h2 as $iso=>$h3){

		 $fp = fopen(ROOT."web/application/translate/translate_".$controller."_".$action."_".$iso.".php","w");
		 if (!$fp) die('SHIT');
		 fputs($fp,"<?php \n");
		 fputs($fp,"\$trOb = Array();\n");
		 foreach($h3 as $index=>$h4){
		 //$h4 = mb_convert_encoding($h4, "UTF-8");

		 fputs($fp,"\$trOb['". $index ."'] = \"". Models_Helpers_Help::SLash($h4) ."\";\n");

		 }
		 fputs($fp,"Zend_Registry::set('translate',\$trOb);\n");

		 fclose($fp);
		 }

		 }

		 }
		 */

		$preklady = array();
		while ( $row = $stm->fetch() ) {
			$rn = $row['n'];
			if ( empty( $preklady[$rn] ) ) {
				$preklady[$rn] = array();
			}
			$preklady[$rn][$row['index_pole']] = $row['text'];
		}
		foreach ( $preklady as $key => $dict ) {
			$fp = fopen( ROOT . "web/application/translate/translate_$key.php", "w" );
			fputs( $fp, "<?php \n" );
			fputs( $fp, "\$trOb = Array();\n" );
			foreach ( $dict as $index => $text ) {
				fputs($fp,"\$trOb['$index'] = \"". Models_Helpers_Help::SLash($text) ."\";\n");
			}
			fputs( $fp, "Zend_Registry::set('translate',\$trOb);\n" );
			fclose( $fp );
		}
	}

}
