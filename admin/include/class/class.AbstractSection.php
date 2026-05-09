<?php

class AbstractSection {

/**
 * Cislo sekce
 * @var int
 */
protected $section;

/**
 * vystupni XML response
 * @access private
 * @var int
 */
protected $vrat;

/**
 * Prava pro editaci v sekci
 * @var boolean
 */
protected $update;

/**
 * Prava pro mazani v sekce
 * @var boolean
 */
protected $delete;

/**
 * wrapper pro URL teto sekce
 * @var Url
 */
protected $url;




public function __construct($section=0) {
	$this->section 	= $section;
	$this->vrat 		= '';
	$this->update 	= false;
	$this->delete 	= false;
	$this->url 			= new Url(null, array('section' => $this->section));
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
public function getContent($templatePath=null, $params=array()){
	if($templatePath != null){
		extract($this->templateVars);
		die(var_dump($images));
		ob_start();
			require 'Template/'.$templatePath;
			$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	else
		return $this->vrat;
}

/**
 * Returns a name of layout top be used or null for using default.
 * @return null | string 
 */
public function getLayout() {
	return $this->layout;
}

} // class AbstractSection
