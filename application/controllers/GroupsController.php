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
define ( 'TPL_GROUPSPACES', 'website.groupspaces.html' );
define ( 'TPL_GROUPMEMBERS', 'website.groupmembers.html' );
class groupsController extends Zend_Controller_Action {
	private $oCache;
	private $oSettings;
	private $oAccounts;
	private $oRights;
	private $oAccount;
	
	public function init() {
		
		/*
		 * Initialize action controller here
		 */
		$this->oSettings = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', 'production' );
		$this->oAccounts = new Application_Model_Account ( $this->oSettings );
		$this->oGroups = new Application_Model_Groups ( $this->oSettings );
		$this->oRights = new Application_Model_Rights ();
		$this->oAccount = Application_Model_Session::getInstance ( 'ACCOUNT' );
	}
	
	public function indexAction() {
		
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		
		$oPage = new Application_Model_Page ();
		$oApstack = new Application_Model_Appstackapi ();
		$aBoxes = array ('LEFT' => array (), 'CONTENT' => array (), 'RIGHT' => array () );
		$oCache = new Application_Model_Cache ( 'groups_navigations' );
		
		if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
			
			$aNavigations ['HEADNAV'] = $oPage->getNavi ( 'navi_top', 'top_nav', 'apst_small' );
			$aNavigations ['MAINNAV'] = $oPage->getNavi ( 'navi_main', 'main_nav', '' );
			$aNavigations ['FOOTERNAV'] = $oPage->getNavi ( 'navi_bottom', 'footer', 'clearfix apst_small', ' | ' );
			$oCache->writeCache ( serialize ( $aNavigations ) );
		}
		
		$aNavigations = unserialize ( $oCache->readCache () );
		
		$oCache->setCache ( 'groups_accountadmin' );
		
