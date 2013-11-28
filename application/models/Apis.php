<?php
define('TPL_CREATE_SERVER_KEY', 'website.createserverkey.html');
define('TPL_CREATE_BROWSER_KEY', 'website.createbrowserkey.html');
define('TPL_SERVER_KEY_TABLE', 'website.serverkey.table.html');
define('TPL_BROWSER_KEY_TABLE', 'website.browserkey.table.html');

require_once (APPLICATION_PATH . '/../library/oauth/OAuthStore.php');

class Application_Model_Apis extends Application_Model_Table {
	private $oOAuthStore;
	private $oServerReg;
	private $oSettings;
	private $oAccount;
	private $oAccounts;

	public function __construct($oSettings) {
		parent::__construct();
		$this -> oSettings = $oSettings;
		$this -> setTable('oauth_server_registry');
		$this -> setPrimary('osr_id');
		$this -> oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$this -> oAccounts = new Application_Model_Account($this -> oSettings);
	}

	public function getMyServerKeys($bGetAll = false) {
		////
		$aLimits = array('5' => 5, '10' => 10, '25' => 25, '50' => 50, '100' => 100);
		if (isset($this -> oAccount -> userdata['UID'])) {
			$sSubExpr = '';

			if (isset($_GET['filter_letter']) && strlen($_GET['filter_letter']) == 1 && $_GET['filter_letter'] != '*') {
				$this -> oAccount -> userdata['FILTERLETTER'] = $_GET['filter_letter'];
			} else {
				if (isset($_GET['filter_letter']) && $_GET['filter_letter'] == '*') {
					$this -> oAccount -> userdata['FILTERLETTER'] = '*';
				}
			}

			if (!isset($_GET['filter_expr'])) {
				if (isset($this -> oAccount -> userdata['FILTERLETTER']) && $this -> oAccount -> userdata['FILTERLETTER'] != '*') {
					$sSubExpr = " AND osr_application_uri LIKE 'http://" . strtolower($this -> oAccount -> userdata['FILTERLETTER']) . "%'";
				}
			} else {

				if (isset($_GET['filter_expr']) && strlen($_GET['filter_expr']) > 1) {
					$this -> oAccount -> userdata['FILTERLETTER'] = $_GET['filter_expr'];
					$sSubExpr = " AND osr_application_uri LIKE '%" . strtolower(Preprocessor_String::filterBadChars($_GET['filter_expr'])) . "%'";
				}
			}

			$iAccountId = $this -> oAccount -> userdata['UID'];

			if (!isset($this -> oAccount -> userdata['LIMITRESULT'])) {
				$this -> oAccount -> userdata['LIMITRESULT'] = 5;
			}

			if (isset($_GET['limit']) && isset($aLimits[$_GET['limit']])) {
				$this -> oAccount -> userdata['LIMITRESULT'] = $aLimits[$_GET['limit']];
			}

			if (isset($_GET['list_index'])) {
				$this -> oAccount -> userdata['LISTINDEX'] = (int)$_GET['list_index'];
			} else {
				$this -> oAccount -> userdata['LISTINDEX'] = 0;
			}
			if ($bGetAll) {
				$sSubExpr = '';
			}

			if ($bGetAll) {
				$oSelect = $this -> select() -> setIntegrityCheck(false) -> from($this -> getTable(), array('osr_id', 'osr_consumer_key', 'osr_consumer_secret', 'osr_requester_name', 'osr_requester_email', 'osr_callback_uri', 'osr_application_uri', )) -> joinleft('oauth_server_token', 'ost_token_type="access" AND oauth_server_token.ost_osr_id_ref=' . $this -> getTable() . '.' . $this -> getPrimary(), array('ost_token', 'ost_token_secret')) -> where('osr_usa_id_ref=' . $this -> oAccount -> userdata['UID']);
			} else {

				$oAffectedRows = $this -> fetchAll($this -> select() -> from($this -> getTable(), array('osr_id')) -> where('osr_usa_id_ref=' . $this -> oAccount -> userdata['UID'] . $sSubExpr) -> limit($this -> oAccount -> userdata['LIMITRESULT'], $this -> oAccount -> userdata['LISTINDEX']));
				$this -> oAccount -> userdata['SERVERKEYS'] = $this -> fetchAll($this -> select() -> from($this -> getTable(), array('osr_id')) -> where('osr_usa_id_ref=' . $this -> oAccount -> userdata['UID'] . $sSubExpr)) -> count();
				if (is_object($oAffectedRows)) {
					foreach ($oAffectedRows as $oRow) {
						if (!isset($aAffected)) {
							$aAffected = array();
						}
						array_push($aAffected, $oRow -> osr_id);
					}

					if (isset($aAffected)) {
						$oSelect = $this -> select() -> setIntegrityCheck(false) -> from($this -> getTable(), array('osr_id', 'osr_consumer_key', 'osr_consumer_secret', 'osr_requester_name', 'osr_requester_email', 'osr_callback_uri', 'osr_application_uri', )) -> joinleft('oauth_server_token', 'ost_token_type="access" AND oauth_server_token.ost_osr_id_ref=' . $this -> getTable() . '.' . $this -> getPrimary(), array('ost_token', 'ost_token_secret')) -> where('osr_id IN (' . implode(',', $aAffected) . ')');
					}
				}
			}
			if (isset($oSelect)) {
				$oResult = $this -> fetchAll($oSelect);
				return $oResult;
			}
		}

		return false;
	}

