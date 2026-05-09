<?php

class It6_XmlRpc_RequestHelper {

/**
 * @param string $method Method name with namespace (eg. "foo.bar")
 * @returns array first element is Namespace parsed form method full name (eg. "foo" from "foo.bar" or "" from "bar")
 *                second element is method name (eg. "bar" from "foo.bar" or "bar" from "bar")
 */
public static function parseNamespace($method) {
	if (1 == preg_match('/^([a-z0-9_]+)\\.(.*)$/i', $method, $match))
		return array($match[1], $match[2]);
	else
		return array('', $method);
}

/**
 * @param Zend_XmlRpc_Request $request request wrapping XML-RPC call
 * @param string $method Optional output parameter which will receive called method name
 * @returns string Namespace that is required to be known to XML-RPC server (used eg. when calling Zend_XmlRpc_Server::setClass())
 */
public static function getRequiredNamespace(Zend_XmlRpc_Request $request, &$method = null) {
	list($namespace, $method) = static::parseNamespace($request->getMethod());
	if ('system' == $namespace && 'methodSignature' == $method) {
		$params = $request->getParams();
		list($namespace, $method) = static::parseNamespace($params[0]);
	}
	return $namespace;
}

} //class It6_XmlRpc_RequestHelper