		if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
			$sAccountAdmin = $oPage->getNavi ( 'groups', 'accountadmin', 'boxnavi', false, false, array ('overview' => 'groups/index', 'configure' => 'groups/configure', 'edit' => 'groups/edit', 'transfer' => 'groups/transfer' ) );
			$oCache->writeCache ( $sAccountAdmin );
		}
		
		$sAccountAdmin = $oCache->readCache ();
		
		array_push ( $aBoxes ['LEFT'], $oPage->getBox ( $sAccountAdmin, 'l1', 'teaser_box' ) );
		
		$aUsage = $this->oAccounts->getUsage ( $this->oAccount->userdata ['UID'] );
		$sContent = '';
		$aRate = $this->oAccounts->getRate ( $this->oAccount->userdata ['UID'] );
		
		foreach ( $aUsage as $sColumnName => $iValue ) {
			
			$iSum = $aRate [$sColumnName];
			
			if ($iSum != - 1 && $iSum != 0 && $sColumnName == 'maxgroups') {
				
				$iUsage = floor ( ($iValue * 100) / $iSum );
				$iAvail = 100 - $iUsage;
				$sLabel = ucfirst ( str_replace ( 'max', '', $sColumnName ) );
				$sHtmlStat = '<span class="apst_pink apst_mid">' . $iValue . ' of ' . $iSum . '</span>' . '<br/>' . $sLabel;
				
				$sContent .= $oPage->pieChart ( $sHtmlStat, array ('Available' => $iAvail, 'Created' => $iUsage ), 300, 0, array ('0e8cf6', '1742cc', 'de057e' ), '282828' );
			}
		
		}
		
		array_push ( $aBoxes ['CONTENT'], $oPage->getBox ( $sContent, 'c1', 'content_box' ) );
		array_push ( $aBoxes ['RIGHT'], $oPage->getBox ( 'z', 'r1', 'teaser_box' ) );
		
		$this->view->sPage = $oPage->getPage ( 'content', $aNavigations, $aBoxes, 'landing.js' );
	}
	
	public function configureAction() {
		
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		if (! isset ( $_GET ['group'] )) {
			$oPage = new Application_Model_Page ();
			$oApstack = new Application_Model_Appstackapi ();
			$aBoxes = array ('LEFT' => array (), 'CONTENT' => array (), 'RIGHT' => array () );
			$oCache = new Application_Model_Cache ( 'groups_navigations' );
			
			if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
				
				$aNavigations ['HEADNAV'] = $oPage->getNavi ( 'navi_top', 'top_nav', 'apst_small' );
				$aNavigations ['MAINNAV'] = $oPage->getNavi ( 'navi_main', 'main_nav', '' );
				$aNavigations ['FOOTERNAV'] = $oPage->getNavi ( 'navi_bottom', 'footer', 'clearfix apst_small', ' | ' );
				$oCache->writeCache ( serialize ( $aNavigations ) );
			}
			
			$aNavigations = unserialize ( $oCache->readCache () );
			
			$oCache->setCache ( 'groups_accountadmin' );
			
			if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
				$sAccountAdmin = $oPage->getNavi ( 'groups', 'accountadmin', 'boxnavi', false, false, array ('overview' => 'groups/index', 'configure' => 'groups/configure', 'edit' => 'groups/edit', 'transfer' => 'groups/transfer' ) );
				$oCache->writeCache ( $sAccountAdmin );
			}
			
			$sAccountAdmin = $oCache->readCache ();
			$sgroupList = $this->oGroups->listGroups ();
			array_push ( $aBoxes ['LEFT'], $oPage->getBox ( $sAccountAdmin, 'l1', 'teaser_box' ) );
			array_push ( $aBoxes ['CONTENT'], $oPage->getBox ( $this->oGroups->create () . $sgroupList, 'c1', 'content_box' ) );
			array_push ( $aBoxes ['RIGHT'], $oPage->getBox ( $oPage->getFiltes ( 'groups/configure/', $this->oAccount->userdata ['LIMITRESULT'], ((isset ( $this->oAccount->userdata ['FILTERLETTER'] ) ? $this->oAccount->userdata ['FILTERLETTER'] : '*')) ), 'r1', 'teaser_box' ) );
			$this->view->sPage = $oPage->getPage ( 'content', $aNavigations, $aBoxes, 'groups.js' );
		} else {
			echo json_encode ( $this->oGroups->create ( $_GET ['group'] ) );
		}
	}
	
	public function lookupAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		
		Preprocessor_Header::setContentType ( 'json' );
		
		if (isset ( $this->oAccount->userdata ['UID'] )) {
			$_GET ['group'] = Preprocessor_String::filterBadChars ( ltrim ( trim ( $_GET ['group'] ) ) );
			
			if (isset ( $_GET ['group'] ) && strlen ( $_GET ['group'] ) > 3) {
				$this->view->bUserExists = json_encode ( $this->oGroups->lookup ( $_GET ['group'] ) );
			}
		}
	}
	
	public function listAction() {
		
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		
		if (isset ( $this->oAccount->userdata ['UID'] )) {
			$this->view->sPage = $this->oGroups->listGroups ();
		}
	}
	
	public function deleteAction() {
		if (isset ( $_GET ['idg'] ) && ( int ) $_GET ['idg'] > 0) {
			$aReturn = array ('error' => true, 'message' => 'unknown error' );
			if (isset ( $this->oAccount->userdata ['UID'] )) {
				// removed... $this -> view -> sPage =
				if ($this->oGroups->deletegroup ( $_GET ['idg'] )) {
					$aReturn = array ('error' => false, 'message' => 'group was deleted' );
				} else {
					$aReturn = array ('error' => true, 'message' => 'group wasn\'t deleted' );
				}
			} else {
				$aReturn = array ('error' => true, 'message' => 'Session expired' );
			}
		} else {
			$aReturn = array ('error' => true, 'message' => 'Invalid group' );
		}
		$this->view->sPage = json_encode ( $aReturn );
	}
	
	public function addspacesAction() {
		
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		
		$oNameSpaces = new Application_Model_Namespaces ( $this->oSettings );
		$aNameSpaceList = array ();
		$oGroupSpaces = $this->oGroups->getGroupSpaces ( $_REQUEST ['groupid'] );
		$aSpaceIds = array ();
		if (is_object ( $oGroupSpaces )) {
			foreach ( $oGroupSpaces as $oGroupSpace ) {
				array_push ( $aSpaceIds, $oGroupSpace->namespaces_idnamespace );
			}
		}
		$oNameSpacesData = $oNameSpaces->getMyNameSpaces ( true );
		if (is_object ( $oNameSpacesData )) {
			foreach ( $oNameSpacesData as $oNameSpace ) {
				if (! isset ( $aNameSpaceList [$oNameSpace->idnamespace] )) {
					$aNameSpaceList [$oNameSpace->idnamespace] = array ();
					$aNameSpaceList [$oNameSpace->idnamespace] ['name'] = $oNameSpace->name;
					
					if (is_object ( $oGroupSpaces )) {
						
						if (in_array ( $oNameSpace->idnamespace, $aSpaceIds )) {
							$aNameSpaceList [$oNameSpace->idnamespace] ['bind'] = true;
						}
					
					}
				}
			}
			$oSmarty = Application_Model_Smarty::getInstance ();
			
			$oSmarty->assign ( 'SM_A_NAMESPACES', $aNameSpaceList );
			$this->view->namespaces = $oSmarty->fetch ( TPL_GROUPSPACES );
			
			$oGroupRights = $this->oRights->getGroupRights ( $_REQUEST ['groupid'] );
			
			$aGroupRights = array ();
			if (is_object ( $oGroupRights )) {
				foreach ( $oGroupRights as $oRow ) {
					$aGroupRights [$oRow->rights_idright] = true;
				}
			}
			
			$this->view->aGroupRights = $aGroupRights;
			$this->views->namespaces = $this->view->rights = $this->oRights->getRights ();
		}
	}
	
	public function bindAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		Preprocessor_Header::setContentType ( 'text/json' );
		$oNameSpaces = new Application_Model_Namespaces ( $this->oSettings );
		$this->view->sPage = json_encode ( $this->oGroups->bindGroupSpace ( $oNameSpaces, $_REQUEST ['idgroup'], $_REQUEST ['idns'] ) );
	}
	
	public function unbindAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		Preprocessor_Header::setContentType ( 'text/json' );
		$oNameSpaces = new Application_Model_Namespaces ( $this->oSettings );
		$this->view->sPage = json_encode ( $this->oGroups->unbindGroupSpace ( $oNameSpaces, $_REQUEST ['idgroup'], $_REQUEST ['idns'] ) );
	
	}
	
	public function memberAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		Preprocessor_Header::setContentType ( 'text/json' );
		$this->view->sPage = json_encode ( $this->oGroups->memberGroupUser ( $_REQUEST ['idgroup'], $_REQUEST ['idusr'] ) );
	}
	
	public function nomemberAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		Preprocessor_Header::setContentType ( 'text/json' );
		$this->view->sPage = json_encode ( $this->oGroups->nomemberGroupUser ( $_REQUEST ['idgroup'], $_REQUEST ['idusr'] ) );
	}
	
	public function nscatsAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
	}
	
	public function addusersAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		$aUsersList = array ();
		$oGroupMembers = $this->oGroups->getGroupMembers ( $_REQUEST ['groupid'] );
		$aMemberIds = array ();
		if (is_object ( $oGroupMembers )) {
			foreach ( $oGroupMembers as $oGroupMember ) {
				array_push ( $aMemberIds, $oGroupMember->accounts_idaccount );
			}
		}
		$oUsersData = $this->oAccounts->getMySubAccounts ( true );
		if (is_object ( $oUsersData )) {
			foreach ( $oUsersData as $oUser ) {
				if (! isset ( $aUsersList [$oUser->idaccount] )) {
					$aUsersList [$oUser->idaccount] = array ();
					$aUsersList [$oUser->idaccount] ['name'] = $oUser->username;
					
					if (is_object ( $oGroupMembers )) {
						
						if (in_array ( $oUser->idaccount, $aMemberIds )) {
							$aUsersList [$oUser->idaccount] ['member'] = true;
						}
					
					}
				}
			}
			$oSmarty = Application_Model_Smarty::getInstance ();
			
			$oSmarty->assign ( 'SM_A_USERS', $aUsersList );
			$this->view->users = $oSmarty->fetch ( TPL_GROUPMEMBERS );
		
		}
	}
	
	public function setrightAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		Preprocessor_Header::setContentType ( 'text/json' );
		if (isset ( $_REQUEST ['groupid'] ) && isset ( $_REQUEST ['setright'] )) {
			$this->view->sPage = json_encode ( $this->oGroups->setGroupright ( $_REQUEST ['groupid'], $_REQUEST ['setright'] ) );
		} else {
			$this->view->sPage = json_encode ( array ('error' => true, 'message' => 'Unknown call' ) );
		}
	}

}
?>