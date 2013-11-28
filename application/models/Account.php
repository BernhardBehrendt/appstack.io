<?php define('TPL_WIDGET_USERPANEL', 'website.userpanel.html');
define('TPL_MAIL_RESETPW', 'mail.resetpw.html');
define('TPL_MAIL_NOPW', 'mail.nopw.html');
define('TPL_MAIL_WELCOME', 'mail.welcome.html');
define('TPL_MAIL_CONFIRMATION', 'mail.confirmation.html');
define('TPL_MAIL', 'mail.main.html');
class Application_Model_Account extends Application_Model_Table {

	private $oCountrys;
	private $oSettings;
	private $oRates;

	public function __construct($oSettings) {
		parent::__construct();

		$this -> oSettings = $oSettings;
		$this -> setTable('accounts');
		$this -> setPrimary('idaccount');

		$this -> oRates = new Application_Model_Rates($this -> oSettings);

		$this -> oCountrys = new Application_Model_Table();
		$this -> oCountrys -> setTable('countries');
		$this -> oCountrys -> setPrimary('idcountry');

		$this -> createCountrys();

	}

	public function lookup($sPreferedUser) {
		$sSelect = $this -> select() -> where('username="' . $sPreferedUser . '"');
		$iCountMatch = $this -> fetchAll($sSelect) -> count();

		if ($iCountMatch === 0) {
			return array('error' => false, 'message' => 'Username is available');
		} else {
			return array('error' => true, 'message' => 'Username is already in use');
		}
	}

	public function create($aRegistration, $oAccount = false) {
		if (is_object($oAccount)) {
			if (!$this -> setUsage($oAccount -> userdata['UID'], array('maxusers' => 1))) {
				return Preprocessor_String::createTag('h3', false, 'Max User limit reached');
			}
		}
		if ($aRegistration['pwa'] === $aRegistration['pwb']) {
			$aRegistration['pwa'] = md5(md5($this -> oSettings -> user -> security -> salt) . md5($aRegistration['pwa']));
			if ($aRegistration['maila'] === $aRegistration['mailb']) {
				if ($this -> fetchAll($this -> select() -> where('mail="' . $aRegistration['maila'] . '"')) -> count() === 0) {

					$aRegistration['user'] = strtolower($aRegistration['user']);

					if ($this -> fetchAll($this -> select() -> where('username="' . $aRegistration['user'] . '"')) -> count() === 0) {

						$iNewAccountID = $this -> insert(array('countries_idcountry' => $aRegistration['country'], 'username' => $aRegistration['user'], 'password' => $aRegistration['pwa'], 'mail' => $aRegistration['maila'], 'address' => $aRegistration['address'], 'city' => $aRegistration['city'], 'company' => $aRegistration['company'], 'phone' => $aRegistration['phone']));

						if (is_object($oAccount)) {
							$this -> setAccountRate($iNewAccountID, false);

							//$this -> setUsage($oAccount -> userdata['UID'], array('maxusers' => 1));

							$iNewAccountID = $oAccount -> userdata['UID'];
							$sActivation = $this -> sendActivation($aRegistration, $oAccount, $aRegistration['pwb']);
						} else {
							$this -> setAccountRate($iNewAccountID, 1);
							$sActivation = $this -> sendActivation($aRegistration);
						}

						$this -> update(array('fk_account' => $iNewAccountID, 'activation' => $sActivation), 'username="' . $aRegistration['user'] . '"');

						return true;
					} else {
						return Preprocessor_String::createTag('h3', false, 'Username is already in use');
					}
				} else {
					return Preprocessor_String::createTag('h3', false, 'E-mail address is already in use');
				}
			} else {
				return Preprocessor_String::createTag('h3', false, 'Your e-mail didn\'t match');
			}
		} else {
			return Preprocessor_String::createTag('h3', false, 'Your password didn\'t match');
		}
	}

