<?php

/**
 * Transaction related static methods.
 * @author Pavel Klinger
 * @see Entities_User
 *
 */
class Webservice_Params extends Webservice_AbstractWebService  {

	public static $TABLE						     = "parametry";
	public static $TABLE_PREFIX					     = "p";
	public static $TABLE_PARAMETRY_DATOVY_TYP	     = "parametry_datovy_typ";
	public static $TABLE_PARAMETRY_OPRAVNENI	     = "parametry_opravneni";
	public static $USER_IM_DATA_TABLE			     = "uzivatel_im_data";
	public static $TABLE_SEARCH				    	 = "parametry";
	public static $TABLE_PARAMETRY_DATOVY_TYP_PREFIX = "dt";
	public static $TABLE_PARAMETRY_OPRAVNENI_PREFIX	 = "po";
	public static $TABLE_USER_BANK_ACC			     = "user_bank_account";
	public static $IDENTITY						     = "id";
	public static $ADMIN_DB						     = "vic_admin";
	public static $MAIN_DB						     = "vic_main"; //vic_main


	protected static $CONV = array(
		'id'			=> 'id',
		'id_opravneni'	=> 'id_opravneni',
		'id_datovy_typ' => 'id_datovy_typ',
		'mandatory'		=> 'mandatory',
		"nasobnost"		=> 'nasobnost',
		'p.nazev'			=> 'nazev',
		'dt.nazev'		=> 'dt_nazev'
	);

	const WATCHED_NOT = 0;
	const WATCHED_ALL = 1;

	protected static function defaultJoins($query) {

		$query = parent::defaultJoins($query);
		$query
			->joinLeft(
				array(static::$TABLE_PARAMETRY_OPRAVNENI_PREFIX => static::$TABLE_PARAMETRY_OPRAVNENI),
				static::$TABLE_PARAMETRY_OPRAVNENI_PREFIX.'.id = '.self::$TABLE_PREFIX.'.id_opravneni',null)
			->joinLeft(
				array(static::$TABLE_PARAMETRY_DATOVY_TYP_PREFIX => static::$TABLE_PARAMETRY_DATOVY_TYP),
				static::$TABLE_PARAMETRY_DATOVY_TYP_PREFIX.'.id = '.self::$TABLE_PREFIX.'.id_datovy_typ',null);
		return $query;
	}

	protected static function getDb() {
		return static::getAdminDb();
	}

	
	/**
	 * Returns all users in the system.
	 * @return struct users structure
	 * @see Entities_User
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find param by given identifier.
	 * @param integer $paramId identifier of the param
	 * @return struct param structure
	 * @see Entities_Param
	 */
	public static function getById($paramId, $extensions = null) {
		return parent::getById($paramId, $extensions);
	}

	/**
	 * Insert new param. Value of the param identifier is ignored and new
	 * is generated.
	 * @param struct $param structure of the param
	 * @return integer|bool param identifier of the created param or false on error
	 * @see Entities_Param
	 */
	public static function insert($param) {
		if ( empty($param['nazev']) )
			throw new It6_XmlRpc_Exception("nazev je prazdny");

		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$param = new It6_ArrayWrapper($param);

			$data = static::fromEntity($param);
			$now = It6_Date::dbNow();

			$db->insert(self::$TABLE, $data);
			$pid = $db->lastInsertId();

			It6_Log::info(
				'New param created.',
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'paramId' => $pid,
				)
			);

			It6_DbTransaction::commit($db);

