<?php
define('TPL_CREATE_GROUP', 'website.creategroup.html');
define('TPL_GROUP_TABLE', 'website.group.table.html');
class Application_Model_Groups extends Application_Model_Table {

	private $oSettings;
	private $oAccount;
	private $oAccounts;
	private $oMembers;
	private $oSpaces;
	private $oRights;

	public function __construct($oSettings) {
		parent::__construct();

		$this -> oSettings = $oSettings;
		$this -> setTable('groups');
		$this -> setPrimary('idgroup');
		$this -> oAccounts = new Application_Model_Account($this -> oSettings);
		$this -> oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$this -> oRates = new Application_Model_Rates($this -> oSettings);

		// Setup Members Table
		$this -> oMembers = new Application_Model_Table();
		$this -> oMembers -> setTable('accountgroups');
		$this -> oMembers -> setPrimary($this -> getTable() . '_' . $this -> getPrimary());

		// Setup Spaces Table
		$this -> oSpaces = new Application_Model_Table();
		$this -> oSpaces -> setTable('groupspaces');
		$this -> oSpaces -> setPrimary($this -> oMembers -> getPrimary());
		// Setup Rights Table
		$this -> oRights = new Application_Model_Rights();

	}

	public function lookup($sPreferedGroup) {
		$sSelect = $this -> select() -> where('name="' . $sPreferedGroup . '" AND accounts_idaccount=' . $this -> oAccount -> userdata['UID']);
		$iCountMatch = $this -> fetchAll($sSelect) -> count();

		if ($iCountMatch === 0) {
			return array('error' => false, 'message' => 'Group is available');
		} else {
			return array('error' => true, 'message' => 'Group is already in use');
		}
	}

	public function create($sGroup = false) {

		if (is_object($this -> oAccounts)) {

			if (!$sGroup) {

				$oSmarty = Application_Model_Smarty::getInstance();

				return $oSmarty -> fetch(TPL_CREATE_GROUP);
			} else {
				if (!$this -> oAccounts -> setUsage($this -> oAccount -> userdata['UID'], array('maxgroups' => 1))) {
					return array('error' => false, 'message' => 'Max groups limit reached');
				}
				$aFreeSpace = $this -> lookup($sGroup);
				if (!$aFreeSpace['error']) {
					$iInsertID = $this -> insert(array('name' => Preprocessor_String::filterBadChars($sGroup), 'accounts_idaccount' => $this -> oAccount -> userdata['UID']));
					if ((int)$iInsertID > 0) {
						return array('error' => false, 'message' => 'Group was created');
					}
				} else {
					return array('error' => true, 'message' => 'Group already exists');
				}
			}
		}
		return false;
	}

	public function listGroups() {
		$oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$oMyGroups = $this -> getMyGroups();
		$aGroups = array();
		foreach ($oMyGroups as $oNameSpace) {
			array_push($aGroups, array('CREATED' => $oNameSpace -> created, 'ID' => $oNameSpace -> idgroup, 'NAME' => $oNameSpace -> name));
		}
		$oSmarty = Application_Model_Smarty::getInstance();
		$oSmarty -> assign('SM_A_GROUPS', $aGroups);
		$oSmarty -> assign('SM_I_LIMITATION', $this -> oSettings -> website -> site -> basehref . 'groups/configure');
		$oSmarty -> assign('SM_I_PERPAGE', $oAccount -> userdata['LIMITRESULT']);
		$oSmarty -> assign('SM_I_GROUPS', $oAccount -> userdata['GROUPS']);
		$oSmarty -> assign('SM_I_INDEX', $oAccount -> userdata['LISTINDEX']);
		return $oSmarty -> fetch(TPL_GROUP_TABLE);
	}