	public function sendActivation($aRegistration, $oAccount = false, $sPassword = false) {

		$oSmarty = Application_Model_Smarty::getInstance();

		$oConfirmMail = new Zend_Mail('UTF-8');
		$sActivation = md5($aRegistration['user'] . $aRegistration['pwa'] . time('void'));
		$oSmarty -> assign('SM_STR_USERNAME', $aRegistration['user']);
		$oSmarty -> assign('SM_STR_CONFIRMATION', $this -> oSettings -> website -> site -> basehref . 'account/activate/?activation=' . $sActivation);

		if (!$oAccount) {
			$oSmarty -> assign('SM_STR_HEADLINE', 'thank you for your registration');
			$oSmarty -> assign('SM_STR_TEXT', 'Please finish your registration by clicking on the following link.');
			$oSmarty -> assign('SM_STR_PASSWORD', '');
		} else {
			$oSmarty -> assign('SM_STR_HEADLINE', 'an account for you has been created');
			$oSmarty -> assign('SM_STR_PASSWORD', '<br/>Your password for login is <b>' . $sPassword . '</b><br/><br/>');
			$oSmarty -> assign('SM_STR_TEXT', 'Please activate your account by clicking the following link.');
		}
		$oSmarty -> assign('SM_STR_MAILCONTENT', $oSmarty -> fetch(TPL_MAIL_CONFIRMATION));
		$oConfirmMail -> setBodyHtml($oSmarty -> fetch(TPL_MAIL));
		$oConfirmMail -> setFrom($this -> oSettings -> website -> mail -> confirmation, 'appstack.io');
		$oConfirmMail -> addTo($_POST['maila']);
		if (is_object($oAccount)) {
			$oConfirmMail -> addCc($oAccount -> userdata['EMAIL']);
		}
		if (!$oAccount) {
			$oConfirmMail -> setSubject('Please confirm your registration');
		} else {
			$oConfirmMail -> setSubject('Please activate your account');
		}

		if ($oConfirmMail -> send()) {
			return $sActivation;
		}
		return false;

	}

	public function activate($aActivation) {
		$oSmarty = Application_Model_Smarty::getInstance();

		if (strlen($aActivation['activation']) == 32) {
			$oRow = $this -> fetchRow($this -> select() -> where('activation="' . $aActivation['activation'] . '"'));
			if ($this -> update(array('activated' => 1, 'activation' => md5($aActivation['activation'] . time() . $this -> oSettings -> user -> security -> salt)), 'activation="' . $aActivation['activation'] . '" AND activated=0')) {

				$oConfirmMail = new Zend_Mail('UTF-8');
				$oSmarty -> assign('SM_STR_MAILCONTENT', $oSmarty -> fetch(TPL_MAIL_WELCOME));

				$oConfirmMail -> setBodyHtml($oSmarty -> fetch(TPL_MAIL));
				$oConfirmMail -> setFrom($this -> oSettings -> website -> mail -> confirmation, 'appstack.io');
				$oConfirmMail -> addTo($oRow -> mail);
				$oConfirmMail -> setSubject('Welcome on appstack.io systems');
				if ($oConfirmMail -> send()) {
					header('Location:' . $this -> oSettings -> website -> site -> basehref);
				}
			} else {
				header('Location:' . $this -> oSettings -> website -> site -> basehref);
			}
		}
	}

	public function setAccountRate($iIdAcount, $iRateId) {
		$oAccountRates = new Application_Model_Table();
		$oAccountRates -> setTable('accountrates');
		$oAccountRates -> setPrimary('accounts_idaccount');

		$oUsage = new Application_Model_Table();
		$oUsage -> setTable('usages');
		$oUsage -> setPrimary('accountrates_accounts_idaccount');

		$aCols = $oAccountRates -> getCols();

		$aInsert = array();

		foreach ($aCols as $iColNum => $aCol) {
			if (!isset($aInsert[$aCol['FIELDNAME']])) {
				$aInsert[$aCol['FIELDNAME']] = 0;
			}
		}

		$aInsertUsage = $aInsert;

		if (is_int($iRateId)) {
			$oRate = $this -> oRates -> getRate($iRateId);
			foreach ($aInsert as $sColName => $iValue) {
				$aInsert[$sColName] = $oRate -> $sColName;
			}
		}

		$aInsert[$oAccountRates -> getPrimary()] = $iIdAcount;
		// Hier muss die ID Gesichert werden und ein neur 0er Datensatz muss in usages abgelegt werdmen

		$iAccountRate = $oAccountRates -> insert($aInsert);
		$aInsertUsage[$oUsage -> getPrimary()] = $iAccountRate;

		if ($oUsage -> insert($aInsertUsage)) {
			return $iAccountRate;
		}
	}