	public function getMyBrowserKeys($bGetAll = false) {
		////
		$oTblBrowserKeys = new Application_Model_Table();
		$oTblBrowserKeys -> setTable('browserkeys');
		$oTblBrowserKeys -> setPrimary('idbrowserkey');

		$aLimits = array('5' => 5, '10' => 10, '25' => 25, '50' => 50, '100' => 100);
		if (isset($this -> oAccount -> userdata['UID'])) {
			$sSubExpr = '';

			if (isset($_GET['filter_letter']) && strlen($_GET['filter_letter']) == 1 && $_GET['filter_letter'] != '*') {
				$this -> oAccount -> userdata['FILTERLETTER'] = $_GET['filter_letter'];
			} else {
				if (isset($_GET['filter_letter']) && $_GET['filter_letter'] == '*') {
					$this -> oAccount -> userdata['FILTERLETTER'] = '*';
				}
			}

			if (!isset($_GET['filter_expr'])) {
				if (isset($this -> oAccount -> userdata['FILTERLETTER']) && $this -> oAccount -> userdata['FILTERLETTER'] != '*') {
					$sSubExpr = " AND domain LIKE 'http://" . strtolower($this -> oAccount -> userdata['FILTERLETTER']) . "%'";
				}
			} else {

				if (isset($_GET['filter_expr']) && strlen($_GET['filter_expr']) > 1) {
					$this -> oAccount -> userdata['FILTERLETTER'] = $_GET['filter_expr'];
					$sSubExpr = " AND domain LIKE '%" . strtolower(Preprocessor_String::filterBadChars($_GET['filter_expr'])) . "%'";
				}
			}

			$iAccountId = $this -> oAccount -> userdata['UID'];

			if (!isset($this -> oAccount -> userdata['LIMITRESULT'])) {
				$this -> oAccount -> userdata['LIMITRESULT'] = 5;
			}

			if (isset($_GET['limit']) && isset($aLimits[$_GET['limit']])) {
				$this -> oAccount -> userdata['LIMITRESULT'] = $aLimits[$_GET['limit']];
			}

			if (isset($_GET['list_index'])) {
				$this -> oAccount -> userdata['LISTINDEX'] = (int)$_GET['list_index'];
			} else {
				$this -> oAccount -> userdata['LISTINDEX'] = 0;
			}
			if ($bGetAll) {
				$sSubExpr = '';
			}
			$this -> oAccount -> userdata['BROWSERKEYS'] = $oTblBrowserKeys -> fetchAll($oTblBrowserKeys -> select() -> where('accounts_idaccount=' . $this -> oAccount -> userdata['UID'])) -> count();
			if ($bGetAll) {
				$oSelect = $oTblBrowserKeys -> select() -> where('accounts_idaccount=' . $this -> oAccount -> userdata['UID']);
			} else {
				$oSelect = $oTblBrowserKeys -> select() -> from($oTblBrowserKeys -> getTable(), array('idbrowserkey', 'key', 'domain', 'anywhere')) -> where('accounts_idaccount=' . $this -> oAccount -> userdata['UID']) -> limit($this -> oAccount -> userdata['LIMITRESULT'], $this -> oAccount -> userdata['LISTINDEX']);
			}

			if (isset($oSelect)) {
				$oResult = $oTblBrowserKeys -> fetchAll($oSelect);
				return $oResult;
			}
		}

		return false;
	}

