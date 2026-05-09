<?php

class It6_Muzo_Digest {

	protected $localPrivateKey = false;
	protected $remotePublicKey = false;

	public function __construct($remotePublicKeyFile, $localPrivateKeyFile, $localPrivateKeyPassword = null) {
		$this->localPrivateKey = openssl_get_privatekey(file_get_contents($localPrivateKeyFile), $localPrivateKeyPassword);
		$this->remotePublicKey = openssl_get_publickey(file_get_contents($remotePublicKeyFile));
	}

	public function __destruct() {
		if (false !== $this->localPrivateKey)
			openssl_free_key($this->localPrivateKey);
		if (false !== $this->remotePublicKey)
			openssl_free_key($this->remotePublicKey);
	}

	public function sign($message) {
		if (false === $this->localPrivateKey)
			return false;
		if (openssl_sign($message, $signature, $this->localPrivateKey))
			return $signature;
		else
			return false;
	}

	public function verify($message, $signature) {
		if (false === $this->remotePublicKey)
			return false;
		return (1 == openssl_verify($message, $signature, $this->remotePublicKey));
	}

}
