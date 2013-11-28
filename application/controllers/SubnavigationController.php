<?php
/**
 * Central Metainformation controller
 * This controller provides create, delete, modify of metas and their values
 * on base of the following models
 *
 * 1) Application_Model_Meta
 * 2) Application_Model_Metavalue
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 * @category Metainformation_Controller
 * @version 1.0.0
 *
 * @uses Application_Model_Meta, Application_Model_Metavalue
 */

class SubnavigationController extends Zend_Controller_Action
{

	private	$oSess;
	private $oTable;
	private $sRequestFile;
	private $oActionTraversing;

	public function init(){

		$this->oActionTraversing = $this->getRequest();


		$this->oSess = new Zend_Session_Namespace('USER');
		$this->oTable = new Application_Model_Table();

		$this->oTable->setSchema('tagitall');
		$this->oTable->setTable('rights');
		$this->oTable->setPrimary('id_right');

		$sActionName = $this->oActionTraversing->getActionName();

		$this->sRequestFile = $sActionName;

		if(!method_exists($this, $sActionName.'Action')){
			// Run index action and perform file conectivity and process
			$this->oActionTraversing->setActionName('index');
		}
	}

	/**
	 * Represent all metas and route them to json output
	 * @return none
	 */
	public function indexAction(){
		$sSubnav = '';
		if($this->sRequestFile!='destroy'){
			// Catch Subnavs for this
			$aSubnavigation = $this->oTable->getRow('fk_group<='.((isset($this->oSess->fk_group))?$this->oSess->fk_group:0).
												' AND fornav="'.$this->sRequestFile.'" OR fornav is NULL');

			$sSubnav .= '<ul>';
			foreach ($aSubnavigation as $iKey => $aValues){
				$sSubnav .= '<li><a href="'.$aValues['href'].'" target="'.$aValues['target'].'">'.$aValues['name'].'</a></li>';
			}
			$sSubnav .= '</ul>';
		}
		$this->view->subnavigation = $sSubnav;
	}
}
?>