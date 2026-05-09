<?php
/**
 * Models_Helpers_RegistryAndSession
 * Handles reading and writing of class data to session and Zend_Registry.
 */

class Models_Helpers_RegistryAndSession {

	/**
	 * array ( _member_name_ => array(
	 *		'key' => key in registry and session
	 *	),
	 ...
	 * )
	 */
	protected $membersData;

	public function __construct() {
	}

	/**
	 * Vyresetuje data tridy.
	 */
	public function clear() {
		if (!empty($this->membersData)) {
			foreach ($this->membersData as $name => $data) {
				$this->$name = null;
			}
		}
	}

	/**
	 * Zapise data tridy do Zend_Registry.
	 */
	public function writeRegistry() {
		if (!empty($this->membersData)) {
			foreach ($this->membersData as $name => $data) {
				Zend_Registry::set($data['key'], $this->$name);
			}
		}
	}

	/**
	 * Nacte data tridy z Zend_Registry.
	 */
	public function readRegistry() {
		$this->clear();
		if (!empty($this->membersData)) {
			foreach ($this->membersData as $name => $data) {
				$key = $data['key'];
				if (Zend_Registry::isRegistered($key))
				$this->$name = Zend_Registry::get($key);
			}
		}
	}

	/**
	 * Zapise data tridy do session.
	 */
	public function writeSession() {
		if (!empty($this->membersData)) {
			foreach ($this->membersData as $name => $data) {
				$_SESSION[$data['key']] = $this->$name;
			}
		}
	}

	/**
	 * Nacte data tridy ze session.
	 */
	public function readSession() {
		$this->clear();
		if (!empty($this->membersData)) {
			foreach ($this->membersData as $name => $data) {
				$key = $data['key'];
				if (isset($_SESSION[$key]))
				$this->$name = $_SESSION[$key];
			}
		}
	}
}
