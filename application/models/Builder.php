<?php
define ( 'TPL_BUILDER_TABLE', 'website.builder.table.html' );
class Application_Model_Builder extends Application_Model_Table {
	
	private $oSettings;
	private $oAccount;
	private $oGroups;
	private $oAccounts;
	
	public function __construct($oSettings) {
		parent::__construct ();
		
		$this->oSettings = $oSettings;
		$this->setTable ( 'namespaces' );
		$this->setPrimary ( 'idnamespace' );
		$this->oAccounts = new Application_Model_Account ( $this->oSettings );
		$this->oAccount = Application_Model_Session::getInstance ( 'ACCOUNT' );
		$this->oRates = new Application_Model_Rates ( $this->oSettings );
		$this->oGroups = new Application_Model_Groups ( $this->oSettings );
	}
	
	public function listMembersSpaces() {
		$oMemberSpaces = $this->getMembersSpaces ();
		$aMemberSpaces = array ();
		$sReturn = '';
		
		foreach ( $oMemberSpaces ['OWNER'] as $iKey => $aGroupconfig ) {
			$oMemberSpaces ['MEMBER'] [$iKey] = $aGroupconfig;
		}
		unset ( $oMemberSpaces ['OWNER'] );
		if (isset ( $oMemberSpaces ['MEMBER'] )) {
			foreach ( $oMemberSpaces ['MEMBER'] as $iGroupMember => $aGroupSettings ) {
				$oGroup = $this->oGroups->fetchRow ( $this->oGroups->select ()->where ( $this->oGroups->getPrimary () . '=' . $iGroupMember ) );
				if (is_object ( $oGroup )) {
					$sRights = '';
					if (isset ( $aGroupSettings ['RIGHTS'] )) {
						foreach ( $aGroupSettings ['RIGHTS'] as $iIdRight => $sRightName ) {
							$sRights .= '<img src="img/rgt_' . $sRightName . '.png"/>';
						}
					}
					if (isset ( $aGroupSettings ['SPACES'] )) {
						foreach ( $aGroupSettings ['SPACES'] as $iIdNameSpace => $sNameSpaceName ) {
							array_push ( $aMemberSpaces, array ('ID' => $iIdNameSpace, 'GROUP' => $oGroup->name, 'NAME' => $sNameSpaceName, 'RIGHTS' => $sRights ) );
						}
					}
				}
			}
			
			$oSmarty = Application_Model_Smarty::getInstance ();
			$oSmarty->assign ( 'SM_A_NAMESPACES', $aMemberSpaces );
			
			$sReturn .= $oSmarty->fetch ( TPL_BUILDER_TABLE );
		}
		
		return $sReturn;
	}
	
	public function getMembersSpaces($bGetAll = false) {
		$oResult = $this->oGroups->accountGroups ();
		return $oResult;
	}

}
?>