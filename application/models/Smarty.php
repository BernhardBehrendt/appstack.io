<?php
/**
 * Application_Model_Cache controlls files containing php generated data for store data
 * in a file for time there is no change in
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 * @category Cache Model
 * @version 1.0.0
 *
 */

class Application_Model_Smarty {

	static private $oSmarty = null;

	private function __construct() {

	}

	private function __clone() {

	}

	static function getInstance($bNew=false) {
		if(null === self::$oSmarty || $bNew) {
			if(!defined('SMARTY_PATH')) {
			} else {
				if(!file_exists(SMARTY_PATH . '/Smarty.class.php')) {
					'Error Load Smarty';
					exit ;
				}

				require_once (SMARTY_PATH . '/Smarty.class.php');
				if(!is_dir(realpath(APPLICATION_PATH . '/../public/cache/') . '/templates_c')) {

					mkdir(realpath(APPLICATION_PATH . '/../public/cache/') . '/templates_c');
				}
				if(!is_dir(realpath(APPLICATION_PATH . '/configs/') . '/smarty/')) {
					mkdir(realpath(APPLICATION_PATH . '/configs/') . '/smarty/');
				}

				$oSmarty = new Smarty();
				$oSmarty -> setTemplateDir(realpath(APPLICATION_PATH . '/views/smarty'));
				$oSmarty -> setCompileDir(realpath(APPLICATION_PATH . '/../public/cache/templates_c'));
				$oSmarty -> setCacheDir(realpath(APPLICATION_PATH . '/../public/cache/'));
				$oSmarty -> setConfigDir(realpath(APPLICATION_PATH . '/configs/smarty/'));

				if($bNew) {
					return $oSmarty;
				}

				self::$oSmarty = &$oSmarty;
			}
		}
		return self::$oSmarty;
	}

}
?>