	public function getRate($iIdAccount) {
		if ((int)$iIdAccount > 0) {
			$oAccountRates = new Application_Model_Table();
			$oAccountRates -> setTable('accountrates');
			$oAccountRates -> setPrimary('accounts_idaccount');
			$oRateMatch = $oAccountRates -> fetchRow($oAccountRates -> select() -> where($oAccountRates -> getPrimary() . '=' . $iIdAccount));
			if (is_object($oRateMatch)) {
				$aRateMatch = array();

				foreach ($oRateMatch as $sColname => $iValue) {
					if ($sColname != $oAccountRates -> getPrimary()) {
						$aRateMatch[$sColname] = $iValue;
					}
				}
				return $aRateMatch;
			}
		}
		return false;
	}

	public function getUsage($iAccountID) {
		if ((int)$iAccountID > 0) {
			$oUsage = new Application_Model_Table();
			$oUsage -> setTable('usages');
			$oUsage -> setPrimary('accountrates_accounts_idaccount');
			$oRateMatch = $oUsage -> fetchRow($oUsage -> select() -> where($oUsage -> getPrimary() . '=' . $iAccountID));
			if (is_object($oRateMatch)) {
				$aRateMatch = array();

				foreach ($oRateMatch as $sColname => $iValue) {
					if ($sColname != $oUsage -> getPrimary()) {
						$aRateMatch[$sColname] = $iValue;
					}
				}

				return $aRateMatch;
			}
		}
		return false;
	}

	public function setUsage($iIdAccount, $aUpdateValue) {
		$oUsage = new Application_Model_Table();
		$oUsage -> setTable('usages');
		$oUsage -> setPrimary('accountrates_accounts_idaccount');
		
		$oRateMatch = $oUsage -> fetchRow($oUsage -> select() -> where($oUsage -> getPrimary() . '=' . $iIdAccount));

		$oAccountRate = $this -> getRate($iIdAccount);
		$aUpdate = array();
		$sUpdate = '';
		$sAndExpr = '';
		foreach ($oRateMatch as $sColName => $iValue) {
			if ($sColName != $oUsage -> getPrimary() && isset($aUpdateValue[$sColName])) {
				if (!isset($aUpdate[$sColName]) && ($iValue + $aUpdateValue[$sColName]) <= $oAccountRate[$sColName]) {
					$aUpdate[$sColName] = new Zend_Db_Expr($sColName.'+'.$aUpdateValue[$sColName]);
				}
			}
		}
		if (count($aUpdate) > 0) {
			$iRowUpdate = $oUsage -> update($aUpdate, $oUsage -> getPrimary() . '=' . $iIdAccount . $sAndExpr);
			if ($iRowUpdate == 1) {
				return true;
			}
		}
		return false;
	}

	public function logIn($aLogin) {
		$oAccount = Application_Model_Session::getInstance('ACCOUNT');
		if (!isset($oAccount -> userdata['USERNAME'])) {
			if (isset($aLogin['username']) && isset($aLogin['password'])) {
				$aLogin['password'] = md5(md5($this -> oSettings -> user -> security -> salt) . md5($aLogin['password']));
				$oUser = $this -> fetchRow($this -> select() -> where('username="' . $aLogin['username'] . '" AND password="' . $aLogin['password'] . '" AND activated=1'));
				if (is_object($oUser)) {
					//echo md5(session_id());exit;
					$this -> update(array('lastlogin' => new Zend_Db_Expr('NOW()'), 'sid' => md5(session_id())), $this -> getPrimary() . '=' . $oUser -> idaccount . ' AND activated=1');

					$aUser = array('UID' => $oUser -> idaccount, 'SIDHASH' => md5(session_id()), 'USERNAME' => $oUser -> username, 'LOGIN' => time('void'), 'EMAIL' => $oUser -> mail);
					$oAccount -> userdata = $aUser;

					return array('error' => false, 'message' => 'Login was successful');

				} else {
					sleep(5);
					return array('error' => true, 'message' => 'Login error');
				}
			}
		} else {
			return array('error' => true, 'message' => 'User already loged in');
		}
	}

