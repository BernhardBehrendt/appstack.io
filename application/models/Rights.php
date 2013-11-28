<?php
class Application_Model_Rights extends Application_Model_Table {

	private $oCountrys;
	private $oSettings;
	private $oRates;
	private $oCache;
	public $oRights;

	public function __construct() {
		parent::__construct();

		$this -> setTable('rights');
		$this -> setPrimary('idright');

		$this -> oCache = new Application_Model_Cache('grouptights');

		if (!$this -> oCache -> cacheExists()) {
			$this -> setupRights();
		}

		$this -> oRights = $this -> getRights();
	}

	public function getRights() {

		if (!$this -> oCache -> cacheExists()) {
			$this -> oCache -> writeCache(serialize($this -> fetchAll()));
		}

		if (!is_object($this -> oRights)) {
			$this -> oRights = unserialize($this -> oCache -> readCache());
		}

		return $this -> oRights;
	}

	public function getGroupRights($iGroupId) {
		$oTblGroupRights = new Application_Model_Table();
		$oTblGroupRights -> setTable('grouprights');
		$oTblGroupRights -> setPrimary('groups_idgroup');

		$oGroupRights = $oTblGroupRights -> fetchAll($oTblGroupRights -> select() -> where($oTblGroupRights -> getPrimary() . '=' . (int)$iGroupId));
		if ($oGroupRights -> count() > 0) {
			return $oGroupRights;
		}
		return false;
	}

	public function setRight($iGroupId, $iRightId) {
		$oTblGrpRights = new Application_Model_Table();
		$oTblGrpRights -> setTable('grouprights');
		$oTblGrpRights -> setPrimary('groups_idgroup');

		$iEntryExists = $oTblGrpRights -> fetchAll($oTblGrpRights -> select() -> where($oTblGrpRights -> getPrimary() . '=' . (int)$iGroupId . ' AND rights_idright=' . (int)$iRightId)) -> count();

		if ($iEntryExists == 0) {
			$oTblGrpRights -> insert(array($oTblGrpRights -> getPrimary() => $iGroupId, 'rights_idright' => $iRightId));
			return true;
		} else {
			return true;
		}

		return false;
	}

	public function unsetRight($iGroupId, $iRightId) {
		$oTblGrpRights = new Application_Model_Table();
		$oTblGrpRights -> setTable('grouprights');
		$oTblGrpRights -> setPrimary('groups_idgroup');

		$iEntryExists = $oTblGrpRights -> fetchAll($oTblGrpRights -> select() -> where($oTblGrpRights -> getPrimary() . '=' . (int)$iGroupId . ' AND rights_idright=' . (int)$iRightId)) -> count();

		if ($iEntryExists > 0) {
			if ($oTblGrpRights -> delete($oTblGrpRights -> getPrimary() . '=' . $iGroupId . ' AND rights_idright=' . $iRightId)) {
				return true;
			}
		}
		return false;
	}

	private function setupRights() {
		if ($this -> fetchAll() -> count() == 0) {

			$aUserRights = array('read', 'change', 'create', 'delete', 'extend');

			foreach ($aUserRights as $iKey => $sRight) {
				$this -> insert(array('name' => $sRight));
			}

			echo 'Installed group rights. Please reload';
			exit ;
		}
	}

}
?>