	public function getMyGroups($bGetAll = false) {

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
			$oAccount -> userdata['GROUPS'] = $iSumCount;
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

	public function deleteGroup($iIdGroup) {
		if ((int)$iIdGroup > 0 && $this -> oAccount -> userdata['UID'] > 0) {
			// Here the full procedure for complete deleting of a namespace starts
			$oMembers = $this -> getGroupMembers($iIdGroup);
			if ($this -> delete($this -> getPrimary() . '=' . $iIdGroup . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID'])) {
				Application_Model_Privileges::cleanPrivileges($oMembers, $this -> oAccount -> userdata['UID']);
				return $this -> oAccounts -> setUsage($this -> oAccount -> userdata['UID'], array('maxgroups' => -1));
			}
		}
		return false;
	}

	public function getGroupSpaces($iIdGroup) {

		$oGroupSpaces = $this -> oSpaces -> fetchAll($this -> oSpaces -> select() -> where($this -> oSpaces -> getPrimary() . '=' . (int)$iIdGroup));

		if ($oGroupSpaces -> count() > 0) {
			return $oGroupSpaces;
		}
		return false;
	}

	public function getGroupMembers($iIdGroup) {

		$oGroupMembers = $this -> oMembers -> fetchAll($this -> oMembers -> select() -> where($this -> oMembers -> getPrimary() . '=' . (int)$iIdGroup));

		if ($oGroupMembers -> count() > 0) {
			return $oGroupMembers;
		}
		return false;
	}

	public function bindGroupSpace(Application_Model_Namespaces $oNameSpaces, $iIdGroup, $iIdNamespace) {

		$iEntrys = $this -> oSpaces -> fetchAll($this -> oSpaces -> select() -> where($this -> oSpaces -> getPrimary() . '=' . $iIdGroup . ' AND namespaces_idnamespace=' . (int)$iIdNamespace)) -> count();
		$aReturn = array('error' => true);
		if ($iEntrys == 0) {

			$oNameSpace = $oNameSpaces -> fetchRow($oNameSpaces -> select() -> where($oNameSpaces -> getPrimary() . '=' . (int)$iIdNamespace . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID']));

			if (isset($oNameSpace -> idnamespace)) {
				$iIdNamespace = $oNameSpace -> idnamespace;

				$oGroup = $this -> fetchRow($this -> select() -> where($this -> getPrimary() . '=' . (int)$iIdGroup . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID']));

				if (isset($oGroup -> idgroup)) {
					$iIdGroup = $oGroup -> idgroup;

					$iNoPrimary = $this -> oSpaces -> insert(array($this -> oSpaces -> getPrimary() => $iIdGroup, 'namespaces_idnamespace' => $iIdNamespace));

					if ($iNoPrimary == $iIdGroup) {
						$aReturn['error'] = false;
						$aReturn['message'] = 'Namspace bounded';
						Application_Model_Privileges::cleanPrivileges($this -> getGroupMembers($iIdGroup), $this -> oAccount -> userdata['UID']);
					} else {
						$aReturn['message'] = 'Unknown error';
					}
				} else {
					$aReturn['message'] = 'Group not found';
				}
			} else {
				$aReturn['message'] = 'Namespace not found';
			}

		} else {
			$aReturn['message'] = 'Already bound';
		}
		return $aReturn;
	}

	public function unbindGroupSpace(Application_Model_Namespaces $oNameSpaces, $iIdGroup, $iIdNamespace) {

		$iEntrys = $this -> oSpaces -> fetchAll($this -> oSpaces -> select() -> where($this -> oSpaces -> getPrimary() . '=' . (int)$iIdGroup . ' AND namespaces_idnamespace=' . (int)$iIdNamespace)) -> count();
		$aReturn = array('error' => true);
		if ($iEntrys == 1) {

			$oNameSpace = $oNameSpaces -> fetchRow($oNameSpaces -> select() -> where($oNameSpaces -> getPrimary() . '=' . (int)$iIdNamespace . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID']));

			if (isset($oNameSpace -> idnamespace)) {
				$iIdNamespace = $oNameSpace -> idnamespace;

				$oGroup = $this -> fetchRow($this -> select() -> where($this -> getPrimary() . '=' . (int)$iIdGroup . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID']));

				if (isset($oGroup -> idgroup)) {
					$iIdGroup = $oGroup -> idgroup;

					$iNoPrimary = $this -> oSpaces -> delete($this -> oSpaces -> getPrimary() . '=' . ((int)$iIdGroup) . ' AND namespaces_idnamespace=' . ((int)$iIdNamespace));

					if ((int)$iNoPrimary == 1) {
						$aReturn['error'] = false;
						$aReturn['message'] = 'Namspace unbounded';
						Application_Model_Privileges::cleanPrivileges($this -> getGroupMembers($iIdGroup), $this -> oAccount -> userdata['UID']);
					} else {
						$aReturn['message'] = 'Unknown error';
					}
				} else {
					$aReturn['message'] = 'Group not found';
				}
			} else {
				$aReturn['message'] = 'Namespace not found';
			}

		} else {
			$aReturn['message'] = 'Unknown error';
		}
		return $aReturn;

	}

	public function memberGroupUser($iIdGroup, $iIdAccount) {

		$iEntrys = $this -> oMembers -> fetchAll($this -> oMembers -> select() -> where($this -> oMembers -> getPrimary() . '=' . $iIdGroup . ' AND accounts_idaccount=' . (int)$iIdAccount)) -> count();
		$aReturn = array('error' => true);
		if ($iEntrys == 0) {

			$oAccount = $this -> oAccounts -> fetchRow($this -> oAccounts -> select() -> where($this -> oAccounts -> getPrimary() . '=' . (int)$iIdAccount . ' AND fk_account=' . $this -> oAccount -> userdata['UID']));

			if (isset($oAccount -> idaccount)) {
				$iIdAccount = $oAccount -> idaccount;

				$oGroup = $this -> fetchRow($this -> select() -> where($this -> getPrimary() . '=' . (int)$iIdGroup . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID']));

				if (isset($oGroup -> idgroup)) {
					$iIdGroup = $oGroup -> idgroup;

					$iNoPrimary = $this -> oMembers -> insert(array($this -> oMembers -> getPrimary() => $iIdGroup, 'accounts_idaccount' => $iIdAccount));

					if ($iNoPrimary == $iIdGroup) {
						$aReturn['error'] = false;
						$aReturn['message'] = 'started membership';
						Application_Model_Privileges::cleanPrivileges($this -> getGroupMembers($iIdGroup), $this -> oAccount -> userdata['UID']);
					} else {
						$aReturn['message'] = 'Unknown error';
					}
				} else {
					$aReturn['message'] = 'Group not found';
				}
			} else {
				$aReturn['message'] = 'Account not found';
			}

		} else {
			$aReturn['message'] = 'Already member';
		}
		return $aReturn;
	}

	public function nomemberGroupUser($iIdGroup, $iIdAccount) {

		$iEntrys = $this -> oMembers -> fetchAll($this -> oMembers -> select() -> where($this -> oMembers -> getPrimary() . '=' . (int)$iIdGroup . ' AND accounts_idaccount=' . (int)$iIdAccount)) -> count();
		$aReturn = array('error' => true);
		
		if ($iEntrys == 1) {
			
			$oAccount = $this -> oAccounts -> fetchRow($this -> oAccounts -> select() -> where($this -> oAccounts -> getPrimary() . '=' . (int)$iIdAccount . ' AND fk_account=' . $this -> oAccount -> userdata['UID']));

			if (isset($oAccount -> idaccount)) {
				$iIdAccount = $oAccount -> idaccount;

				$oGroup = $this -> fetchRow($this -> select() -> where($this -> getPrimary() . '=' . (int)$iIdGroup . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID']));

				if (isset($oGroup -> idgroup)) {
					$iIdGroup = $oGroup -> idgroup;
					$oMembers = $this -> getGroupMembers($iIdGroup);
					$iNoPrimary = $this -> oMembers -> delete($this -> oMembers -> getPrimary() . '=' . ((int)$iIdGroup) . ' AND accounts_idaccount=' . ((int)$iIdAccount));

					if ((int)$iNoPrimary == 1) {
						$aReturn['error'] = false;
						$aReturn['message'] = 'finished membership';
						Application_Model_Privileges::cleanPrivileges($oMembers, $this -> oAccount -> userdata['UID']);
					} else {
						$aReturn['message'] = 'Unknown error';
					}
				} else {
					$aReturn['message'] = 'Group not found';
				}
			} else {
				$aReturn['message'] = 'Accouns not found';
			}

		} else {
			$aReturn['message'] = 'Unknown error';
		}
		return $aReturn;

	}

	public function accountGroups() {

		$iUID = $this -> oAccount -> userdata['UID'];
		$oCache = new Application_Model_Cache($iUID . '_privileges');

		if (!$oCache -> cacheExists()) {
			$aSpacesBefore = false;
			$iKeyBefore = false;
			$oGroupsMember = $this -> oMembers -> fetchAll($this -> oMembers -> select() -> where($this -> oAccounts -> getTable() . '_' . $this -> oAccounts -> getPrimary() . '=' . $iUID));
			$oAccountGroups = $this -> fetchAll($this -> select() -> where($this -> oAccounts -> getTable() . '_' . $this -> oAccounts -> getPrimary() . '=' . $iUID));
			$oRightsAll = $this -> oRights -> getRights();
			$aGroups = array('MEMBER' => array(), 'OWNER' => array());
			$oNameSpaces = new Application_Model_Namespaces($this -> oSettings);

			if (is_object($oRightsAll)) {
				foreach ($oRightsAll as $oRight) {
					if (!isset($aRightNames)) {
						$aRightNames = array($oRight -> idright => $oRight -> name);
					} else {
						$aRightNames[$oRight -> idright] = $oRight -> name;
					}
				}
			}
			if (is_object($oAccountGroups)) {
				$aRightsAll = array();

				foreach ($oRightsAll as $oRight) {
					$aRightsAll[$oRight -> idright] = $oRight -> name;
				}
				foreach ($oAccountGroups as $oAccountGroup) {
					$aSpaces = array();
					$oSpaces = $this -> oSpaces -> fetchAll($this -> oSpaces -> select() -> setIntegrityCheck(false) -> from($this -> oSpaces -> getTable()) -> join('namespaces', 'namespaces_idnamespace=idnamespace', 'name') -> where($this -> oSpaces -> getPrimary() . '=' . $oAccountGroup -> idgroup));

					if (is_object($oSpaces)) {
						foreach ($oSpaces as $oSpace) {
							$aSpaces[$oSpace -> namespaces_idnamespace] = $oSpace -> name;
						}
					}
					$aGroups['OWNER'][$oAccountGroup -> idgroup]['RIGHTS'] = $aRightsAll;
					$aGroups['OWNER'][$oAccountGroup -> idgroup]['SPACES'] = $aSpaces;
				}
			}
			if (is_object($oGroupsMember)) {
				foreach ($oGroupsMember as $oGroupMember) {
					$oRights = $this -> oRights -> getGroupRights($oGroupMember -> groups_idgroup);
					$oSpaces = $this -> oSpaces -> fetchAll($this -> oSpaces -> select() -> setIntegrityCheck(false) -> from($this -> oSpaces -> getTable()) -> join('namespaces', 'namespaces_idnamespace=idnamespace', 'name') -> where($this -> oSpaces -> getPrimary() . '=' . $oGroupMember -> groups_idgroup));
					$aRights = array();
					$aSpaces = array();
					if (is_object($oRights)) {
						foreach ($oRights as $oRight) {
							$aRights[$oRight -> rights_idright] = $aRightNames[$oRight -> rights_idright];
						}
					}
					if (is_object($oSpaces)) {
						foreach ($oSpaces as $oSpace) {
							$aSpaces[$oSpace -> namespaces_idnamespace] = $oSpace -> name;
						}
					}
					$aGroups['MEMBER'][$oGroupMember -> groups_idgroup]['RIGHTS'] = array_intersect($aRightsAll, $aRights);
					$aGroups['MEMBER'][$oGroupMember -> groups_idgroup]['SPACES'] = $aSpaces;
				}
			}

			foreach ($aGroups['OWNER'] as $key => $value) {
				if (count($value['SPACES']) == 0) {
					unset($aGroups['OWNER'][$key]);
					continue;
				}
				if (!$aSpacesBefore) {
					$aSpacesBefore = &$aGroups['OWNER'][$key]['SPACES'];
					$iKeyBefore = $key;
				} else {
					foreach ($aGroups['OWNER'] as $keyb => $valueb) {
						if ($keyb != $iKeyBefore) {
							$aReset = array_intersect($valueb['SPACES'], $aSpacesBefore);
							foreach ($aReset as $ikeyreset => $sNamespace) {
								unset($aSpacesBefore[$ikeyreset]);
								if (count($aSpacesBefore) == 0) {
									unset($aGroups['OWNER'][$iKeyBefore]);
								}
							}
						}
					}
					$aSpacesBefore = &$aGroups['OWNER'][$key]['SPACES'];
					$iKeyBefore = $key;
				}

			}

			// clean Member array
			$aRmKeys = array_keys($aGroups['OWNER']);
			foreach ($aRmKeys as $iIndex => $iKey) {
				if (isset($aGroups['MEMBER'][$iKey])) {
					unset($aGroups['MEMBER'][$iKey]);
				}
			}
			unset($aSpacesBefore, $iKeyBefore);
			$aSpacesBefore = false;
			$iKeyBefore = false;
			foreach ($aGroups['MEMBER'] as $key => $value) {
				if (count($value['RIGHTS']) == 0 || count($value['SPACES']) == 0) {
					unset($aGroups['MEMBER'][$key]);
					continue;
				}
				if (!$aSpacesBefore) {
					$aSpacesBefore = &$aGroups['MEMBER'][$key]['SPACES'];
					$iKeyBefore = $key;
				} else {
					foreach ($aGroups['MEMBER'] as $keyb => $valueb) {
						if ($keyb != $iKeyBefore) {
							$aReset = array_intersect($valueb['SPACES'], $aSpacesBefore);
							foreach ($aReset as $ikeyreset => $sNamespace) {
								unset($aSpacesBefore[$ikeyreset]);
								if (count($aSpacesBefore) == 0) {
									unset($aGroups['MEMBER'][$iKeyBefore]);
								}
							}
						}
					}
					$aSpacesBefore = &$aGroups['MEMBER'][$key]['SPACES'];
					$iKeyBefore = $key;
				}
			}

			$oCache -> writeCache(serialize($aGroups));

		} else {
			$aGroups = unserialize($oCache -> readCache());
		}

		return $aGroups;
	}

	public function setGroupright($iIdGroup, $aSetRight) {
		$oRights = new Application_Model_Rights();
		$oRightsAll = $oRights -> getRights();
		$aReturn = array('error' => true);

		if (is_array($aSetRight)) {
			$oGroup = $this -> fetchRow($this -> select() -> where($this -> getPrimary() . '=' . (int)$iIdGroup . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID']));
			if (isset($oGroup -> idgroup)) {
				$iIdGroup = $oGroup -> idgroup;

				foreach ($oRightsAll as $oRight) {
					if (isset($aSetRight[$oRight -> name])) {
						if ($aSetRight[$oRight -> name] == 1) {

							if ($oRights -> setRight($oGroup -> idgroup, $oRight -> idright)) {
								$aReturn['error'] = false;
								$aReturn['message'] = 'Groupright was set';
								Application_Model_Privileges::cleanPrivileges($this -> getGroupMembers($iIdGroup), $this -> oAccount -> userdata['UID']);
							} else {
								$aReturn['message'] = 'Unknown error';
							}
						} elseif ($aSetRight[$oRight -> name] == 0) {
							if ($oRights -> unsetRight($oGroup -> idgroup, $oRight -> idright)) {
								$aReturn['error'] = false;
								$aReturn['message'] = 'Groupright was unset';
								Application_Model_Privileges::cleanPrivileges($this -> getGroupMembers($iIdGroup), $this -> oAccount -> userdata['UID']);
							} else {
								$aReturn['message'] = 'Unknown error';
							}
						}
						break;
					}
				}
			} else {
				$aReturn['message'] = 'Group not found';
			}
		} else {
			$aReturn['message'] = 'Wrong configuration';
		}
		return $aReturn;
	}

}
?>