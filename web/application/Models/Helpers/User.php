<?php
/**
 * class Models_Helpers_User
 * User data wrapper
 */

class Models_Helpers_User extends Models_Helpers_RegistryAndSession {

	const COOKIE_USERID = 'userId';
	const DBTABLE = 'uzivatel';

	public $id;
	public $handle;
	public $nick;
	public $name;
	public $surname;
	public $email;

	public $gender;
	public $birthDate;
	public $birthId;
	public $citizenId;
	public $street;
	public $place;
	public $zipCode;
	public $phone;
	public $password;
	public $newsletter;
	public $registrationDateTime;
	public $countryId;
	public $currencyId;
	public $activationDate;
	public $branchId;



	/**
	 * Initializes member variables' descriptors, resets members.
	 */
	public function __construct() {
		parent::__construct();
		// RegistryAndSession data
		$this->membersData = array(
			'id' => array( 'key' => 'user_id' ),
			'handle' => array( 'key' => 'user_handle' ),
			'nick' => array( 'key' => 'nick' ),
			'name' => array( 'key' => 'jmeno' ),
			'surname' => array( 'key' => 'prijmeni' ),
		//'email' => array( 'key' => 'email' ),
		);
		$this->clear();
	}

	/**
	 * @returns true if user can be registered (unique fields are not present in DB)
	 */
	public function canRegister() {
		$res = Zend_Registry::get('db')->select()
			->from( self::DBTABLE, array('c' => 'COUNT(user_id)') )
			->where('nick = ?', $this->nick)->orWhere('email = ?', $this->email)
			->query()->fetchAll();


		return (0 == $res[0]['c']);
	}

	/**
	 * Inserts new user into DB.
	 * @returns true on success
	 */
	public function writeDb() {
		//$db = Zend_Registry::get('db');
		$this->registrationDateTime = strftime('%Y-%m-%d %H:%M:%S');
		//$this->countryId = Ip::getCountryId();
		//if (!$this->countryId)
		//TODO: how handle unknown IP region?
		$this->countryId = 3; // ID for CZ
		//TODO: currency from country?
		$this->currencyId = 8; //CZK

		$this->id = Zend_Registry::get('ws')->User->insert(array(
			'sex'            => $this->gender,
			'firstName'      => $this->name,
			'lastName'       => $this->surname,
			'street'         => $this->street,
			'zip'            => $this->zipCode,
			'town'           => $this->place,
			'birthDate'      => $this->birthDate,
			'email'          => $this->email,
			'phone'          => $this->phone,
			'username'       => $this->nick,
			'password'       => $this->password,
			'sendNewsletter' => $this->newsletter,
			'countryId'      => $this->countryId,
			'currencyId'     => $this->currencyId,
			'citizenId'      => $this->citizenId,
			'branchId'       => $this->branchId,
		));

		/*
		$data = array(
			'pohlavi' => $this->gender,
			'jmeno' => $this->name,
			'prijmeni' => $this->surname,
			'ulice' => $this->street,
			'psc' => $this->zipCode,
			'misto' => $this->place,
			'datum_narozeni' => $this->birthDate,
			'email' => $this->email,
			'telefon' => $this->phone,
			'nick' => $this->nick,
			'heslo' => $this->password,
			'newsletter' => $this->newsletter,
			'datum_registrace' => $this->registrationDateTime,
			'zeme_id' => $this->countryId,
			'mena_id' => $this->currencyId,
			'datum_aktivace' => $this->activationDate,
		);



		$rows = $db->insert(self::DBTABLE, $data);
		if (1 == $rows) {
			$this->id = $db->lastInsertId();
			$userImData = new Models_Helpers_UserImData($this->id);
			//$db->insert('uzivatel_im_data', array('user_id' => $this->id)); //, 'zustatek' => 0.0, 'zetony' => 0.0, 'dluh' => 0.0, 'zustatek_bonus' => 0.0));
			$userImData->writeDb();
			return $this->id;
		}
		else
			return false;
		*/
	}

	/**
	 * Update user in DB (only fields that can be modified in user profile).
	 * @returns true on success
	 */
	public function updateDb() {

		$db = Zend_Registry::get('db');

		$data = array(
			'pohlavi' => $this->gender,
			'telefon' => $this->phone,
			'newsletter' => $this->newsletter
		);
		if ( !empty($this->password) )
			$data['heslo'] = $this->password;

		$rows = $db->update(self::DBTABLE, $data, array('user_id = ?' => $this->id));

		if ($rows !== false)
			return true;
		else
			return false;
	}

	/**
	 * Reads user data from DB.
	 * @param $id user ID
	 * @returns true if user was found and read
	 */
	public function readDb($id) {
		$db = Zend_Registry::get('db');
		$rows = $db->select()
		->from( self::DBTABLE, array( 'user_id', 'handle', 'pohlavi', 'jmeno', 'prijmeni', 'ulice', 'psc', 'misto', 'datum_narozeni', 'email',
				'telefon', 'nick', 'heslo', 'newsletter', 'datum_registrace', 'zeme_id', 'mena_id', 'datum_aktivace', 'citizen_id', 'branch_id') )
		->where('user_id = ?', $id)
		->query()->fetchAll();
		if (empty($rows))
			return false;
		else
			return $this->readArray($rows[0]);
	}

	/**
	 * Reads user data from array.
	 * @param $data associative array, keys are database columns
	 * @returns true if user was found and read
	 */
	public function readArray(array $data) {
		$this->id = $data['user_id'];
		$this->handle = $data['handle'];
		$this->gender = $data['pohlavi'];
		$this->name = $data['jmeno'];
		$this->surname = $data['prijmeni'];
		$this->street = $data['ulice'];
		$this->zipCode = $data['psc'];
		$this->place = $data['misto'];
		$this->birthDate = $data['datum_narozeni'];
		$this->email = $data['email'];
		$this->phone = $data['telefon'];
		$this->nick = $data['nick'];
		$this->password = $data['heslo'];
		$this->newsletter = $data['newsletter'];
		$this->registrationDateTime = $data['datum_registrace'];
		$this->countryId = $data['zeme_id'];
		$this->currencyId = $data['mena_id'];
		$this->activationDate = $data['datum_aktivace'];
		$this->citizenId = $data['citizen_id'];
		$this->branchId = $data['branch_id'];
		return $this;
	}

	public static function setUserIdCookieValue($value, $expires = 0) {
		return setcookie(self::COOKIE_USERID, $value, $expires, '/', WEBHOST, true);
	}

	public function setUserIdCookie() {
		$encId = mcrypt_cbc(MCRYPT_BLOWFISH, 'jo2ieFo6aiN6liu4opaeQuahPee3foam', $this->id, MCRYPT_ENCRYPT, '7gafee1j');
		return self::setUserIdCookieValue(base64_encode($encId), time() + 30 * 24 * 60 * 60);
	}

	public static function getUserIdCookie() {
		if (!empty($_COOKIE[self::COOKIE_USERID])) {
			$encId = trim( base64_decode($_COOKIE[self::COOKIE_USERID]) );
			return mcrypt_cbc(MCRYPT_BLOWFISH, 'jo2ieFo6aiN6liu4opaeQuahPee3foam', $encId, MCRYPT_DECRYPT, '7gafee1j');
		}
		else
			return false;
	}

	public static function deleteUserIdCookie() {
		return self::setUserIdCookieValue('', time() - 24 * 3600);
	}
}