	public function createConsumer($aConsumerConfig = false) {
		$aBudget = $this -> oAccounts -> getMyBudget();
		if (is_object($this -> oAccounts) && $aBudget['HASSERVERKEYS'] > 0) {

			if (!$aConsumerConfig) {

				$oSmarty = Application_Model_Smarty::getInstance();

				return $oSmarty -> fetch(TPL_CREATE_SERVER_KEY);
			} else {
				if (!$this -> oAccounts -> setUsage($this -> oAccount -> userdata['UID'], array('maxserverkeys' => 1))) {
					return array('error' => true, 'message' => 'Max namespaces limit reached');
				}

				$sAppUrl = $aConsumerConfig['appurl'];
				$sCallbackUrl = $aConsumerConfig['callback'];
				$changesMail = $aConsumerConfig['appmail'];
				$sAdmin = Preprocessor_String::filterBadChars($aConsumerConfig['admin']);

				if (strlen($sAppUrl) > 8 && strlen($sCallbackUrl) > 8) {
					($dboauth = mysql_connect($this -> oSettings -> resources -> db -> params -> host, $this -> oSettings -> resources -> db -> params -> username, $this -> oSettings -> resources -> db -> params -> password)) || die(mysql_error());
					mysql_select_db($this -> oSettings -> resources -> db -> params -> dbname, $dboauth) || die(mysql_error());

					OAuthStore::instance('MySQL', array('conn' => $dboauth));
					$aRequester = array('application_uri' => $sAppUrl, 'callback_uri' => $sCallbackUrl, 'requester_email' => $changesMail, 'requester_name' => $sAdmin);
					$store = OAuthStore::instance();
					$key = $store -> updateConsumer($aRequester, $this -> oAccount -> userdata['UID'], true);
					$c = $store -> getConsumer($key, $this -> oAccount -> userdata['UID']);
					if (is_array($c)) {
						return array('error' => false, 'consumer' => $c);
					}

					$this -> oAccounts -> setUsage($this -> oAccount -> userdata['UID'], array('maxserverkeys' => -1));
					return array('error' => true, 'message' => 'no consumer created');

				}

			}
		}
		return false;
	}

	public function createBrowserkey($aBrowserConfig = false) {
		$aBudget = $this -> oAccounts -> getMyBudget();
		if (is_object($this -> oAccounts) && $aBudget['HASBROWSERKEYS'] > 0) {

			if (!$aBrowserConfig) {

				$oSmarty = Application_Model_Smarty::getInstance();

				return $oSmarty -> fetch(TPL_CREATE_BROWSER_KEY);
			} else {
				if (!$this -> oAccounts -> setUsage($this -> oAccount -> userdata['UID'], array('maxbrowserkeys' => 1))) {
					return array('error' => true, 'message' => 'Max browserkey limit reached');
				}

				$iAnywhere = 0;
				$sDomain = '';
				if (isset($aBrowserConfig['anywhere']) == 1) {
					$iAnywhere = 1;
				}
				if (isset($aBrowserConfig['appurl'])) {
					$sDomain = Preprocessor_String::filterBadChars($aBrowserConfig['appurl']);
				}
				if (strlen($sDomain) > 8 || $iAnywhere == 1) {
					$oTblBrowserKeys = new Application_Model_Table();
					$oTblBrowserKeys -> setTable('browserkeys');
					$oTblBrowserKeys -> setPrimary('idbrowserkey');
					$sKey = md5($this -> oSettings -> user -> security -> salt . $sDomain . $iAnywhere . time('void') . rand(1, 100000));
					$oTblBrowserKeys -> insert(array('accounts_idaccount' => $this -> oAccount -> userdata['UID'], 'key' => $sKey, 'domain' => $sDomain, 'anywhere' => $iAnywhere));
					return array('error' => false, 'message' => 'Browserkey created');

				}
			}
		}
		return false;
	}

