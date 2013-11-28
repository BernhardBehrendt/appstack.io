<?php
/**
 * Documentation controller
 *
 * This contrioller is just a dummy yet
 * is required for documentation I/O and should handle:
 * documentation show, search, relationmodel
 * handle user comments and a discussion which is a living document eg max 500 comments for one artile
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Documentation
 * @version 0.0.0
 *
 */
class DocumentationController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */

	}

	public function indexAction()
	{
		$this->view->sHeadline = 'Tagitall documentation';
		$this->view->sSubheadline = 'Index';

		$this->view->sIndex = 'We hope the documentation is helpful for you.';
	}


}

?>