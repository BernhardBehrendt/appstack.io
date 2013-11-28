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
class AccountController extends Zend_Controller_Action {
	private $oCache;
	private $oSettings;
	private $oAccounts;
	private $oAccount;
	public function init() {
		/*
		 * Initialize action controller here
		 */
		$this->oSettings = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', 'production' );
		$this->oAccounts = new Application_Model_Account ( $this->oSettings );
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
		$oCache = new Application_Model_Cache ( 'account_navigations' );
		if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
			$aNavigations ['HEADNAV'] = $oPage->getNavi ( 'navi_top', 'top_nav', 'apst_small' );
			$aNavigations ['MAINNAV'] = $oPage->getNavi ( 'navi_main', 'main_nav', '' );
			$aNavigations ['FOOTERNAV'] = $oPage->getNavi ( 'navi_bottom', 'footer', 'clearfix apst_small', ' | ' );
			
			$oCache->writeCache ( serialize ( $aNavigations ) );
		}
		
		$aNavigations = unserialize ( $oCache->readCache () );
		
		$oCache->setCache ( 'account_accountadmin' );
		if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
			$sAccountAdmin = $oPage->getNavi ( 'account', 'accountadmin', 'boxnavi', false, false, array ('overview' => 'account/index', 'change' => 'account/change', 'logout' => 'account/logout' ) );
			$oCache->writeCache ( $sAccountAdmin );
		}
		
		$sAccountAdmin = $oCache->readCache ();
		array_push ( $aBoxes ['LEFT'], $oPage->getBox ( $sAccountAdmin, 'l1', 'teaser_box' ) );
		
		$aUsage = $this->oAccounts->getUsage ( $this->oAccount->userdata ['UID'] );
		$aAccountrates = $this->oAccounts->getRate ( $this->oAccount->userdata ['UID'] );
		$sContent = '';
		$sStats = '';
		
		$oStats = new Application_Model_Table ();
		$oStats->setTable ( 'stats' );
		$oStats->setPrimary ( 'accounts_idaccount' );
		
		foreach ( $aUsage as $sColumnName => $iValue ) {
			
			if ($sColumnName == 'maxapiget' || $sColumnName == 'maxapiput') {
				unset ( $iDateBefore );
				
				if (! isset ( $oMyStats )) {
					$iSecondsDay = 86400;
					$iToday = mktime ( 0, 0, 0, date ( 'n' ), date ( 'j' ), date ( 'Y' ) ) + $iSecondsDay;
					$iFirstDay = $iToday;
					// If current Day is not sundy calculate last syndays
					// timestamp by vuttent offset;
					$iFirstDay = $iToday - (10 * $iSecondsDay);
					$oSelect = $oStats->select ()->where ( $oStats->getPrimary () . '=' . $this->oAccount->userdata ['UID'] . ' AND timestamp BETWEEN FROM_UNIXTIME(' . $iFirstDay . ') AND FROM_UNIXTIME(' . $iToday . ')' )->order ( 'timestamp' );
					$oMyStats = $oStats->fetchAll ( $oSelect );
				}
				
				$aLabels = array ();
				$aData = array ();
				if ($oMyStats->count () > 0) {
					$bHasValue = false;
					foreach ( $oMyStats as $oRow ) {
						
						$aDateTime = explode ( ' ', $oRow->timestamp );
						$aClockTime = explode ( ':', $aDateTime [1] );
						$sDate = explode ( '-', $aDateTime [0] );
						
						if (! isset ( $iDateBefore ) || $iDateBefore < $sDate [2]) {
							array_push ( $aLabels, "'" . $sDate [2] . '.' . $sDate [1] . "'" );
							if ($sColumnName == 'maxapiget') {
								if (! $bHasValue && $oRow->apiget > 0) {
									$bHasValue = true;
								}
								array_push ( $aData, $oRow->apiget );
							}
							if ($sColumnName == 'maxapiput') {
								if (! $bHasValue && $oRow->apiput > 0) {
									$bHasValue = true;
								}
								array_push ( $aData, $oRow->apiput );
							}
							$iDateBefore = $sDate [2];
						} else {
							array_push ( $aLabels, "'" . $aClockTime [0] . ':00' . "'" );
							if ($sColumnName == 'maxapiget') {
								if (! $bHasValue && $oRow->apiget > 0) {
									$bHasValue = true;
								}
								array_push ( $aData, $oRow->apiget );
							}
							if ($sColumnName == 'maxapiput') {
								if (! $bHasValue && $oRow->apiput > 0) {
									$bHasValue = true;
								}
								array_push ( $aData, $oRow->apiput );
							}
						}
					}
					if (count ( $aData ) > 1 && $bHasValue) {
						$dHue = ($sColumnName == 'maxapiget') ? .58 : .91;
						$aChartConf = array ('hue' => $dHue, 'saturation' => 0.9, 'brightness' => 1, 'width' => 515, 'height' => 200, 'labels' => implode ( ',', $aLabels ), 'data' => implode ( ',', $aData ), 'unit' => '', 'popuptext' => (($sColumnName == 'maxapiget') ? ' Get' : ' Put') );
						
						$oPage->lineChart ( '.content_box', $sColumnName, $aChartConf );
					}
				}
			}
			
			if ($sColumnName != $this->oAccounts->getPrimary ()) {
				
				$iSum = $aAccountrates [$sColumnName];
				
				if ($iSum != - 1 && $iSum != 0) {
					$iUsage = floor ( ($iValue * 100) / $iSum );
					$iAvail = 100 - $iUsage;
					$sLabel = ucfirst ( str_replace ( 'max', '', $sColumnName ) );
					$sHtmlStat = '<span class="apst_pink apst_mid">' . $iValue . ' / ' . $iSum . '</span>' . '<br/>' . $sLabel;
					
					$sContent .= $oPage->pieChart ( $sHtmlStat, array ('Available' => $iAvail, 'Created' => $iUsage ), 300, 0, array ('0e8cf6', '1742cc', 'de057e' ), '282828' );
				}
			}
		
		}
		
		if (strlen ( $sStats ) == 0 && strlen ( $sContent ) == 0) {
			$sContent = '<h3>There isn\'t any budget for your Account</h3>';
		}
		array_push ( $aBoxes ['CONTENT'], $oPage->getBox ( $sStats . $sContent, 'c1', 'content_box' ) );
		array_push ( $aBoxes ['RIGHT'], $oPage->getBox ( 'z', 'r1', 'teaser_box' ) );
		
		$this->view->sPage = $oPage->getPage ( 'content', $aNavigations, $aBoxes, 'account.js' );
	}
	
	public function changeAction() {
		
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		
		$sUserName = $this->oAccount->userdata ['USERNAME'];
		
		$oAccountData = $this->oAccounts->fetchRow ( $this->oAccounts->select ()->where ( 'username="' . $sUserName . '" AND activated = 1' ) );
		
		$this->oCache = new Application_Model_Cache ( md5 ( 'index/content' ) );
		
		$oCache = new Application_Model_Cache ( 'countrylist' );
		
		if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
			$oCountrys = new Application_Model_Table ();
			$oCountrys->setTable ( 'countries' );
			$oCountrys->setPrimary ( 'idcountry' );
			
			$aCountryList = array ();
			foreach ( $oCountrys->fetchAll () as $aCountry ) {
				$aCountryList [$aCountry->idcountry] = $aCountry->name;
			
			}
			$oCache->writeCache ( serialize ( $aCountryList ) );
		}
		
		$aCountryList = unserialize ( $oCache->readCache () );
		
		// Define Fields
		$oRegForm = new Zend_Form ();
		$view = $oRegForm->getView ();
		$view->doctype ( 'XHTML1_TRANSITIONAL' );
		$oMaila = new Zend_Form_Element_Text ( 'maila' );
		$oMailb = new Zend_Form_Element_Text ( 'mailb' );
		$oOrga = new Zend_Form_Element_Text ( 'company' );
		$oPhone = new Zend_Form_Element_Text ( 'phone' );
		$oAddress = new Zend_Form_Element_Text ( 'address' );
		$oCity = new Zend_Form_Element_Text ( 'city' );
		$oCountry = new Zend_Form_Element_Select ( 'country' );
		$oPwa = new Zend_Form_Element_Password ( 'pwa' );
		$oPwb = new Zend_Form_Element_Password ( 'pwb' );
		$oHidden = new Zend_Form_Element_Hidden ( 'ticket' );
		$oSubmit = new Zend_Form_Element_Submit ( 'Save' );
		$oReset = new Zend_Form_Element_Reset ( 'Abort' );
		
		$oMaila->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oMailb->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oOrga->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oPhone->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oAddress->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oCity->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oCountry->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oPwa->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oPwb->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		
		$oMaila->setLabel ( 'E-mail address' );
		$oMailb->setLabel ( 'Repeat E-mail address *' );
		$oOrga->setLabel ( 'Organisation' );
		$oPhone->setLabel ( 'Phone' );
		$oAddress->setLabel ( 'Address' );
		$oCity->setLabel ( 'City' );
		$oCountry->setLabel ( 'Country' );
		$oCountry->setMultiOptions ( $aCountryList );
		$oPwa->setLabel ( 'Password' );
		$oPwb->setLabel ( 'Repeat password' );
		$oHidden->setLabel ( false );
		
		// Add Validators
		$oMaila->addValidator ( 'stringLength', false, array (7, 50 ) )->addValidator ( new Zend_Validate_EmailAddress () )->setRequired ( true )->addFilter ( 'StringToLower' );
		$oMailb->addValidator ( 'stringLength', false, array (7, 50 ) )->addValidator ( new Zend_Validate_EmailAddress () )->setRequired ( true )->addFilter ( 'StringToLower' );
		$oOrga->addValidator ( 'stringLength', false, array (0, 50 ) )->addValidator ( 'regex', false, array ('/^[a-zA-Z0-9]+/' ) );
		$oPhone->addValidator ( 'stringLength', false, array (0, 25 ) )->addValidator ( 'regex', false, array ('/^[0-9]+/' ) );
		$oCity->addValidator ( 'stringLength', false, array (0, 50 ) )->addValidator ( 'regex', false, array ('/^[a-zA-Z0-9]/' ) );
		$oPwa->addValidator ( 'StringLength', false, array (6 ) );
		$oPwb->addValidator ( 'StringLength', false, array (6 ) );
		// Assign Input elements in form
		$oRegForm->setAction ( 'account/change' )->setMethod ( 'POST' )->setAttrib ( 'id', 'registerform' );
		$oRegForm->addDecorator ( 'HtmlTag', array ('tag' => 'dl', 'class' => 'clearfix' ) );
		$oRegForm->addElement ( $oMaila );
		$oRegForm->addElement ( $oMailb );
		$oRegForm->addElement ( $oOrga );
		$oRegForm->addElement ( $oPhone );
		$oRegForm->addElement ( $oAddress );
		$oRegForm->addElement ( $oCity );
		$oRegForm->addElement ( $oCountry );
		$oRegForm->addElement ( $oPwa );
		$oRegForm->addElement ( $oPwb );
		$oRegForm->addElement ( $oHidden );
		$oRegForm->addElement ( $oSubmit );
		$oRegForm->addElement ( $oReset );
		
		$sMainMessage = '';
		
		if (count ( $_POST ) > 0) {
			if ($oRegForm->isValid ( $_POST )) {
				$sUpdateResult = $this->oAccounts->change ( $this->oAccount->userdata ['USERNAME'], $_POST );
				if ($sUpdateResult === true) {
					$sMainMessage = '<h3>Changed account</h3>';
					$bFinal = true;
				
				} else {
					$sMainMessage = $sUpdateResult;
				}
			}
		} else {
			$oMaila->setValue ( $oAccountData->mail );
			$oMailb->setValue ( $oAccountData->mail );
			$oOrga->setValue ( $oAccountData->company );
			$oPhone->setValue ( $oAccountData->phone );
			$oAddress->setValue ( $oAccountData->address );
			$oCity->setValue ( $oAccountData->city );
			$oHidden->setValue ( $oAccountData->activation );
			$oRegForm->setDefault ( 'country', $oAccountData->countries_idcountry );
		}
		
		$aBoxes = array ('LEFT' => array (), 'CONTENT' => array (), 'RIGHT' => array () );
		$aNavigations = array ();
		$oPage = new Application_Model_Page ();
		$oApstack = new Application_Model_Appstackapi ();
		$oCache = new Application_Model_Cache ( 'account_navigations' );
		if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
			$aNavigations ['HEADNAV'] = $oPage->getNavi ( 'navi_top', 'top_nav', 'apst_small' );
			$aNavigations ['MAINNAV'] = $oPage->getNavi ( 'navi_main', 'main_nav', '' );
			$aNavigations ['FOOTERNAV'] = $oPage->getNavi ( 'navi_bottom', 'footer', 'clearfix apst_small', ' | ' );
			
			$oCache->writeCache ( serialize ( $aNavigations ) );
		}
		
		$aNavigations = unserialize ( $oCache->readCache () );
		
		$oCache->setCache ( 'account_accountadmin' );
		if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
			$sAccountAdmin = $oPage->getNavi ( 'account', 'accountadmin', 'boxnavi', false, false, array ('overview' => 'account/index', 'change' => 'account/change', 'logout' => 'account/logout' ) );
			$oCache->writeCache ( $sAccountAdmin );
		}
		
		$sAccountAdmin = $oCache->readCache ();
		array_push ( $aBoxes ['LEFT'], $oPage->getBox ( $sAccountAdmin, 'l1', 'teaser_box' ) );
		array_push ( $aBoxes ['CONTENT'], $oPage->getBox ( $sMainMessage . ((! isset ( $bFinal )) ? $oRegForm->render () : ''), 'c1', 'content_box' ) );
		array_push ( $aBoxes ['RIGHT'], $oPage->getBox ( 'z', 'r1', 'teaser_box' ) );
		
		$this->view->sPage = $oPage->getPage ( 'content', $aNavigations, $aBoxes, 'landing.js' );
	}
	
	public function registerAction() {
		
		$this->oCache = new Application_Model_Cache ( md5 ( 'index/content' ) );
		
		$oCountrys = new Application_Model_Table ();
		$oCountrys->setTable ( 'countries' );
		$oCountrys->setPrimary ( 'idcountry' );
		
		$aCountryList = array ();
		foreach ( $oCountrys->fetchAll () as $aCountry ) {
			$aCountryList [$aCountry->idcountry] = $aCountry->name;
		
		}
		// Define Fields
		$oRegForm = new Zend_Form ();
		$view = $oRegForm->getView ();
		$view->doctype ( 'XHTML1_TRANSITIONAL' );
		$oUser = new Zend_Form_Element_Text ( 'user' );
		$oPwa = new Zend_Form_Element_Password ( 'pwa' );
		$oPwb = new Zend_Form_Element_Password ( 'pwb' );
		$oMaila = new Zend_Form_Element_Text ( 'maila' );
		$oMailb = new Zend_Form_Element_Text ( 'mailb' );
		$oOrga = new Zend_Form_Element_Text ( 'company' );
		$oPhone = new Zend_Form_Element_Text ( 'phone' );
		$oAddress = new Zend_Form_Element_Text ( 'address' );
		$oCity = new Zend_Form_Element_Text ( 'city' );
		$oCountry = new Zend_Form_Element_Select ( 'country' );
		$oSubmit = new Zend_Form_Element_Submit ( 'Register' );
		$oReset = new Zend_Form_Element_Reset ( 'Reset' );
		
		$oUser->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oPwa->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oPwb->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oMaila->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oMailb->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oOrga->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oPhone->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oAddress->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oCity->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		$oCountry->addDecorators ( array ('ViewHelper', 'Errors', array ('HtmlTag', array ('tag' => 'dt', 'class' => 'clearfix' ) ), array ('Label', array ('tag' => 'dd', 'class' => '' ) ) ) );
		
		$oUser->setLabel ( 'Username *' );
		$oPwa->setLabel ( 'Password *' );
		$oPwb->setLabel ( 'Repeat password *' );
		$oMaila->setLabel ( 'E-mail address *' );
		$oMailb->setLabel ( 'Repeat E-mail address *' );
		$oOrga->setLabel ( 'Organisation' );
		$oPhone->setLabel ( 'Phone' );
		$oAddress->setLabel ( 'Address' );
		$oCity->setLabel ( 'City' );
		$oCountry->setLabel ( 'Country' );
		$oCountry->setMultiOptions ( $aCountryList );
		// Add Validators
		$oUser->addValidator ( 'stringLength', false, array (6, 20 ) )->addValidator ( 'regex', false, array ('/^[a-z]+/' ) )->setRequired ( true )->addFilter ( 'StringToLower' );
		$oPwa->addValidator ( 'StringLength', false, array (6 ) )->setRequired ( true );
		$oPwb->addValidator ( 'StringLength', false, array (6 ) )->setRequired ( true );
		$oMaila->addValidator ( 'stringLength', false, array (7, 50 ) )->addValidator ( new Zend_Validate_EmailAddress () )->setRequired ( true )->addFilter ( 'StringToLower' );
		$oMailb->addValidator ( 'stringLength', false, array (7, 50 ) )->addValidator ( new Zend_Validate_EmailAddress () )->setRequired ( true )->addFilter ( 'StringToLower' );
		$oOrga->addValidator ( 'stringLength', false, array (0, 50 ) )->addValidator ( 'regex', false, array ('/^[a-zA-Z0-9]+/' ) );
		$oPhone->addValidator ( 'stringLength', false, array (0, 25 ) )->addValidator ( 'regex', false, array ('/^[0-9]+/' ) );
		$oCity->addValidator ( 'stringLength', false, array (0, 50 ) )->addValidator ( 'regex', false, array ('/^[a-zA-Z0-9]/' ) );
		
		// Assign Input elements in form
		$oRegForm->setAction ( 'account/register' )->setMethod ( 'POST' )->setAttrib ( 'id', 'registerform' );
		$oRegForm->addDecorator ( 'HtmlTag', array ('tag' => 'dl', 'class' => 'clearfix' ) );
		$oRegForm->addElement ( $oUser );
		$oRegForm->addElement ( $oPwa );
		$oRegForm->addElement ( $oPwb );
		$oRegForm->addElement ( $oMaila );
		$oRegForm->addElement ( $oMailb );
		$oRegForm->addElement ( $oOrga );
		$oRegForm->addElement ( $oPhone );
		$oRegForm->addElement ( $oAddress );
		$oRegForm->addElement ( $oCity );
		$oRegForm->addElement ( $oCountry );
		$oRegForm->addElement ( $oSubmit );
		$oRegForm->addElement ( $oReset );
		$sMainMessage = '';
		
		if (isset ( $_POST ["user"] )) {
			if ($oRegForm->isValid ( $_POST )) {
				$sMainMessage = $this->oAccounts->create ( $_POST );
				if ($sMainMessage === true) {
					$bFinal = true;
					
					$sMainMessage = '<h3>We sent a mail for confirmation.</h3>';
				
				}
			}
		}
		
		$aBoxes = array ('LEFT' => array (), 'CONTENT' => array (), 'RIGHT' => array () );
		$aNavigations = array ();
		$oPage = new Application_Model_Page ();
		$oApstack = new Application_Model_Appstackapi ();
		$oCache = new Application_Model_Cache ( 'account_navigations' );
		if ($oCache->isOlderAs ( $this->oSettings->website->cachetime )) {
			$aNavigations ['HEADNAV'] = $oPage->getNavi ( 'navi_top', 'top_nav', 'apst_small' );
			$aNavigations ['MAINNAV'] = $oPage->getNavi ( 'navi_main', 'main_nav', '' );
			$aNavigations ['FOOTERNAV'] = $oPage->getNavi ( 'navi_bottom', 'footer', 'clearfix apst_small', ' | ' );
			
			$oCache->writeCache ( serialize ( $aNavigations ) );
		}
		
		$aNavigations = unserialize ( $oCache->readCache () );
		
		array_push ( $aBoxes ['LEFT'], $oPage->getBox ( 'X', 'l1', 'teaser_box' ) );
		array_push ( $aBoxes ['CONTENT'], $oPage->getBox ( $sMainMessage . ((! isset ( $bFinal )) ? $oRegForm->render () : ''), 'c1', 'content_box' ) );
		array_push ( $aBoxes ['RIGHT'], $oPage->getBox ( 'z', 'r1', 'teaser_box' ) );
		
		$this->view->sPage = $oPage->getPage ( 'content', $aNavigations, $aBoxes, 'landing' );
	}
	
	public function lookupAction() {
		
		Preprocessor_Header::setContentType ( 'json' );
		
		$this->view->bUserExists = json_encode ( $this->oAccounts->lookup ( $_GET ['user'] ) );
	}
	
	public function activateAction() {
		$this->oAccounts->activate ( $_GET );
	}
	
	public function loginAction() {
		$aLoginResponse = $aLogin = $this->oAccounts->logIn ( $_POST );
		
		if (! $aLoginResponse ['error']) {
			header ( 'Location:' . $this->oSettings->website->site->basehref . 'account/index/' );
		} else {
			header ( 'Location:' . $this->oSettings->website->site->basehref . 'error/login/' . '?message=' . urlencode ( $aLoginResponse ['message'] ) );
		}
	}
	
	public function statusAction() {
		Preprocessor_Header::setContentType ( 'json' );
		
		$this->view->sPage = json_encode ( $this->oAccounts->status () );
	}
	
	public function nopwAction() {
		$aResetResult = $this->oAccounts->forgotPassword ( $_GET );
		if (isset ( $aResetResult ['message'] )) {
			$this->view->sPage = $aResetResult ['message'];
		} else {
			$this->view->sPage = '';
		}
	}
	
	public function resetpwAction() {
		$this->oAccounts->resetPassword ( $_GET );
		header ( 'Location:' . $this->oSettings->website->site->basehref );
	}
	
	public function logoutAction() {
		if (! isset ( $this->oAccount->userdata ['UID'] )) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		}
		$this->oAccounts->logout ();
	}

}
?>
