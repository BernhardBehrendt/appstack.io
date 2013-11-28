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
define('TPL_TRANSFERMASK', 'website.user.transfermask.html');
class SubaccountController extends Zend_Controller_Action {
	private $oCache;
	private $oSettings;
	private $oAccounts;
	private $oAccount;

	public function init() {

		/* Initialize action controller here */
		$this -> oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
		$this -> oAccounts = new Application_Model_Account($this -> oSettings);
		$this -> oAccount = Application_Model_Session::getInstance('ACCOUNT');
	}

	public function indexAction() {
		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}

		$oPage = new Application_Model_Page();
		$oApstack = new Application_Model_Appstackapi();
		$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
		$oCache = new Application_Model_Cache('subaccount_navigations');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
			$oCache -> writeCache(serialize($aNavigations));
		}

		$aNavigations = unserialize($oCache -> readCache());

		$oCache -> setCache('subaccount_accountadmin');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$sAccountAdmin = $oPage -> getNavi('subaccount', 'accountadmin', 'boxnavi', false, false, array('overview' => 'subaccount/index', 'create' => 'subaccount/create', 'edit' => 'subaccount/edit', 'transfer' => 'subaccount/transfer'));
			$oCache -> writeCache($sAccountAdmin);
		}

		$sAccountAdmin = $oCache -> readCache();

		array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));

		$aUsage = $this -> oAccounts -> getUsage($this -> oAccount -> userdata['UID']);
		$sContent = '';
		$aRate = $this -> oAccounts -> getRate($this -> oAccount -> userdata['UID']);

		foreach ($aUsage as $sColumnName => $iValue) {

			$iSum = $aRate[$sColumnName];

			if ($iSum != -1 && $iSum != 0 && $sColumnName == 'maxusers') {
				$iUsage = floor(($iValue * 100) / $iSum);
				$iAvail = 100 - $iUsage;
				$sLabel = ucfirst(str_replace('max', '', $sColumnName));
				$sHtmlStat = '<span class="apst_pink apst_mid">' . $iValue . ' of ' . $iSum . '</span>' . '<br/>' . $sLabel;

				$sContent .= $oPage -> pieChart($sHtmlStat, array('Available' => $iAvail, 'Created' => $iUsage), 300, 0, array('0e8cf6', '1742cc', 'de057e'), '282828');
			}

		}

		array_push($aBoxes['CONTENT'], $oPage -> getBox($sContent, 'c1', 'content_box'));
		array_push($aBoxes['RIGHT'], $oPage -> getBox('z', 'r1', 'teaser_box'));

		$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');

	}

	public function transferAction() {

		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}

		$oPage = new Application_Model_Page();
		$oApstack = new Application_Model_Appstackapi();
		$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
		$oCache = new Application_Model_Cache('subaccount_navigations');
		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
			$oCache -> writeCache(serialize($aNavigations));
		}

		$aNavigations = unserialize($oCache -> readCache());

		$oCache -> setCache('subaccount_accountadmin');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$sAccountAdmin = $oPage -> getNavi('subaccount', 'accountadmin', 'boxnavi', false, false, array('overview' => 'subaccount/index', 'create' => 'subaccount/create', 'edit' => 'subaccount/edit', 'transfer' => 'subaccount/transfer'));
			$oCache -> writeCache($sAccountAdmin);
		}

		$sAccountAdmin = $oCache -> readCache();

		array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));

		$sSubExpr = false;

		$oSubaccounts = $this -> oAccounts -> getMySubAccounts();

		$sContent = '';
		$aUsers = array();

		foreach ($oSubaccounts as $oSubAccount) {
		
			$aUser = array('UID' => $oSubAccount -> idaccount, 'NAME' => $oSubAccount -> username, 'MAIL' => $oSubAccount -> mail, 'CREATED' => $oSubAccount -> regdate, 'LASTLOGIN' => (!empty($oSubAccount -> lastlogin) ? $oSubAccount -> lastlogin : 'Never'), 'TRANSFER' => true);
			array_push($aUsers, $aUser);
		}

		$sContent = $oPage -> getUserTable('subaccount/transfer/', $aUsers, $this -> oAccount -> userdata['SUBACCOUNTS'], $this -> oAccount -> userdata['LIMITRESULT'], $this -> oAccount -> userdata['LISTINDEX']);

		array_push($aBoxes['CONTENT'], $oPage -> getBox($sContent, 'c1', 'content_box'));
		array_push($aBoxes['RIGHT'], $oPage -> getBox($oPage -> getFiltes('subaccount/transfer/', $this -> oAccount -> userdata['LIMITRESULT'], ((isset($this -> oAccount -> userdata['FILTERLETTER']) ? $this -> oAccount -> userdata['FILTERLETTER'] : '*'))), 'r1', 'teaser_box'));

		$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'transfer.js');

	}

	public function createAction() {

		$iAccountUsage = $this -> oAccounts -> getUsage($this -> oAccount -> userdata['UID']);
		$iAccountUsage = $iAccountUsage['maxusers'];

		$iAccountRate = $this -> oAccounts -> getRate($this -> oAccount -> userdata['UID']);
		$iAccountRate = $iAccountRate['maxusers'];

		if (isset($this -> oAccount -> userdata['USERNAME'])) {

			$oAccountData = $this -> oAccounts -> fetchRow($this -> oAccounts -> select() -> where('username="' . $this -> oAccount -> userdata['USERNAME'] . '" AND activated = 1'));

			$oCache = new Application_Model_Cache('countrylist');

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
				$oCountrys = new Application_Model_Table();
				$oCountrys -> setTable('countries');
				$oCountrys -> setPrimary('idcountry');

				$aCountryList = array();
				foreach ($oCountrys->fetchAll () as $aCountry) {
					$aCountryList[$aCountry -> idcountry] = $aCountry -> name;

				}
				$oCache -> writeCache(serialize($aCountryList));
			}

			$aCountryList = unserialize($oCache -> readCache());

			// Define Fields
			$oRegForm = new Zend_Form();
			$view = $oRegForm -> getView();
			$view -> doctype('XHTML1_TRANSITIONAL');
			$oUser = new Zend_Form_Element_Text('user');
			$oPwa = new Zend_Form_Element_Password('pwa');
			$oPwb = new Zend_Form_Element_Password('pwb');
			$oMaila = new Zend_Form_Element_Text('maila');
			$oMailb = new Zend_Form_Element_Text('mailb');
			$oOrga = new Zend_Form_Element_Text('company');
			$oPhone = new Zend_Form_Element_Text('phone');
			$oAddress = new Zend_Form_Element_Text('address');
			$oCity = new Zend_Form_Element_Text('city');
			$oCountry = new Zend_Form_Element_Select('country');
			$oSubmit = new Zend_Form_Element_Submit('Register');
			$oReset = new Zend_Form_Element_Reset('Reset');

			$oUser -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oPwa -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oPwb -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oMaila -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oMailb -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oOrga -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oPhone -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oAddress -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oCity -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));
			$oCountry -> addDecorators(array('ViewHelper', 'Errors', array('HtmlTag', array('tag' => 'dt', 'class' => 'clearfix')), array('Label', array('tag' => 'dd', 'class' => ''))));

			$oUser -> setLabel('Username *');
			$oPwa -> setLabel('Password *');
			$oPwb -> setLabel('Repeat password *');
			$oMaila -> setLabel('E-mail address *');
			$oMailb -> setLabel('Repeat E-mail address *');
			$oOrga -> setLabel('Organisation');
			$oPhone -> setLabel('Phone');
			$oAddress -> setLabel('Address');
			$oCity -> setLabel('City');
			$oCountry -> setLabel('Country');
			$oCountry -> setMultiOptions($aCountryList);
			// Add Validators
			$oUser -> addValidator('stringLength', false, array(6, 20)) -> addValidator('regex', false, array('/^[a-z]+/')) -> setRequired(true) -> addFilter('StringToLower');
			$oPwa -> addValidator('StringLength', false, array(6)) -> setRequired(true);
			$oPwb -> addValidator('StringLength', false, array(6)) -> setRequired(true);
			$oMaila -> addValidator('stringLength', false, array(7, 50)) -> addValidator(new Zend_Validate_EmailAddress()) -> setRequired(true) -> addFilter('StringToLower');
			$oMailb -> addValidator('stringLength', false, array(7, 50)) -> addValidator(new Zend_Validate_EmailAddress()) -> setRequired(true) -> addFilter('StringToLower');
			$oOrga -> addValidator('stringLength', false, array(0, 50)) -> addValidator('regex', false, array('/^[a-zA-Z0-9]+/'));
			$oPhone -> addValidator('stringLength', false, array(0, 25)) -> addValidator('regex', false, array('/^[0-9]+/'));
			$oCity -> addValidator('stringLength', false, array(0, 50)) -> addValidator('regex', false, array('/^[a-zA-Z0-9]/'));
			// Assign Input elements in form
			$oRegForm -> setAction('subaccount/create') -> setMethod('POST') -> setAttrib('id', 'registerform');
			$oRegForm -> addDecorator('HtmlTag', array('tag' => 'dl', 'class' => 'clearfix'));
			$oRegForm -> addElement($oUser);
			$oRegForm -> addElement($oPwa);
			$oRegForm -> addElement($oPwb);
			$oRegForm -> addElement($oMaila);
			$oRegForm -> addElement($oMailb);
			$oRegForm -> addElement($oOrga);
			$oRegForm -> addElement($oPhone);
			$oRegForm -> addElement($oAddress);
			$oRegForm -> addElement($oCity);
			$oRegForm -> addElement($oCountry);
			$oRegForm -> addElement($oSubmit);
			$oRegForm -> addElement($oReset);
			$sMainMessage = '';

			if (isset($_POST["user"])) {
				if ($oRegForm -> isValid($_POST)) {

					$sMainMessage = $this -> oAccounts -> create($_POST, $this -> oAccount);
					if ($sMainMessage === true) {
						$bFinal = true;
						$sMainMessage = '<h3>We sent a mail for confirmation.</h3>';
					}
				}
			}

			$oOrga -> setValue($oAccountData -> company);
			$oAddress -> setValue($oAccountData -> address);
			$oCity -> setValue($oAccountData -> city);
			$oRegForm -> setDefault('country', $oAccountData -> countries_idcountry);

			$aNavigations = array();
			if ($iAccountUsage == $iAccountRate && !isset($bFinal)) {
				unset($oRegForm);
				$bFinal = false;
			}

			$oPage = new Application_Model_Page();
			$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
			$oCache = new Application_Model_Cache('subaccount_navigations');
			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

				$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
				$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
				$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');

				$oCache -> writeCache(serialize($aNavigations));
			}

			$aNavigations = unserialize($oCache -> readCache());

			$oCache -> setCache('subaccount_accountadmin');

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
				$sAccountAdmin = $oPage -> getNavi('subaccount', 'accountadmin', 'boxnavi', false, false, array('overview' => 'subaccount/index', 'create' => 'subaccount/create', 'edit' => 'subaccount/edit', 'transfer' => 'subaccount/transfer'));
				$oCache -> writeCache($sAccountAdmin);
			}

			$sAccountAdmin = $oCache -> readCache();
			array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));

			if (isset($oRegForm)) {
				array_push($aBoxes['CONTENT'], $oPage -> getBox($sMainMessage . ((!isset($bFinal)) ? $oRegForm -> render() : ''), 'c1', 'content_box'));
			} else {
				array_push($aBoxes['CONTENT'], $oPage -> getBox(Preprocessor_String::createTag('h2', false, 'There is no budget for creating new users') . Preprocessor_String::createTag('h3', false, 'Maximum ' . $iAccountRate . ' subaccounts are allowed for your account'), 'c1', 'content_box'));
			}

			array_push($aBoxes['RIGHT'], $oPage -> getBox('z', 'r1', 'teaser_box'));

			$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');
		} else {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
		}
	}

	public function transfermaskAction() {
		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}

		$oSmarty = Application_Model_Smarty::getInstance();

		if (!$this -> oAccounts -> checkLogin()) {
			$this -> view -> sPage = Preprocessor_String::createTag('script', array('type' => 'text/javascript'), 'alert("It seems there is somone else online with your Account");window.location.href = "' . $this -> oSettings -> website -> site -> basehref . '";');
		} else {

			$oSubAccounts = $this -> oAccounts -> getMySubAccounts(true);
			if (!is_object($oSubAccounts)) {
				sleep(3);
				exit ;
			}
			foreach ($oSubAccounts as $oSubAccount) {
				if ($oSubAccount -> idaccount == $_GET['account']) {
					$aSubAccountRequested = $this -> oAccounts -> getHisBudget($oSubAccount -> idaccount, $oSubAccount -> fk_account);
					break;
				}
			}
			if (isset($aSubAccountRequested) && is_array($aSubAccountRequested)) {
				$oSmarty -> assign('SM_ARR_MYBUDGET', $this -> oAccounts -> getMyBudget());
				$oSmarty -> assign('SM_ARR_HISBUDGET', $aSubAccountRequested);
				$this -> view -> sPage = $oSmarty -> fetch(TPL_TRANSFERMASK);
			} else {
				sleep(3);
				exit ;
			}
		}
	}

	public function transferdoAction() {
		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}

		$aResponse = $this -> oAccounts -> transfer($_GET, $this -> oAccount -> userdata['UID']);
		Preprocessor_Header::setContentType('json');
		$this -> view -> sPage = json_encode($aResponse);
	}

}
?>