	public function restLogin($iIdUser) {
		$oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$oUser = $this -> fetchRow($this -> select() -> where($this -> getPrimary() . '=' . $iIdUser . ' AND activated=1'));
		if (is_object($oUser)) {
			$this -> update(array('lastlogin' => new Zend_Db_Expr('NOW()'), 'sid' => md5(session_id())), $this -> getPrimary() . '=' . $oUser -> idaccount . ' AND activated=1');

			$aUser = array('UID' => $oUser -> idaccount, 'SIDHASH' => md5(session_id()), 'USERNAME' => $oUser -> username, 'LOGIN' => time('void'), 'EMAIL' => $oUser -> mail);
			$oAccount -> userdata = $aUser;

			return true;

		} else {
			return false;
		}

	}

	public function checkLogin() {

		$oAccount = Application_Model_Session::getInstance('ACCOUNT');

		if (isset($oAccount -> userdata['SIDHASH'])) {
			$iIdentify = $this -> fetchAll($this -> select() -> where($this -> getPrimary() . '=' . $oAccount -> userdata['UID'] . ' AND sid="' . $oAccount -> userdata['SIDHASH'] . '"')) -> count();
			if ($iIdentify == 1) {
				return true;
			}
		}
		$this -> logout(false);
		return false;
	}

