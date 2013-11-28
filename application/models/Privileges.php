<?php
class Application_Model_Privileges {

	private $aAccount;
	private $oSettings;
	private $aMemberships;

	public function __construct(Zend_Session_Namespace $oSession) {

		$this -> oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
		$this -> aAccount = $oSession -> userdata;
		$oAccount = new Application_Model_Account($this -> oSettings);
		$oGroups = new Application_Model_Groups($this -> oSettings);
		//print_r($this -> aAccount);
		$this -> aMemberships = $oGroups -> accountGroups();
		$this -> oAccount = $oSession -> userdata;
		echo '<pre>' . print_r($this -> aMemberships, true) . '</pre>';
	}

	public function __destruct() {

	}

	public static function cleanPrivileges($oMembers, $iAccountId) {
		$oCache = new Application_Model_Cache($iAccountId . '_privileges');
		if (is_object($oMembers)) {
			foreach ($oMembers as $oMember) {
				$oCache -> setCache($oMember -> accounts_idaccount . '_privileges');
				$oCache -> deleteCache();
			}
		}

		$oCache -> setCache($iAccountId . '_privileges');
		$oCache -> deleteCache();
	}

}
?>