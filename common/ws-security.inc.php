<?php

// WS_SSL_TRUSTED_CA complete DNs for approved CA certificates (use \0 as separator)
def('WS_SSL_TRUSTED_CA_DN', array(
	'UNIT_TESTING' => '/C=CZ/ST=Czech Republic/L=Prague/O=IT6/OU=IT/CN=IT6 CA/emailAddress=info@compbet.com',
	'DEVEL_LOCAL' => '/C=CZ/ST=Czech Republic/L=Prague/O=IT6/OU=IT/CN=IT6 CA/emailAddress=info@compbet.com',
	'DEVEL' => false, //TODO:
	'BBAS_TESTING' => '/C=CZ/ST=Czech Republic/L=Prague/O=Smartbetting a.s./OU=IT/CN=Smartbetting a.s. CA/emailAddress=info@compbet.com',
	'BBAS_PRODUCTION' => '/C=CZ/ST=Czech Republic/L=Prague/O=Smartbetting a.s./OU=IT/CN=Smartbetting a.s. CA/emailAddress=info@compbet.com',
));

// define following constant (non-empty) to allow reading SSL data from headers HTTP_CLIENT_VERIFY, HTTP_CLIENT_ISSUER, HTTP_CLIENT_SUBJECT
// instead of appropriate _SERVER variables
// if allowed it must be assured by proxy/server configuration that the header HTTP_CLIENT_VERIFY cannot be forged
//def('ACL_ALLOW_SSL_DATA_FROM_HTTP_HEADERS', true);
