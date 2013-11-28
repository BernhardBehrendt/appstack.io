<?php
/**
 * Central session model grant acces int Sesson namespaces and their Values
 * This model provides an abstraction of a nested set tree with base tree functionalities
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Session Model
 * @version 0.0.1
 *
 */

class Application_Model_Session {

	static private $oSession = null;

	private function __construct() {

	}

	private function __clone() {

	}

	static function getInstance($sSessionSpace) {
		if (!isset(self::$oSession[$sSessionSpace])) {
			self::$oSession[$sSessionSpace] = new Zend_Session_Namespace($sSessionSpace);
		}

		$oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

		self::$oSession[$sSessionSpace] -> setExpirationSeconds($oSettings -> user -> session -> lifetime);
		self::$oSession[$sSessionSpace] -> expires = (($_SESSION['__ZF'][$sSessionSpace]['ENT'] - time()) * 1000);

		return self::$oSession[$sSessionSpace];
	}

}
?>