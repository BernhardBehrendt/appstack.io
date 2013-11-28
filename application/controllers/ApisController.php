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
class ApisController extends Zend_Controller_Action {
	private $oCache;
	private $oSettings;
	private $oAccounts;
	private $oAccount;
	private $oApis;
	public function init() {

		/* Initialize action controller here */
		$this -> oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$this -> oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}

		$this -> oAccounts = new Application_Model_Account($this -> oSettings);
		$this -> oApis = new Application_Model_Apis($this -> oSettings);

	}

	public function indexAction() {

		$oPage = new Application_Model_Page();
		$oApstack = new Application_Model_Appstackapi();
		$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
		$oCache = new Application_Model_Cache('api_navigations');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
			$oCache -> writeCache(serialize($aNavigations));
		}

		$aNavigations = unserialize($oCache -> readCache());

		$oCache -> setCache('api_accountadmin');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$sAccountAdmin = $oPage -> getNavi('apis', 'accountadmin', 'boxnavi', false, false, array('overview' => 'apis/index', 'serverkeys' => 'apis/serverkeys', 'browserkeys' => 'apis/browserkeys'));
			$oCache -> writeCache($sAccountAdmin);
		}

		$sAccountAdmin = $oCache -> readCache();

		array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));

		$aUsage = $this -> oAccounts -> getUsage($this -> oAccount -> userdata['UID']);
		$sContent = '';
		$aRate = $this -> oAccounts -> getRate($this -> oAccount -> userdata['UID']);

		foreach ($aUsage as $sColumnName => $iValue) {

			$iSum = $aRate[$sColumnName];

			if ($iSum != -1 && $iSum != 0 && $sColumnName == 'maxbrowserkeys') {
				$iUsage = floor(($iValue * 100) / $iSum);
				$iAvail = 100 - $iUsage;
				$sLabel = ucfirst(str_replace('max', '', $sColumnName));
				$sHtmlStat = '<span class="apst_pink apst_mid">' . $iValue . ' of ' . $iSum . '</span>' . '<br/>' . $sLabel;

				$sContent .= $oPage -> pieChart($sHtmlStat, array('Available' => $iAvail, 'Created' => $iUsage), 300, 0, array('0e8cf6', '1742cc', 'de057e'), '282828');
			}
			if ($iSum != -1 && $iSum != 0 && $sColumnName == 'maxserverkeys') {
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

	public function listAction() {

		$oPage = new Application_Model_Page();
		$oApstack = new Application_Model_Appstackapi();
		$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());

		echo '<pre>' . print_r($this -> oApis -> getKeys($this -> oAccount -> userdata['UID']), true) . '</pre>';
		exit ;
		//$this -> oApis -> createOAuthConsumer($this -> oAccount -> userdata['UID'], 'http://web.de', 'http://xz.de', 'bernhard.bezdek@web.de');
		array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));
		array_push($aBoxes['CONTENT'], $oPage -> getBox('dd', 'c1', 'content_box'));
		array_push($aBoxes['RIGHT'], $oPage -> getBox('xxx', 'r1', 'teaser_box'));
		$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');

	}

	public function serverkeysAction() {

		$oPage = new Application_Model_Page();
		$oApstack = new Application_Model_Appstackapi();
		$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
		$oCache = new Application_Model_Cache('apis_navigations');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
			$oCache -> writeCache(serialize($aNavigations));
		}

		$aNavigations = unserialize($oCache -> readCache());

		$oCache -> setCache('apis_accountadmin');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$sAccountAdmin = $oPage -> getNavi('apis', 'accountadmin', 'boxnavi', false, false, array('overview' => 'apis/index', 'serverkeys' => 'apis/serverkeys', 'browserkeys' => 'apis/browserkeys'));
			$oCache -> writeCache($sAccountAdmin);
		}

		$sAccountAdmin = $oCache -> readCache();

		array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));
		array_push($aBoxes['CONTENT'], $oPage -> getBox($this -> oApis -> createConsumer() . $this -> oApis -> listServerKeys(), 'c1', 'content_box'));
		array_push($aBoxes['RIGHT'], $oPage -> getBox($oPage -> getFiltes('apis/serverkeys/', $this -> oAccount -> userdata['LIMITRESULT'], ((isset($this -> oAccount -> userdata['FILTERLETTER']) ? $this -> oAccount -> userdata['FILTERLETTER'] : '*'))), 'r1', 'teaser_box'));
		$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');

	}

	public function browserkeysAction() {

		$oPage = new Application_Model_Page();
		$oApstack = new Application_Model_Appstackapi();
		$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
		$oCache = new Application_Model_Cache('apis_navigations');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
			$oCache -> writeCache(serialize($aNavigations));
		}

		$aNavigations = unserialize($oCache -> readCache());

		$oCache -> setCache('apis_accountadmin');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$sAccountAdmin = $oPage -> getNavi('apis', 'accountadmin', 'boxnavi', false, false, array('overview' => 'apis/index', 'serverkeys' => 'apis/serverkeys', 'browserkeys' => 'apis/browserkeys'));
			$oCache -> writeCache($sAccountAdmin);
		}

		$sAccountAdmin = $oCache -> readCache();

		array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));
		array_push($aBoxes['CONTENT'], $oPage -> getBox($this -> oApis -> createBrowserkey() . $this -> oApis -> listBrowserKeys(), 'c1', 'content_box'));
		array_push($aBoxes['RIGHT'], $oPage -> getBox($oPage -> getFiltes('apis/browserkeys/', $this -> oAccount -> userdata['LIMITRESULT'], ((isset($this -> oAccount -> userdata['FILTERLETTER']) ? $this -> oAccount -> userdata['FILTERLETTER'] : '*'))), 'r1', 'teaser_box'));
		$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');

	}

	public function consumercreateAction() {
		$this -> view -> response = $this -> oApis -> createConsumer($_REQUEST);
	}

	public function browserkeycreateAction() {
		$this -> view -> response = $this -> oApis -> createBrowserkey($_REQUEST);
	}

	public function browserkeydeleteAction() {
		$this -> view -> response = $this -> oApis -> deletebrowserkey(Preprocessor_String::filterBadChars($this -> getRequest() -> getParam('browserkey')));
	}

	public function keydeleteAction() {
		$this -> view -> response = $this -> oApis -> deleteAccessToken(Preprocessor_String::filterBadChars($this -> getRequest() -> getParam('key')));
	}

	public function consumerdeleteAction() {
		$this -> view -> response = $this -> oApis -> deleteConsumer(Preprocessor_String::filterBadChars($this -> getRequest() -> getParam('consumer')));
	}

}
?>