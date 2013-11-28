<?php
define('TPL_CREATE_NAMESPACE', 'website.createnamespace.html');
define('TPL_NAMESPACE_TABLE', 'website.namespace.table.html');
class Application_Model_Namespaces extends Application_Model_Table {

	private $oSettings;
	private $oAccount;
	private $oAccounts;

	public function __construct($oSettings) {
		parent::__construct();

		$this -> oSettings = $oSettings;
		$this -> setTable('namespaces');
		$this -> setPrimary('idnamespace');
		$this -> oAccounts = new Application_Model_Account($this -> oSettings);
		$this -> oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$this -> oRates = new Application_Model_Rates($this -> oSettings);
	}

	public function lookup($sPreferedNamespace) {
		$sSelect = $this -> select() -> where('name="' . $sPreferedNamespace . '"');
		$iCountMatch = $this -> fetchAll($sSelect) -> count();

		if ($iCountMatch === 0) {
			return array('error' => false, 'message' => 'Namespace is available');
		} else {
			return array('error' => true, 'message' => 'Namespace is already in use');
		}
	}

	public function create($sNamespace = false) {

		if (is_object($this -> oAccounts)) {

			if (!$sNamespace) {

				$oSmarty = Application_Model_Smarty::getInstance();

				return $oSmarty -> fetch(TPL_CREATE_NAMESPACE);
			} else {
				if (!$this -> oAccounts -> setUsage($this -> oAccount -> userdata['UID'], array('maxnamespaces' => 1))) {
					return array('error' => false, 'message' => 'Max namespaces limit reached');
				}
				$aFreeSpace = $this -> lookup($sNamespace);
				if (!$aFreeSpace['error']) {
					$iInsertID = $this -> insert(array('name' => Preprocessor_String::filterBadChars($sNamespace), 'accounts_idaccount' => $this -> oAccount -> userdata['UID']));
					if ((int)$iInsertID > 0) {
						return array('error' => false, 'message' => 'Namespace was created');
					}
				} else {
					return array('error' => true, 'message' => 'Namespace already exists');
				}
			}
		}
		return false;
	}

	public function listSpaces() {
		$oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$oMyNameSpaces = $this -> getMyNameSpaces();
		$aNameSpaces = array();
		foreach ($oMyNameSpaces as $oNameSpace) {
			array_push($aNameSpaces, array('CREATED' => $oNameSpace -> created, 'ID' => $oNameSpace -> idnamespace, 'NAME' => $oNameSpace -> name));
		}
		$oSmarty = Application_Model_Smarty::getInstance();
		$oSmarty -> assign('SM_A_NAMESPACES', $aNameSpaces);
		$oSmarty -> assign('SM_I_LIMITATION', $this -> oSettings -> website -> site -> basehref . 'namespaces/configure');
		$oSmarty -> assign('SM_I_PERPAGE', $oAccount -> userdata['LIMITRESULT']);
		$oSmarty -> assign('SM_I_NAMESPACES', $oAccount -> userdata['NAMESPACES']);
		$oSmarty -> assign('SM_I_INDEX', $oAccount -> userdata['LISTINDEX']);
		return $oSmarty -> fetch(TPL_NAMESPACE_TABLE);
	}

	public function getMyNameSpaces($bGetAll = false) {

		$oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$aLimits = array('5' => 5, '10' => 10, '25' => 25, '50' => 50, '100' => 100);
		if (isset($oAccount -> userdata['UID'])) {
			$sSubExpr = '';

			if (isset($_GET['filter_letter']) && strlen($_GET['filter_letter']) == 1 && $_GET['filter_letter'] != '*') {
				$oAccount -> userdata['FILTERLETTER'] = $_GET['filter_letter'];
			} else {
				if (isset($_GET['filter_letter']) && $_GET['filter_letter'] == '*') {
					$oAccount -> userdata['FILTERLETTER'] = '*';
				}
			}

			if (!isset($_GET['filter_expr'])) {
				if (isset($oAccount -> userdata['FILTERLETTER']) && $oAccount -> userdata['FILTERLETTER'] != '*') {
					$sSubExpr = " AND name LIKE '" . strtolower($oAccount -> userdata['FILTERLETTER']) . "%'";
				}
			} else {

				if (isset($_GET['filter_expr']) && strlen($_GET['filter_expr']) > 1) {
					$oAccount -> userdata['FILTERLETTER'] = $_GET['filter_expr'];
					$sSubExpr = " AND name LIKE '" . strtolower(Preprocessor_String::filterBadChars($_GET['filter_expr'])) . "%'";
				}
			}

			$iAccountId = $oAccount -> userdata['UID'];

			if (!isset($oAccount -> userdata['LIMITRESULT'])) {
				$oAccount -> userdata['LIMITRESULT'] = 5;
			}

			if (isset($_GET['limit']) && isset($aLimits[$_GET['limit']])) {
				$oAccount -> userdata['LIMITRESULT'] = $aLimits[$_GET['limit']];
			}

			if (isset($_GET['list_index'])) {
				$oAccount -> userdata['LISTINDEX'] = (int)$_GET['list_index'];
			} else {
				$oAccount -> userdata['LISTINDEX'] = 0;
			}
			if ($bGetAll) {
				$sSubExpr = '';
			}
			$iSumCount = $this -> fetchAll($this -> select() -> where('accounts_idaccount=' . $oAccount -> userdata['UID'] . $sSubExpr)) -> count();
			$oAccount -> userdata['NAMESPACES'] = $iSumCount;

			if ($bGetAll) {
				$oSelect = $this -> select() -> where('accounts_idaccount=' . $iAccountId) -> order('name');
			} else {
				$oSelect = $this -> select() -> where('accounts_idaccount=' . $iAccountId . $sSubExpr) -> order('name') -> limit($oAccount -> userdata['LIMITRESULT'], $oAccount -> userdata['LISTINDEX']);

			}

			$oResult = $this -> fetchAll($oSelect);
			return $oResult;
		}
		return false;
	}

	public function deleteNamespace($iIdNamespace) {
		$oSpaces = new Application_Model_Table();
		$oGroups = new Application_Model_Groups($this->oSettings);
		$oSpaces->setTable('groupspaces');
		$oSpaces->setPrimary('namespaces_idnamespace');
		
		if ((int)$iIdNamespace > 0 && $this->oAccount -> userdata['UID'] > 0) {
			// Here the full procedure for complete deleting of a namespace starts
			$oGroupSpaces = $oSpaces->fetchAll($oSpaces->select()->where($oSpaces->getPrimary().'='.$iIdNamespace));
			if ($this -> delete($this -> getPrimary() . '=' . $iIdNamespace . ' AND accounts_idaccount=' . $this->oAccount -> userdata['UID'])) {
				foreach($oGroupSpaces as $oGroup){
					Application_Model_Privileges::cleanPrivileges($oGroups -> getGroupMembers($oGroup->groups_idgroup), $this->oAccount->userdata['UID']);
				}	
				return $this -> oAccounts -> setUsage($this->oAccount -> userdata['UID'], array('maxnamespaces' => -1));
			}
		}
		return false;
	}

}
?>