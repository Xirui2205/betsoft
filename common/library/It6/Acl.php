<?php

abstract class It6_Acl extends Zend_Acl {

// different user identity components (identity of user with branch given is different then without branch)
const IDNAME_ADMIN = 'admin';
const IDNAME_BRANCH = 'branch';
const IDNAME_HOST = 'host';
const IDNAME_CLIENT = 'client';

abstract public function getIdentity($idName = null);
abstract public function setHttpAuthForWs(Zend_Http_Client $client);

} // class It6_Acl