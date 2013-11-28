<?php
/**
 * Indexcontroller delivers base application frame
 * This is an html file which includes Javascripts and contain base structure
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
class BlogController extends Zend_Controller_Action {
	private $oCache;
	private $oSettings;
	private $oAccount;
	public function init() {
		header ( 'Location: http://blog.appstack.io' );
	}
}
?>