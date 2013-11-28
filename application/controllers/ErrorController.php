<?php
/**
 * Errorcontroller provides errorhandling of Servererrors and Application Errors
 * e.g. 404 Errors
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Categories_Controller
 * @version 1.0.0
 *
 */
define('MSG_USER_NOT_FOUND', 'Login error');
define('MSG_NO_RATE', 'User has no rate');
define('MSG_INVALID_RATE', 'User rate is not valid');
define('MSG_ALREADY_LOGEDIN', 'User already loged in');

class ErrorController extends Zend_Controller_Action {

	/**
	 * default erroraction
	 */
	public function errorAction() {
		$oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
		$bShowErrors = false;
		$errors = $this -> _getParam('error_handler');

		if(isset($oSettings -> dev -> debug -> key)) {
			if(stripos($_SERVER['HTTP_USER_AGENT'], $oSettings -> dev -> debug -> key) !== false) {
				$bShowErrors = true;
			}
		}

		if(is_object($errors)) {
			$errorSwitch = $errors -> type;
		} else {
			$errorSwitch = false;
		}
		$aLocParams = $this -> getRequest() -> getParams();
		switch ($errorSwitch) {

			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE :

			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :

			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :

			default :
				$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
				$aNavigations = array();
				$oPage = new Application_Model_Page();
				$oApstack = new Application_Model_Appstackapi;

				$sDefaultMsg = '<h1>The page you requested wasn\'t found</h1>';

				if(isset($_GET['message'])) {
					if($_GET['message'] == MSG_USER_NOT_FOUND) {
						$sDefaultMsg = '<h1>' . strip_tags($_GET['message']) . '</h1>';
					}
					if($_GET['message'] == MSG_NO_RATE) {
						$sDefaultMsg = '<h1>' . strip_tags($_GET['message']) . '</h1>';
					}
					if($_GET['message'] == MSG_INVALID_RATE) {
						$sDefaultMsg = '<h1>' . strip_tags($_GET['message']) . '</h1>';
					}
					if($_GET['message'] == MSG_ALREADY_LOGEDIN) {
						$sDefaultMsg = '<h1>' . strip_tags($_GET['message']) . '</h1>';
					}
				}

				$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
				$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
				$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');

				array_push($aBoxes['LEFT'], $oPage -> getBox('X', 'l1', 'teaser_box'));
				if($bShowErrors) {
					if(isset($_SESSION) && count($_SESSION) > 0) {
						$sSession = nl2br(Preprocessor_String::createTag('pre', false, print_r($_SESSION, true)));
						array_push($aBoxes['CONTENT'], $oPage -> getBox('<h1>SESSION data</h1>' . $sSession, 'debug', 'content_box'));
					}
					if(isset($_POST) && count($_POST) > 0) {
						$sPost = nl2br(Preprocessor_String::createTag('pre', false, print_r($_POST, true)));
						array_push($aBoxes['CONTENT'], $oPage -> getBox('<h1>POST data</h1>' . $sPost, 'debug', 'content_box'));
					}
					if(isset($_GET) && count($_GET) > 0) {
						$sGet = nl2br(Preprocessor_String::createTag('pre', false, print_r($_GET, true)));
						array_push($aBoxes['CONTENT'], $oPage -> getBox('<h1>GET data</h1>' . $sGet, 'debug', 'content_box'));
					}

					$sZFError = nl2br(Preprocessor_String::createTag('pre', false, print_r($aLocParams, true)));
					array_push($aBoxes['CONTENT'], $oPage -> getBox('<h1>ZENDFRAMEWORK data</h1>' . $sZFError, 'debug', 'content_box'));
				}
				array_push($aBoxes['CONTENT'], $oPage -> getBox($sDefaultMsg, 'c1', 'content_box'));

				array_push($aBoxes['RIGHT'], $oPage -> getBox('z', 'r1', 'teaser_box'));

				$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');

				break;
		}
	}

	/**
	 *
	 * never known that this method was needed
	 */
	public function getLog() {
		$bootstrap = $this -> getInvokeArg('bootstrap');
		if(!$bootstrap -> hasPluginResource('Log')) {
			return false;
		}
		$log = $bootstrap -> getResource('Log');
		return $log;
	}

}
?>
