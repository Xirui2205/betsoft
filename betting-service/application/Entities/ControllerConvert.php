<?php

class Entities_ControllerConvert extends Entities_AbstractEntity {

/**
 * Unique ID
 * @var integer
 */
public $controllerId;

/**
 * Language ID to support different request data for each language
 * @var integer
 */
public $langId;

/**
 * Controller part of URI
 * @var string
 */
public $requestController;

/**
 * Action part of URI
 * @var string
 */
public $requestAction;

/**
 * Controller class name (following Zend MVC controller mapping conventions)
 * @var string
 */
public $realController;

/**
 * Action method name (following Zend MVC action mapping conventions)
 * @var string
 */
public $realAction;

/**
 * HTML &lt;title&gt; data for page
 * @var string
 */
public $title;

/**
 * HTML &lt;description&gt; data for page
 * @var string
 */
public $description;

/**
 * TRUE if user can stay on page after login 
 * @var boolean
 */
public $afterLogin;

/**
 * TRUE if page URL has no action and possible action part is in fact first parameter
 * @var boolean
 */
public $actionless;

/**
 * TRUE if URL parameters are not key-value pairs, but value list
 * @var boolean
 */
public $nonassocParams;

} // class
