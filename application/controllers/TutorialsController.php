<?php
/**
 * Tutorial controller
 *
 * This contrioller is just a dummy yet
 * Controller is required for any I/O of demos, snippets and study cases
 * In future a extended tutorial chapter is possible
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Tutorials
 * @version 0.0.0
 *
 */
class TutorialsController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */

	}

	public function indexAction()
	{
		$this->view->sHeadline = 'Tagitall tutorials and demo scripts';
		$this->view->sSubheadline = 'Index';

		$this->view->sIndex = 'We hope the documentation is helpful for you.';
	}
}

?>