	public function listServerKeys() {
		$oMyServerKeys = $this -> getMyServerKeys();
		$aServerKeys = array();
		if (is_object($oMyServerKeys)) {
			foreach ($oMyServerKeys as $oRow) {
				if (isset($aServerKeys[$oRow -> osr_id])) {
					if (strlen($oRow -> ost_token) && strlen($oRow -> ost_token) > 0) {
						array_push($aServerKeys[$oRow -> osr_id]['access'], array('access_token' => $oRow -> ost_token, 'access_secret' => $oRow -> ost_token_secret));
					}
				} else {
					$aServerKeys[$oRow -> osr_id] = array('consumer_key' => $oRow -> osr_consumer_key, 'consumer_secret' => $oRow -> osr_consumer_secret, 'application_uri' => $oRow -> osr_application_uri, 'callback_uri' => $oRow -> osr_callback_uri, 'access' => array());
					if (strlen($oRow -> ost_token) && strlen($oRow -> ost_token) > 0) {
						array_push($aServerKeys[$oRow -> osr_id]['access'], array('access_token' => $oRow -> ost_token, 'access_secret' => $oRow -> ost_token_secret));
					}
				}
			}
			//return $aServerKeys;

			$oSmarty = Application_Model_Smarty::getInstance();
			$oSmarty -> assign('SM_A_SERVERKEYS', $aServerKeys);
			$oSmarty -> assign('SM_I_LIMITATION', $this -> oSettings -> website -> site -> basehref . 'apis/serverkeys');
			$oSmarty -> assign('SM_I_PERPAGE', $this -> oAccount -> userdata['LIMITRESULT']);
			$oSmarty -> assign('SM_I_SERVERKEYS', $this -> oAccount -> userdata['SERVERKEYS']);
			$oSmarty -> assign('SM_I_INDEX', $this -> oAccount -> userdata['LISTINDEX']);
			return $oSmarty -> fetch(TPL_SERVER_KEY_TABLE);
		}
	}

	public function listBrowserKeys() {
		$oMyBrowserKeys = $this -> getMyBrowserKeys();
		$aBrowserKeys = array();
		if (is_object($oMyBrowserKeys)) {
			foreach ($oMyBrowserKeys as $oRow) {
				$oMyBrowserKeys[$oRow -> idbrowserkey] = array('key' => $oRow -> key, 'domain' => $oRow -> domain, 'anywhere' => $oRow -> anywhere);
			}
			//return $aServerKeys;

			$oSmarty = Application_Model_Smarty::getInstance();
			$oSmarty -> assign('SM_A_BROWSERKEYS', $oMyBrowserKeys);
			$oSmarty -> assign('SM_I_LIMITATION', $this -> oSettings -> website -> site -> basehref . 'apis/browserkeys');
			$oSmarty -> assign('SM_I_PERPAGE', $this -> oAccount -> userdata['LIMITRESULT']);
			$oSmarty -> assign('SM_I_BROWSERKEYS', $this -> oAccount -> userdata['BROWSERKEYS']);
			$oSmarty -> assign('SM_I_INDEX', $this -> oAccount -> userdata['LISTINDEX']);
			return $oSmarty -> fetch(TPL_BROWSER_KEY_TABLE);
		}
	}

	public function deleteAccessToken($sToken) {
		if (strlen($sToken) > 0) {
			$oTokens = new Application_Model_Table();
			$oTokens -> setTable('oauth_server_token');
			$oTokens -> setPrimary('ost_usa_id_ref');

			if ($oTokens -> delete($oTokens -> getPrimary() . '=' . $this -> oAccount -> userdata['UID'] . ' AND ost_token_type="access" AND ost_token="' . $sToken . '"')) {
				return array('error' => false, 'message' => 'Access token was deleted');
			}

			return array('error' => true, 'message' => 'Token doesn\'t exist');
		}
		return array('error' => true, 'message' => 'Missing token');
	}

	public function deleteConsumer($iIdConsumer) {
		if ((int)$iIdConsumer > 0) {
			if ($this -> delete($this -> getPrimary() . '=' . ((int)$iIdConsumer) . ' AND osr_usa_id_ref=' . $this -> oAccount -> userdata['UID'])) {
				$this -> oAccounts -> setUsage($this -> oAccount -> userdata['UID'], array('maxserverkeys' => -1));
				return array('error' => false, 'message' => 'Consumer was deleted');
			} else {
				return array('error' => true, 'message' => 'Consumer doesn\'t exists');
			}
		}
		return array('error' => true, 'message' => 'Wrong configuration');
	}

	public function deletebrowserkey($iBrowserkey) {
		if ((int)$iBrowserkey > 0) {
			$oTblBrowserKeys = new Application_Model_Table();
			$oTblBrowserKeys -> setTable('browserkeys');
			$oTblBrowserKeys -> setPrimary('idbrowserkey');

			if ($oTblBrowserKeys -> delete($oTblBrowserKeys -> getPrimary() . '=' . $iBrowserkey . ' AND accounts_idaccount=' . $this -> oAccount -> userdata['UID'])) {
				$this -> oAccounts -> setUsage($this -> oAccount -> userdata['UID'], array('maxbrowserkeys' => -1));
				return array('error' => false, 'message' => 'Browserkey was deleted');
			}

			return array('error' => true, 'message' => 'Browserkey doesn\'t exist');
		}
		return array('error' => true, 'message' => 'Missing browserkey');
	}

}
?>