	public function logout($bRedirect = true) {

		session_destroy();

		if ($bRedirect) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}
	}

	public function forgotPassword($aAccount) {
		if (isset($aAccount['username'])) {
			$oUser = $this -> fetchRow($this -> select() -> where('username="' . $aAccount['username'] . '" AND activated=1', array('activation')));
			if (is_object($oUser)) {
				$sKeyNopw = md5(md5($this -> oSettings -> user -> security -> salt) . md5($oUser -> activation));
				$this -> update(array('activation' => $sKeyNopw), 'username="' . $oUser -> username . '"');

				$oResetPwMail = new Zend_Mail('UTF-8');
				$oSmarty = Application_Model_Smarty::getInstance();
				$oSmarty -> assign('SM_STR_USERNAME', $oUser -> username);
				$oSmarty -> assign('SM_STR_RESET', $this -> oSettings -> website -> site -> basehref . '/account/resetpw/?reset=' . $sKeyNopw);
				$oSmarty -> assign('SM_STR_MAILCONTENT', $oSmarty -> fetch(TPL_MAIL_NOPW));

				$oResetPwMail -> setBodyHtml($oSmarty -> fetch(TPL_MAIL));
				$oResetPwMail -> setFrom($this -> oSettings -> website -> mail -> confirmation, 'appstack.io');
				$oResetPwMail -> addTo($oUser -> mail);
				$oResetPwMail -> setSubject('Your password reset request');

				if ($oResetPwMail -> send()) {
					return array('error' => false, 'message' => 'confirmation was sent.');
				}
			}
		}
		return array('error' => false);
	}

	public function resetPassword($aReset) {
		if (strlen($aReset['reset']) == 32) {
			$oUser = $this -> fetchRow($this -> select() -> where('activation="' . $aReset['reset'] . '" AND activated=1'));
			if (is_object($oUser)) {
				$sNewPw = substr(md5(time() . $this -> oSettings -> user -> security -> salt), 24);
				$sNewHash = md5(md5($this -> oSettings -> user -> security -> salt) . md5($sNewPw));
				$this -> update(array('password' => $sNewHash), 'username="' . $oUser -> username . '" AND activated=1');

				$oNewpwMail = new Zend_Mail('UTF-8');
				$oSmarty = Application_Model_Smarty::getInstance();
				$oSmarty -> assign('SM_STR_NEWPW', $sNewPw);
				$oSmarty -> assign('SM_STR_MAILCONTENT', $oSmarty -> fetch(TPL_MAIL_RESETPW));

				$oNewpwMail -> setBodyHtml($oSmarty -> fetch(TPL_MAIL));
				$oNewpwMail -> setFrom($this -> oSettings -> website -> mail -> confirmation, 'appstack.io');
				$oNewpwMail -> addTo($oUser -> mail);
				$oNewpwMail -> setSubject('Your password has been reseted');

				if ($oNewpwMail -> send()) {
					return array('error' => false, 'message' => 'A confirmation mail was sent.');
				}
			}
		}
	}

	public function change($sUserName, $aChangeData) {
		$sMainMessage = '';
		if (isset($aChangeData['ticket']) && strlen($aChangeData['ticket']) == 32) {
			if (strlen($aChangeData['pwa']) >= 6) {
				if ($aChangeData['pwa'] === $aChangeData['pwb']) {
					$sNewPasswd = md5(md5($this -> oSettings -> user -> security -> salt) . md5($aChangeData['pwa']));
				} else {
					$sMainMessage = Preprocessor_String::createTag('h3', false, 'Your password didn\'t match');
				}
			} else {
				if (strlen($aChangeData['pwa']) != 0) {
					$sMainMessage = Preprocessor_String::createTag('h3', false, 'Password is to short');
				}
			}

			if ($aChangeData['maila'] === $aChangeData['mailb'] && strlen($sMainMessage) == 0) {

				$sNewMail = $aChangeData['maila'];
				$sMainMessage = Preprocessor_String::createTag('h3', false, 'Cant store changes');

				if (isset($sNewMail)) {
					$sUpdateKey = md5(md5($this -> oSettings -> user -> security -> salt) . md5($aChangeData['ticket']));
					$aUpdateAccount = array('countries_idcountry' => $aChangeData['country'], 'mail' => $sNewMail, 'address' => $aChangeData['address'], 'city' => $aChangeData['city'], 'phone' => $aChangeData['phone'], 'company' => $aChangeData['company']);

					if (isset($sNewPasswd)) {
						$aUpdateAccount['password'] = $sNewPasswd;
					}
					if ($this -> update($aUpdateAccount, 'username="' . $sUserName . '" AND activated=1 AND activation="' . $aChangeData['ticket'] . '"')) {
						$this -> update(array('activation' => $sUpdateKey), 'username="' . $sUserName . '"');
						$bFinal = true;
						$sMainMessage = true;
					} else {
						$sMainMessage = Preprocessor_String::createTag('h3', false, 'No chages made');
					}
				}
			} else {
				if (strlen($sMainMessage) == 0) {
					$sMainMessage = Preprocessor_String::createTag('h3', false, 'Your e-mail didn\'t match');
				}
			}
		} else {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}
		return $sMainMessage;
	}

	public function createCountrys() {
		$oCache = new Application_Model_Cache('countrylist');

		if (!$oCache -> cacheExists()) {
			if ($this -> oCountrys -> fetchAll() -> count() === 0) {
				$Text = utf8_encode(file_get_contents(BASE_PATH . '/countrys.txt'));
				$aCountrylist = explode("\n", $Text);

				foreach ($aCountrylist as $iCountry => $sCountryLine) {
					$aCountrylist[$iCountry] = explode("	", $sCountryLine);
				}

				unset($aCountrylist[0]);

				foreach ($aCountrylist as $iNumCountry => $aCountry) {
					if (isset($aCountry[3])) {
						$this -> oCountrys -> insert(array('name' => $aCountry[3], 'tld' => (($aCountry[2] != '-') ? $aCountry[2] : ''), 'prefix' => $aCountry[1]));
					}
				}
				echo 'Installed Countrys. Please refresh Page.';
				exit ;
			}
		}
	}

	public function status() {

		$oAccount = Application_Model_Session::getInstance('ACCOUNT');

		if (!isset($oAccount -> userdata['USERNAME'])) {
			$aResponse = array('error' => true, 'message' => 'Login required');
		} else {
			$oSmarty = Application_Model_Smarty::getInstance();
			$oSmarty -> assign('SM_STR_USERNAME', $oAccount -> userdata['USERNAME']);
			$oSmarty -> assign('SM_STR_BASEHREF', $this -> oSettings -> website -> site -> basehref);
			$aResponse = array('error' => false, 'message' => false, 'expires' => $oAccount -> expires, 'widget' => str_replace(array("\n", "\n", '  ', "	"), '', $oSmarty -> fetch(TPL_WIDGET_USERPANEL)));
		}
		return $aResponse;
	}

	public function getAll() {
		$oAccount = Application_Model_Session::getInstance('ACCOUNT');
		if (isset($oAccount -> userdata['UID'])) {

			$iAccountId = $oAccount -> userdata['UID'];
			$oSelect = $this -> fetchAll($this -> select() -> where('fk_account IN(' . $iAccountId . ')'));
			$iInitCount = 0;

			while ($oSelect -> count() > $iInitCount) {

				foreach ($oSelect as $oSubAccount) {
					$iAccountId .= ',' . $oSubAccount -> idaccount;
				}

				$iInitCount = $oSelect -> count();
				$oSelect = $this -> fetchAll($this -> select() -> where('fk_account IN(' . $iAccountId . ')'));
			}
			return $oSelect;
		}
		return false;
	}

	public function getMySubAccounts($bGetAll = false) {

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
					$sSubExpr = " AND username LIKE '" . strtolower($oAccount -> userdata['FILTERLETTER']) . "%'";
				}
			} else {

				if (isset($_GET['filter_expr']) && strlen($_GET['filter_expr']) > 1) {
					$oAccount -> userdata['FILTERLETTER'] = $_GET['filter_expr'];
					$sSubExpr = " AND username LIKE '" . strtolower(Preprocessor_String::filterBadChars($_GET['filter_expr'])) . "%'";
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
			$iSumCount = $this -> fetchAll($this -> select() -> where('fk_account=' . $oAccount -> userdata['UID'] . ' AND idaccount!=' . $iAccountId . $sSubExpr)) -> count();
			$oAccount -> userdata['SUBACCOUNTS'] = $iSumCount;

			if ($bGetAll) {
				$oSelect = $this -> select() -> setIntegrityCheck(false) -> from($this -> getTable()) -> where('fk_account IN(' . $iAccountId . ') AND idaccount!=' . $iAccountId) -> order('username');
			} else {
				$oSelect = $this -> select() -> setIntegrityCheck(false) -> from($this -> getTable()) -> where('fk_account IN(' . $iAccountId . ') AND idaccount!=' . $iAccountId) -> order('username') -> limit($oAccount -> userdata['LIMITRESULT'], $oAccount -> userdata['LISTINDEX']);
			}
			$oResult = $this -> fetchAll($oSelect);

			return $oResult;
		}
		return false;
	}

	public function getMyBudget() {
		$oAccount = Application_Model_Session::getInstance('ACCOUNT');
		if (isset($oAccount -> userdata['UID'])) {
			$oAccountrate = new Application_Model_Table();
			$oAccountrate -> setTable('accountrates');
			$oAccountrate -> setPrimary('accounts_idaccount');

			$iAccountId = $oAccount -> userdata['UID'];

			$oSelect = $oAccountrate -> select() -> setIntegrityCheck(false) -> from($oAccountrate -> getTable(), array('maxapiget', 'maxapiput', 'maxmetas', 'maxcats', 'maxcomps', 'maxnamespaces', 'maxgroups', 'maxusers', 'maxbrowserkeys', 'maxserverkeys')) -> join('usages', $oAccountrate -> getPrimary() . '=accountrates_accounts_idaccount', array('useapiget' => 'usages.maxapiget', 'useapiput' => 'usages.maxapiput', 'usemetas' => 'usages.maxmetas', 'usecats' => 'usages.maxcats', 'usecomps' => 'usages.maxcomps', 'usegroups' => 'usages.maxgroups', 'useusers' => 'usages.maxusers', 'usenamespaces' => 'usages.maxnamespaces', 'usebrowserkeys' => 'usages.maxbrowserkeys', 'useserverkeys' => 'usages.maxserverkeys')) -> where($oAccountrate -> getPrimary() . '=' . $iAccountId);

			$oResult = $oAccountrate -> fetchRow($oSelect);

			//return array('HASAPIGET' => $oResult -> maxapiget - $oResult -> useapiget, 'HASAPIPUT' => $oResult -> maxapiput - $oResult -> useapiput, 'HASMETAS' => $oResult -> maxmetas - $oResult -> usemetas, 'HASCATS' => $oResult -> maxcats - $oResult -> usecats, 'HASCOMPS' => $oResult -> maxcomps - $oResult -> usecomps, 'HASNAMESPACES' => $oResult -> maxnamespaces - $oResult -> usenamespaces, 'HASGROUPS' => $oResult -> maxgroups - $oResult -> usegroups, 'HASUSERS' => $oResult -> maxusers - $oResult -> useusers);
			return array('HASAPIGET' => $oResult -> maxapiget - $oResult -> useapiget, 'HASAPIPUT' => $oResult -> maxapiput - $oResult -> useapiput, 'HASMETAS' => $oResult -> maxmetas - $oResult -> usemetas, 'HASCATS' => $oResult -> maxcats - $oResult -> usecats, 'HASCOMPS' => $oResult -> maxcomps - $oResult -> usecomps, 'HASNAMESPACES' => $oResult -> maxnamespaces - $oResult -> usenamespaces, 'HASGROUPS' => $oResult -> maxgroups - $oResult -> usegroups, 'HASUSERS' => $oResult -> maxusers - $oResult -> useusers, 'HASBROWSERKEYS' => $oResult -> maxbrowserkeys - $oResult -> usebrowserkeys, 'HASSERVERKEYS' => $oResult -> maxserverkeys - $oResult -> useserverkeys);
		}
		return false;
	}

	public function getHisBudget($iAccountId, $iOwnerID) {
		if ((int)$iAccountId > 0) {
			$oAccount = Application_Model_Session::getInstance('ACCOUNT');
			if (isset($oAccount -> userdata['UID']) && $oAccount -> userdata['UID'] == $iOwnerID) {
				$oAccountrate = new Application_Model_Table();
				$oAccountrate -> setTable('accountrates');
				$oAccountrate -> setPrimary('accounts_idaccount');

				//$iAccountOwner = $oAccount -> userdata['UID'];

				$oSelect = $oAccountrate -> select() -> setIntegrityCheck(false) -> from($oAccountrate -> getTable(), array('maxapiget', 'maxapiput', 'maxmetas', 'maxcats', 'maxcomps', 'maxnamespaces', 'maxgroups', 'maxusers', 'maxbrowserkeys', 'maxserverkeys')) -> join('accounts', 'fk_account=' . $iOwnerID . ' AND idaccount=' . $iAccountId, false) -> join('usages', $oAccountrate -> getPrimary() . '=accountrates_accounts_idaccount', array('useapiget' => 'usages.maxapiget', 'useapiput' => 'usages.maxapiput', 'usemetas' => 'usages.maxmetas', 'usecats' => 'usages.maxcats', 'usecomps' => 'usages.maxcomps', 'usegroups' => 'usages.maxgroups', 'useusers' => 'usages.maxusers', 'usenamespaces' => 'usages.maxnamespaces', 'usebrowserkeys' => 'usages.maxbrowserkeys', 'useserverkeys' => 'usages.maxserverkeys')) -> where($oAccountrate -> getPrimary() . '=' . $iAccountId);
				$oResult = $oAccountrate -> fetchRow($oSelect);
				if (is_object($oResult)) {
					return array('HASAPIGET' => $oResult -> maxapiget - $oResult -> useapiget, 'HASAPIPUT' => $oResult -> maxapiput - $oResult -> useapiput, 'HASMETAS' => $oResult -> maxmetas - $oResult -> usemetas, 'HASCATS' => $oResult -> maxcats - $oResult -> usecats, 'HASCOMPS' => $oResult -> maxcomps - $oResult -> usecomps, 'HASNAMESPACES' => $oResult -> maxnamespaces - $oResult -> usenamespaces, 'HASGROUPS' => $oResult -> maxgroups - $oResult -> usegroups, 'HASUSERS' => $oResult -> maxusers - $oResult -> useusers, 'HASBROWSERKEYS' => $oResult -> maxbrowserkeys - $oResult -> usebrowserkeys, 'HASSERVERKEYS' => $oResult -> maxserverkeys - $oResult -> useserverkeys);
				}
			}
		}
		return false;
	}

	public function transfer($aNewRates, $iOwnerId) {
		if (isset($aNewRates['account']) && (int)$aNewRates['account'] > 0) {
			$oAccountrate = new Application_Model_Table();
			$oAccountrate -> setTable('accountrates');
			$oAccountrate -> setPrimary('accounts_idaccount');
			$aMyBudget = $this -> getMyBudget();
			$aMyUsage = $this -> getUsage($iOwnerId);
			$aHisUsage = $this -> getUsage($aNewRates['account']);

			$aHisBudget = $this -> getHisBudget($aNewRates['account'], $iOwnerId);
			if (is_array($aHisBudget)) {
				$aNewBudgets = array('mine' => array(), 'his' => array());

				foreach ($oAccountrate->getCols() as $iColnum => $aCurcol) {
					$sReqKey = strtoupper(preg_replace('/max/', '', $aCurcol['FIELDNAME']));

					if (isset($aNewRates[$sReqKey]) && isset($aMyBudget['HAS' . $sReqKey]) && isset($aHisBudget['HAS' . $sReqKey])) {
						if ($aNewRates[$sReqKey]['mine'] != $aMyBudget['HAS' . $sReqKey]) {
							if ($aNewRates[$sReqKey]['mine'] > $aMyBudget['HAS' . $sReqKey]) {

								$iTakeFrom = $aNewRates[$sReqKey]['mine'] - $aMyBudget['HAS' . $sReqKey];

								if (($aHisBudget['HAS' . $sReqKey] - $iTakeFrom) > -1) {
									$aNewBudgets['mine'][$aCurcol['FIELDNAME']] = $aMyBudget['HAS' . $sReqKey] + $iTakeFrom + $aMyUsage[$aCurcol['FIELDNAME']];
									$aNewBudgets['his'][$aCurcol['FIELDNAME']] = $aHisBudget['HAS' . $sReqKey] - $iTakeFrom + $aHisUsage[$aCurcol['FIELDNAME']];
								} else {
									$aNewBudgets['mine'][$aCurcol['FIELDNAME']] = $aMyBudget['HAS' . $sReqKey] + $aHisBudget['HAS' . $sReqKey] + $aMyUsage[$aCurcol['FIELDNAME']];
									$aNewBudgets['his'][$aCurcol['FIELDNAME']] = $aHisBudget['HAS' . $sReqKey] - $aHisBudget['HAS' . $sReqKey] + $aHisUsage[$aCurcol['FIELDNAME']];
								}
							}
							if ($aNewRates[$sReqKey]['mine'] < $aMyBudget['HAS' . $sReqKey] && $aNewRates[$sReqKey]['mine'] > -1) {
								$iHisAdd = ($aMyBudget['HAS' . $sReqKey] - $aNewRates[$sReqKey]['mine']);
								$iMineNew = $aMyBudget['HAS' . $sReqKey] - $iHisAdd;
								$aNewBudgets['mine'][$aCurcol['FIELDNAME']] = $iMineNew + $aMyUsage[$aCurcol['FIELDNAME']];
								$aNewBudgets['his'][$aCurcol['FIELDNAME']] = $aHisBudget['HAS' . $sReqKey] + $iHisAdd + $aHisUsage[$aCurcol['FIELDNAME']];
							}
						}
					}
				}

				if (count($aNewBudgets['mine']) > 0) {
					$oAccountrate -> update($aNewBudgets['mine'], $oAccountrate -> getPrimary() . '=' . $iOwnerId);
					$oAccountrate -> update($aNewBudgets['his'], $oAccountrate -> getPrimary() . '=' . $aNewRates['account']);
					return array('success' => true);
				} else {
					return array('message' => 'no change made');
				}
			} else {
				return array('error' => 'Bad request');
			}
		}
		return array('error' => 'No user specified');
	}

}
?>