			return $pid;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not insert parameter.", 0, $e);
		}

	}

	/**
	 * Update params.
	 * @param struct $params structure of the parametry
	 * @return true on success
	 * @see Entities_Params
	 */
	public static function update($params) {
		$db = static::getDb();
		try {
			It6_DbTransaction::begin($db);

			parent::update($params);

			It6_DbTransaction::commit($db);
			return true;
		}

		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update params data.", 0, $e);
			return false;
		}
	}

	/**
	 * Update params.
	 * @param struct $params structure of the parametry
	 * @return true on success
	 * @see Entities_Params
	 */
	public static function getAllRole($paramID = null) {
		$where = '';
		if (isset($paramID)) {
			$where = " AND p.id =".$paramID;
		}
		$dbAdmin = Zend_Registry::get('zdb_admin');
		$prava = $dbAdmin->select()
		     ->from(array('role' => 'acl_role'))
		     ->joinLeft(array('po' => 'parametry_opravneni'),
		            'po.acl_role_id = role.acl_role_id',
		            array('po.h', 'po.r', 'po.rw'))
		     ->joinLeft(array('p' => 'parametry'),
		            'p.id = po.id_parametr',
		            array('p.id', 'p.nazev'))
		     ->where('role.assignable = 1'. $where)
		     ->group("role.acl_role_id")
			 ->query()
			 ->fetchAll();

        // pokud nenajde zadne role tak vybereme vsechno pro vyzobrazeni.
        if (count($prava) == 0 || !isset($paramID)) {
			$prava = $dbAdmin->select()
			     ->from(array('role' => 'acl_role'))
			     ->joinLeft(array('po' => 'parametry_opravneni'),
			            'po.acl_role_id = role.acl_role_id',
			            array('po.h', 'po.r', 'po.rw'))
			     ->joinLeft(array('p' => 'parametry'),
			            'p.id = po.id_parametr',
			            array('p.id', 'p.nazev'))
			     ->where('role.assignable = 1')
     		     ->group("role.acl_role_id")
				 ->query()
				 ->fetchAll();
			foreach ($prava as $key => $value) {
			   $prava[$key]['h'] = $prava[$key]['r'] = $prava[$key]['rw'] = 0;
			}
        } 
		return $prava;
	}


	/**
	 * Update parameter privileges.
	 * @param integer $paramId idenetifier of the parameter.
	 * @param array $data data.
	 * @return true on success
	 * @see Entities_Param
	 */
	public static function updateParameterPrivileges($paramId, $data = null) {
	 	// ********************
	 	// smazani starych prav
	 	// ********************
	 	$sql = 'DELETE FROM parametry_opravneni WHERE id_parametr = ' . $paramId ;
	  	Zend_Registry::get('zdb_admin')->query($sql);
	  	// *****************
		// zapis novych prav
		// *****************
	    foreach ($data as $key=>$value) {
			if (substr($key, 0, 5) == "chkb_") {
	  		    $checkbox = explode("_", $key);
	            $acl_role_id = $checkbox[1];
	            $privilege = $checkbox[2];
		 	      
		 	    $sql = 'SELECT * FROM parametry_opravneni WHERE parametry_opravneni.id_parametr = '.$paramId.' AND parametry_opravneni.acl_role_id = ' . $acl_role_id;
		  	    $checkquery = Zend_Registry::get('zdb_admin')->query($sql);
	            if (count($checkquery->fetchAll()) == 0) {
		          $sql = "INSERT INTO parametry_opravneni (id_parametr, acl_role_id, $privilege) 
		                         VALUES ('$paramId', '$acl_role_id', '$value')";                          	
	            } else {
		          $sql = "UPDATE parametry_opravneni SET $privilege = '$value' WHERE parametry_opravneni.id_parametr = $paramId AND parametry_opravneni.acl_role_id = $acl_role_id";                          	
	            }
		  	    Zend_Registry::get('zdb_admin')->query($sql);
		    }
		}
		return true;
	}

	/**
	 * Insert a new parameter privileges.
	 * @param array $values idenetifier of the parameter.
	 * @param array $paramsForm data.
	 * @return true on success
	 * @see Entities_Param
	 */
	public static function insertParameterPrivileges($values, $paramsForm) {
	    foreach ($values as $key=>$value) {
			if (substr($key, 0, 5) == "chkb_") {
				$checkbox = explode("_", $key);
	            $acl_role_id = $checkbox[1];
	            $privilege = $checkbox[2];

			 	$sql = 'SELECT * FROM parametry_opravneni WHERE parametry_opravneni.id_parametr = '.$paramsForm.' AND parametry_opravneni.acl_role_id = ' . $acl_role_id;
			  	$checkquery = Zend_Registry::get('zdb_admin')->query($sql);
            	if (count($checkquery->fetchAll()) == 0) {
		        	$sql = "INSERT INTO parametry_opravneni (id_parametr, acl_role_id, $privilege) 
		                         VALUES ('$paramsForm', '$acl_role_id', '$value')";                          	
              	} else {
		        	$sql = "UPDATE parametry_opravneni SET $privilege = '$value' WHERE parametry_opravneni.id_parametr = $paramsForm AND parametry_opravneni.acl_role_id = $acl_role_id";                          	
              	}
		  	  	Zend_Registry::get('zdb_admin')->query($sql);
		    }
	    }
		return true;
	}

	/**
	 * Get parameter privileges.
	 * @param int $paramId idenetifier of the parameter.
	 * @return array on success
	 * @see Entities_Param
	 */
	public static function getParameterPrivileges($paramId) {
		// Opravneni
		$parava = array();
		$dbAdmin = Zend_Registry::get('zdb_admin');
		$prava = $dbAdmin->select()
		     ->from(array('role' => 'acl_role'))
		     ->joinLeft(array('po' => 'parametry_opravneni'),
		            'po.acl_role_id = role.acl_role_id',
		            array('po.h', 'po.r', 'po.rw'))
		     ->joinLeft(array('p' => 'parametry'),
		            'p.id = po.id_parametr',
		            array('p.id', 'p.nazev'))
		     ->where('role.assignable = 1')
		     ->where('p.id = '.$paramId)
		     ->group("role.acl_role_id")
			 ->query()
			 ->fetchAll();

		// pokud nenajde zadne role tak si vybereme vsechno pro vyzobrazeni.
		if (count($prava) == 0) {
			$prava = $dbAdmin->select()
			     ->from(array('role' => 'acl_role'))
			     ->joinLeft(array('po' => 'parametry_opravneni'),
			            'po.acl_role_id = role.acl_role_id',
			            array('po.h', 'po.r', 'po.rw'))
			     ->joinLeft(array('p' => 'parametry'),
			            'p.id = po.id_parametr',
			            array('p.id', 'p.nazev'))
			     ->where('role.assignable = 1')
				 ->group("role.acl_role_id")
				 ->query()
				 ->fetchAll();
		    foreach ($prava as $key => $value) {
			   $prava[$key]['h'] = $prava[$key]['r'] = $prava[$key]['rw'] = 0;
			}
		}
		return $prava;
	}
	
	/**
	 * Update params.
	 * @param struct $params structure of the parametry
	 * @return true on success
	 * @see Entities_Params
	 */
	public static function getAllDataType() {
		$dbAdmin = Zend_Registry::get('zdb_admin');
		$dt = $dbAdmin->select()
			 ->from("parametry_datovy_typ")
			 ->query()
			 ->fetchAll();

		return $dt;
	}

	/**
	 * Delete user.
	 * @param integer $userId idenetifier of the user.
	 * @return true on success
	 * @see Entities_User
	 */
	public static function delete($userId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns users according to the fulltext search.
	 * @param mixed $searchTerm term to be searched by fulltext
	 * @return array of the users struct.
	 */
	public static function getAllFulltext($searchTerm) {

		$columns = array();
		foreach(self::$CONV as $dbName => $varName) {
			if(!strpos($dbName, '.'))
				$dbName = self::$TABLE_PREFIX.'.'.$dbName;

			$columns[$varName] = $dbName;
		}


		$users = self::getDb()->select()
			->from(
				array('us' => self::$TABLE_SEARCH),
				$columns)
			->join(
				array(self::$TABLE_PREFIX => self::$TABLE),
				self::$TABLE_PREFIX.'.user_id = us.user_id',
				null)
			->join(
				array(static::$USER_IM_DATA_TABLE_PREFIX => static::$USER_IM_DATA_TABLE),
				static::$USER_IM_DATA_TABLE_PREFIX.'.user_id = '.self::$TABLE_PREFIX.'.user_id',
				null)
			->joinLeft(
				array(Webservice_Currency::$TABLE_PREFIX => Webservice_Currency::$TABLE),
				Webservice_Currency::$TABLE_PREFIX.'.mena_id = '.self::$TABLE_PREFIX.'.mena_id',
				null)
			->joinLeft(
				array(Webservice_Country::$TABLE_PREFIX => Webservice_Country::$TABLE),
				Webservice_Country::$TABLE_PREFIX.'.zeme_id = '.self::$TABLE_PREFIX.'.zeme_id',
				null)
			->joinLeft(
				array(Webservice_Language::$TABLE_PREFIX => Webservice_Language::$TABLE),
				Webservice_Language::$TABLE_PREFIX.'.lang_id = '.self::$TABLE_PREFIX.'.lang_id',
				null)
			->where("MATCH(us.jmeno, us.prijmeni, us.nick, us.email, us.ulice, us.telefon, us.mobil, us.info, us.misto) AGAINST(?)", $searchTerm)
			->query()->fetchAll();

		return $users;
	}

}
